<?php

namespace App\Http\Controllers\Admin\Modules\Notification;

use App\Enums\Types\ContactBroadcastType;
use App\Helpers\StringHelper;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Notification;
use App\Models\Permission;
use App\Models\PropertyType;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationBroadcastController extends Controller
{
    const MODULE_KEY = 'broadcast';

    /**
     * Get Broadcast
     */
    public function get(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getViewPermission())) {
            $data = $this->getList($request->input('table_size'), $request->input('filter'));
            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    private function getList($tableSize, $filter)
    {
        $tableSize = empty($tableSize) ? 10 : $tableSize;

        $data = Notification::listForAdmin($filter)
            ->whereNotNull('notification.contact_noti_type')
            ->where('notification.contact_broadcast_type', ContactBroadcastType::getAdvertise())
            ->paginate($tableSize);

        $response = [
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => intval($data->firstItem()),
                'to' => intval($data->lastItem())
            ],
            'data' => $data->items(),
        ];
        return $response;
    }

    /**
     * Get Select Data For Form Add, Update
     */
    public function getSelectData(Request $request)
    {
        $data = [
            'property' => Business::listProperty(['is_admin_request' => true])->get(),
            'property_type' => PropertyType::lists()->get(),
        ];

        return $this->responseWithData($data);
    }

    /**
     * Check Validation Product
     *
     */
    private function checkValidation($data)
    {
        $messages = [
            'contact_noti_type.required' => 'Type is required.'
        ];
        $this->validate($data, [
            'id' => empty($data['id']) ? 'nullable' : 'required',
            'title' => 'required',
            'description' => 'required',
            'contact_noti_type' => 'required'
        ], $messages);
    }


    /**
     * Store Or Update
     */
    public function storeOrUpdate(Request $request)
    {
        //Check Action Permission
        $action = $request->input('action');
        if ($action == 'add') {
            if (Permission::authorize(self::MODULE_KEY, Permission::getCreatePermission()) == false) {
                return $this->responseNoPermission();
            }
        } else if ($action == 'update') {
            if (Permission::authorize(self::MODULE_KEY, Permission::getUpdatePermission()) == false) {
                return $this->responseNoPermission();
            }
        }
        $this->save($request);
        return $this->responseWithSuccess();
    }

    private function save($request)
    {
        $this->checkValidation($request);

        $businessType = null;
        $contactNotiType = $request->input('contact_noti_type');
        $notiSendType = 0;
        $notiSendType = Notification::getNotiTypeByContactNotiType($contactNotiType);
        $isSend = $request->input('is_send');
        $action = $request->input('action');

        $sendResponse = Notification::sendBroadcastFromAdmin(
            $businessType,
            $contactNotiType,
            $notiSendType,
            $request,
            $isSend,
            $action
        );

        info('Notification From Admin => Broadcast: '. $sendResponse);
    }


    /**
     * Get Edit Data For Form Update
     */
    public function getEditData(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getUpdatePermission())) {
            $this->validate($request, [
                'id' => 'required|exists:notification,id'
            ]);

            $filter['notification_id'] = $request->input('id');

            $data = Notification::listForAdmin($filter)
                ->whereNotNull('notification.contact_noti_type')
                ->where('notification.contact_broadcast_type', ContactBroadcastType::getAdvertise())
                ->first();

            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    /**
     * Delete Broadcast
     */
    public function delete(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getDeletePermission())) {
            $this->validate($request, [
                'id' => 'required|exists:notification,id'
            ]);

            DB::beginTransaction();

            $notification = Notification::find($request['id']);

            //Delete Thumbnail
            StringHelper::deleteImage($notification->image);

            // Set Log
            $description = 'Id : ' . $notification->id . ', Title : ' . $notification->title;
            UserLog::setLog(self::MODULE_KEY, Permission::getDeletePermission(), $description);

            $notification->delete();

            DB::commit();

            $data = $this->getList($request->input('table_size'), $request->input('filter'));
            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    /**
     * Resend
     */
    public function resend(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, 'send')) {
            $this->validate($request, [
                'notification_id' => 'required|exists:notification,id'
            ]);

            $notification = Notification::find($request['notification_id']);

            if (!empty($notification)) {
                $notification->notification_id = $notification->id;
                $notification->old_image = $notification->image;
                $appType = null;
                $contactNotiType = $notification->contact_noti_type;
                $notiSendType = Notification::getNotiTypeByContactNotiType($contactNotiType);
                $isSend = true;
                $action = 'update';

                $sendResponse = Notification::sendBroadcastFromAdmin(
                    $appType,
                    $contactNotiType,
                    $notiSendType,
                    $notification,
                    $isSend,
                    $action
                );
                info('Notification From Admin => Broadcast: '. $sendResponse);
            }

            $data = $this->getList($request->input('table_size'), $request->input('filter'));
            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }
}
