<?php

namespace App\Http\Controllers\Mobile\Modules\ExpressDelivery;

use Carbon\Carbon;
use App\Models\PrefixCode;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Models\DeliveryOrder;
use App\Helpers\Utils\ImagePath;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Enums\Types\ExpressDeliveryStatus;
use App\Helpers\Utils\ErrorCode;
use App\Models\DeliveryDropLocation;

class BookingDeliveryAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    private function checkValidation($data)
    {
        $this->validate($data, [
            'id' => !empty($data['id']) ? 'required|exists:delivery_order,id' : 'null',
            'customer_id' => 'required|exists:contact,id', // Customer _Id
            'vehicle_type_id' => 'required|exists:vehicle_type,id',
            'document_type_id' => 'required|exists:item_type,id',
            'image' => 'required',
            'sender_location_link' => 'required',
            'sender_name' => 'required',
            'sender_phone' => 'required',
            'sender_note' => 'nullable',
            'payer' => 'required',
            'payment_method' => 'required',
            'payment_status' => 'required',
            'total_duration' => 'required',
            'total_distance' => 'required',
            'total_amount' => 'required',
            'cancel_reason' => 'nullable',
            //delivery_drop_location
            'delivery_drop_location' => 'required',
            'delivery_drop_location.*.recipient_location_link' => 'required',
            'delivery_drop_location.*.recipient_name' => 'required',
            'delivery_drop_location.*.recipient_phone' => 'required',
            'delivery_drop_location.*.recipient_note' => 'nullable',
            'delivery_drop_location.*.duration' => 'required',
            'delivery_drop_location.*.distance' => 'required',
            'delivery_drop_location.*.price' => 'required',
            //deleted_delivery_drop_location
            'deleted_delivery_drop_location' => 'nullable',
            'deleted_delivery_drop_location' => !empty($data['deleted_delivery_drop_location']) ? 'required|exists:delivery_drop_location,id' : 'null',
        ]);
    }

    public function bookingDelivery(Request $request)
    {
        // Check Validation
        $this->checkValidation($request);

        DB::beginTransaction();

        $request->merge([
            DeliveryOrder::CONTACT_ID => $request->input('customer_id'),
            DeliveryOrder::ORDER_CODE => PrefixCode::getAutoCodeDeliveryByCustomer(DeliveryOrder::TABLE_NAME, PrefixCode::DELIVERY, $request->input('customer_id')),
        ]);

        $delivery_order = new DeliveryOrder();
        $delivery_order->setData($request);
        $delivery_order->{DeliveryOrder::STATUS} = ExpressDeliveryStatus::getPending();
        $delivery_order->{DeliveryOrder::CREATED_AT} = Carbon::now();

        if ($delivery_order->save()) {
            //Upload Transaction Image
            $image = StringHelper::uploadImage($request->input(DeliveryOrder::IMAGE), ImagePath::itemGallery);
            $delivery_order->{DeliveryOrder::IMAGE} = $image;
            $delivery_order->save();

            $delivery_drop_location_array = [];
            //Set Delivery drop Location
            if (!empty($request->input('delivery_drop_location'))) {
                foreach ($request->input('delivery_drop_location') as $key => $obj) {
                    $delivery_drop_location_data = [
                        DeliveryDropLocation::DELIVERY_ORDER_ID => $delivery_order->{DeliveryOrder::ID},
                        DeliveryDropLocation::DROP_ORDER_NO => $key + 1,
                        DeliveryDropLocation::RECIPIENT_LOCATION_LINK => $obj['recipient_location_link'],
                        DeliveryDropLocation::RECIPIENT_NAME => $obj['recipient_name'],
                        DeliveryDropLocation::RECIPIENT_PHONE => $obj['recipient_phone'],
                        DeliveryDropLocation::RECIPIENT_NOTE => $obj['recipient_note'],
                        DeliveryDropLocation::DURATION => $obj['duration'],
                        DeliveryDropLocation::DISTANCE => $obj['distance'],
                        DeliveryDropLocation::PRICE => $obj['price'],
                    ];
                    $delivery_drop_location = new DeliveryDropLocation();
                    $delivery_drop_location->setData($delivery_drop_location_data);
                    $delivery_drop_location->{DeliveryDropLocation::CREATED_AT} = Carbon::now();
                    $delivery_drop_location->save();
                    $delivery_drop_location_array[] = $delivery_drop_location;
                }
            }

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            DB::rollBack();
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }
}
