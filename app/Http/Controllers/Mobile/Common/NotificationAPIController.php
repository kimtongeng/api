<?php


namespace App\Http\Controllers\Mobile\Common;

use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\ContactBroadcastType;
use App\Enums\Types\ContactNotificationType;
use App\Enums\Types\NewsVisitorsStatus;
use App\Enums\Types\NotificationReadType;
use App\Enums\Types\NotificationSendToPlatform;
use App\Enums\Types\NotificationViewContactType;
use App\Helpers\Utils\ErrorCode;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Notification;
use App\Models\NotificationView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Get Notification List
     */
    public function getNotificationList(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.current_user_id' => 'required|exists:contact,id'
        ]);

        $tableSize = $request->input('table_size');
        empty($tableSize) ? $tableSize = 10 : $tableSize;

        $filter = $request->input('filter');

        $data = $this->notificationList($filter)->paginate($tableSize);

        //Count Not Read Notification
        $notReadCount = 0;
        $countData = $this->notificationList($filter)->get();
        foreach ($countData as $obj) {
            if ($obj['status'] == NotificationReadType::getNotRead()) {
                $notReadCount += 1;
            }
        }

        $response = [
            'pagination' => [
                'total' => $data->total(),
                'per_page' => (int)$data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem()
            ],
            'not_read_count' => $notReadCount,
            'data' => $data->items()
        ];
        return $this->responseWithData($response);
    }

    /**
     * Get Notification Detail
     */
    public function getNotificationDetail(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.notification_id' => 'required|exists:notification,id'
        ]);

        $filter = $request->input('filter');

        $data = $this->notificationList($filter)->first();

        return $this->responseWithData($data);
    }

    private function notificationList($filter = [])
    {
        $notificationID = isset($filter['notification_id']) ? $filter['notification_id'] : null;
        $contactID = isset($filter['current_user_id']) ? $filter['current_user_id'] : null;
        $businessTypeID = isset($filter['business_type_id']) ? $filter['business_type_id'] : null;

        return Notification::leftjoin('business_type', 'business_type.id', 'notification.business_type')
            ->leftjoin('notification_view', function ($join) use ($contactID) {
                $join->on('notification_view.notification_id', 'notification.id')
                    ->where('notification_view.contact_id', $contactID)
                    ->where('notification_view.contact_type', NotificationViewContactType::getContact());
            })
            ->leftjoin('notification_contact', 'notification_contact.notification_id', 'notification.id')
            ->leftjoin('business_comment', 'business_comment.business_id', 'notification.reference_id')
            ->where(function ($query) use ($contactID) {
                $query->where(function ($q) use ($contactID) {
                    $q->where('notification.contact_broadcast_type', ContactBroadcastType::getSelf())
                        ->where('notification.contact_id', $contactID);
                })
                ->OrWhere(function ($q) use ($contactID) {
                    $q->where('notification.contact_broadcast_type', ContactBroadcastType::getSelf())
                        ->whereNull('notification.contact_id')
                        ->where('notification_contact.contact_id', $contactID);
                })
                ->OrWhere(function ($q) use ($contactID) {
                    $q->where('notification.contact_broadcast_type', ContactBroadcastType::getSelf())
                        ->whereNull('notification.contact_id')
                        ->where('business_comment.contact_id', $contactID);
                })
                ->OrWhere('notification.contact_broadcast_type', ContactBroadcastType::getAdvertise());
            })
            ->when($notificationID, function ($query) use ($notificationID) {
                $query->where('notification.id', $notificationID);
            })
            ->when($businessTypeID, function ($query) use ($businessTypeID) {
                $query->where('notification.business_type', $businessTypeID);
            })
            ->whereNotNull('notification.contact_noti_type')
            ->whereNull('notification.deleted_at')
            ->select(
                'notification.id',
                'notification.title',
                'notification.description',
                'notification.image',
                'notification.contact_noti_type as noti_type',
                'notification.reference_id',
                'notification.business_type as business_type_id',
                'business_type.name as business_type_name',
                'business_type.image as business_type_image',
                DB::raw("CASE WHEN notification_view.id IS NULL THEN 0 ELSE 1 END status"),
                'notification.created_at',
            )
            ->orderBy('notification.created_at', 'desc')
            ->groupBy('notification.id');
    }


    /**
     * Set Notification Read
     */
    public function setNotificationRead(Request $request)
    {
        $this->validate($request, [
            'current_user_id' => 'required|exists:contact,id',
            'notification_id' => 'required|exists:notification,id'
        ]);

        $request->merge([
            NotificationView::CONTACT_ID => $request->input('current_user_id'),
            NotificationView::CONTACT_TYPE => NotificationViewContactType::getContact()
        ]);

        $notificationViewCount = NotificationView::where(NotificationView::CONTACT_ID, $request->input(NotificationView::CONTACT_ID))
            ->where(NotificationView::NOTIFICATION_ID, $request->input(NotificationView::NOTIFICATION_ID))
            ->where(NotificationView::CONTACT_TYPE, $request->input(NotificationView::CONTACT_TYPE))
            ->get();

        if (count($notificationViewCount) > 0) {
            return response()->json([
                'message' => 'This has already been read.'
            ], 422);
        } else {
            $notification_view = new NotificationView();
            $notification_view->setData($request);

            if ($notification_view->save()) {
                return $this->responseWithSuccess();
            } else {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        }
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllNotificationsAsRead(Request $request)
    {
        $this->validate($request, [
            'current_user_id' => 'required|exists:contact,id',
        ]);

        $notificationList = $this->notificationList(['current_user_id' => $request->input('current_user_id')])
            ->where('status', 0)
            ->get();

        foreach ($notificationList as $obj) {
            // Check if the notification has already been read by the user
            $notificationViewCount = NotificationView::where('notification_id', $obj->id)
                ->where('contact_id', $request->input('current_user_id'))
                ->get();

            // If the notification has not been read, mark it as read'
            if(count($notificationViewCount) > 0) {
                return response()->json([
                    'message' => 'All massage has already been read.'
                ], 422);
            } else {
                $notification_view_data = [
                    NotificationView::CONTACT_ID => $request->input('current_user_id'),
                    NotificationView::CONTACT_TYPE => NotificationViewContactType::getContact(),
                    NotificationView::NOTIFICATION_ID => $obj['id'],
                ];
                $notification_view = new NotificationView();
                $notification_view->setData($notification_view_data);

                if ($notification_view->save()) {
                    return $this->responseWithSuccess();
                } else {
                    return $this->responseValidation(ErrorCode::ACTION_FAILED);
                }
            }
        }
    }

    /**
     * Alert Notification When Chat
     */
    public function sendChatNotificationToAdmin(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:contact,id',
            'text' => 'required'
        ]);

        $contact = Contact::find($request->input('id'));

        if (!empty($contact)) {
            $notificationFrom = $contact->phone;

            if (!empty($contact->fullname)) {
                $notificationFrom = $contact->fullname;
            }

            $titleMessage = 'Chat from ' . $notificationFrom;
            $descriptionMessage = $request->input('text');
            $platForm = NotificationSendToPlatform::getWeb();

            $send = Notification::sendNotificationWhenChat(
                "/topics/" . env('TOPIC_MAIN_ADMIN'),
                $titleMessage,
                $descriptionMessage,
                $platForm
            );
            info('Chat From App: ' . $send);
        }

        return $this->responseWithSuccess();
    }
}
