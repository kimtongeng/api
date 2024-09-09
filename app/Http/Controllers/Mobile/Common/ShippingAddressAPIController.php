<?php


namespace App\Http\Controllers\Mobile\Common;

use App\Enums\Types\ShippingAddressIsDefault;
use App\Helpers\Utils\ErrorCode;
use App\Http\Controllers\Controller;
use App\Models\ShippingAddress;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShippingAddressAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Add Shipping Address
     */
    public function addShippingAddress(Request $request)
    {
        $this->validate($request, [
            'contact_id' => 'required|exists:contact,id',
            'address' => 'required',
            'phone' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        //check shipping address have or not
        $checkIsDefault = ShippingAddress::where(ShippingAddress::CONTACT_ID, $request->input(ShippingAddress::CONTACT_ID))
            ->whereNull(ShippingAddress::DELETED_AT)
            ->get();
        if (empty($checkIsDefault)) {
            $is_default = ShippingAddressIsDefault::getYes();
        } else {
            $is_default = ShippingAddressIsDefault::getNo();
        }
        $request->merge([ShippingAddress::IS_DEFAULT => $is_default]);

        //store shipping address
        $shipping_address = new ShippingAddress();

        $shipping_address->setData($request);

        if ($shipping_address->save()) {
            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Get Shipping Address
     */
    public function getShippingAddress(Request $request)
    {
        $this->validate($request, [
            'contact_id' => 'required|exists:contact,id'
        ]);

        $data = ShippingAddress::lists()
            ->where(ShippingAddress::CONTACT_ID, $request->input(ShippingAddress::CONTACT_ID))
            ->orderBy(ShippingAddress::ID, 'desc')
            ->get();

        return $this->responseWithData($data);
    }

    /**
     * Update Shipping Address
     */
    public function updateShippingAddress(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:shipping_address,id',
            'contact_id' => 'required|exists:contact,id',
            'address' => 'required',
            'phone' => 'required',
            'latitude' => 'required',
            'longitude' => 'required'
        ]);

        $shipping_address = ShippingAddress::where(ShippingAddress::ID, $request->input(ShippingAddress::ID))
            ->where(ShippingAddress::CONTACT_ID, $request->input(ShippingAddress::CONTACT_ID))
            ->first();

        if (!empty($shipping_address)) {

            $request->merge([ShippingAddress::IS_DEFAULT => $shipping_address->{ShippingAddress::IS_DEFAULT}]);

            $shipping_address->setData($request);

            if ($shipping_address->save()) {
                return $this->responseWithSuccess();
            } else {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else {
            return $this->responseValidation(ErrorCode::INVALID);
        }
    }

    /**
     * Remove Shipping Address
     */
    public function removeShippingAddress(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:shipping_address,id',
            'contact_id' => 'required|exists:contact,id'
        ]);

        $shipping_address = ShippingAddress::where(ShippingAddress::ID, $request->input(ShippingAddress::ID))
            ->where(ShippingAddress::CONTACT_ID, $request->input(ShippingAddress::CONTACT_ID));

        if ($shipping_address->delete()) {
            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Change Default Shipping Address
     */
    public function changeDefaultShippingAddress(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:shipping_address,id',
            'contact_id' => 'required|exists:contact,id'
        ]);

        //Update is default all current contact to no
        $allShippingAddressByContact = ShippingAddress::where(ShippingAddress::CONTACT_ID, $request->input(ShippingAddress::CONTACT_ID))->get();
        if (!empty($allShippingAddressByContact)) {
            DB::table(ShippingAddress::TABLE_NAME)
                ->where(ShippingAddress::CONTACT_ID, $request->input(ShippingAddress::CONTACT_ID))
                ->update([
                    ShippingAddress::IS_DEFAULT => ShippingAddressIsDefault::getNo(),
                    ShippingAddress::UPDATED_AT => Carbon::now()
                ]);
        }

        //Update is default current contact to yes
        $currentShippingAddress = ShippingAddress::where(ShippingAddress::ID, $request->input(ShippingAddress::ID))
            ->where(ShippingAddress::CONTACT_ID, $request->input(ShippingAddress::CONTACT_ID))
            ->first();
        if (!empty($currentShippingAddress)) {
            $shipping_address = DB::table(ShippingAddress::TABLE_NAME)
                ->where(ShippingAddress::ID, $request->input(ShippingAddress::ID))
                ->where(ShippingAddress::CONTACT_ID, $request->input(ShippingAddress::CONTACT_ID))
                ->whereNull(ShippingAddress::DELETED_AT)
                ->update([
                    ShippingAddress::IS_DEFAULT => ShippingAddressIsDefault::getYes(),
                    ShippingAddress::UPDATED_AT => Carbon::now()
                ]);
            if ($shipping_address) {
                return $this->responseWithSuccess();
            } else {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else {
            return $this->responseValidation(ErrorCode::INVALID);
        }
    }
}
