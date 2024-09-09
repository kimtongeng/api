<?php

namespace App\Http\Controllers\Admin\Modules\Notification;

use App\Enums\Types\NotificationViewContactType;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationView;
use App\Models\Permission;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationAdminListController extends Controller
{
    const MODULE_KEY = 'admin_notification_list';

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
            ->whereNotNull('notification.admin_noti_type')
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

    //Set notification readed
    public function setReaded(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:notification,id'
        ]);

        $request->merge([
            'contact_id' => Auth::guard('admin')->user()->id,
            'contact_type' => NotificationViewContactType::getAdmin(),
            'notification_id' => $request->input('id')
        ]);

        $notificationViewCount = NotificationView::where('contact_id', $request->input('contact_id'))
            ->where('notification_id', $request->input('notification_id'))
            ->where('contact_type', $request->input('contact_type'))
            ->get();

        if (count($notificationViewCount) == 0) {
            $notification_view = new NotificationView();
            $notification_view->setData($request);
            $notification_view->save();
        }

        return $this->responseWithSuccess();
    }

    //Get Notification TopNav Bar
    public function getBadgeData()
    {
        $data = Notification::notificationBadgeData();

        return $this->responseWithData($data);
    }

    //Get Transaction Detail
    public function getTransactionDetail(Request $request){
        $this->validate($request, [
            'transaction_id' => 'required|exists:transaction,id'
        ]);

        $data = Transaction::find($request->input('transaction_id'));

        return $this->responseWithData($data);
    }
}
