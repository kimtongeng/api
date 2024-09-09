<?php


namespace App\Http\Controllers\Mobile\Common;

use App\Models\Bank;
use App\Models\AppType;
use App\Models\Commune;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Category;
use App\Models\District;
use App\Models\Province;
use App\Models\Attribute;
use App\Models\Transaction;
use App\Models\VehicleType;
use App\Models\BusinessType;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use App\Models\AssetCategory;
use App\Models\BusinessStaff;
use App\Models\GeneralSetting;
use App\Enums\Types\AppTypeEnum;
use App\Models\BusinessCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use PhpParser\Node\Expr\Cast\Array_;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\PlaceContactType;
use App\Enums\Types\GeneralSettingKey;
use App\Enums\Types\VehicleTypeStatus;
use App\Enums\Types\BusinessTypeStatus;
use App\Enums\Types\BusinessCategoryStatus;
use App\Enums\Types\BusinessTypeHasTransaction;
use App\Models\ItemType;

class DropdownAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    //Get Province List
    public function getProvinceList(Request $request)
    {
        $filter = [
            'search' => $request->input('search')
        ];
        $data = Province::lists($filter)
        ->where('province.country_id', Country::getCountryCambodiaID())
        ->get();

        return $this->responseWithData($data);
    }

    //Get District List By Province
    public function getDistrictListByProvince(Request $request)
    {
        $this->validate($request, [
            'province_id' => 'required|exists:province,id'
        ]);
        $filter = [
            'province_id' => $request->input('province_id'),
            'search' => $request->input('search')
        ];
        $data = District::lists($filter)->get();

        return $this->responseWithData($data);
    }

    //Get Commune List By District
    public function getCommuneListByDistrict(Request $request)
    {
        $this->validate($request, [
            'district_id' => 'required|exists:district,id'
        ]);
        $filter = [
            'district_id' => $request->input('district_id'),
            'search' => $request->input('search')
        ];
        $data = Commune::lists($filter)->get();

        return $this->responseWithData($data);
    }

    //Get Property Type List
    public function getPropertyTypeList(Request $request)
    {
        $filter = [
            'search' => $request->input('search')
        ];
        $data = PropertyType::lists($filter)->get();

        return $this->responseWithData($data);
    }

    //Get App Type List
    public function getAppTypeList(Request $request)
    {
        $data = AppType::getComboList();

        return $this->responseWithData($data);
    }

    //Get All Business Type List
    public function getBusinessTypeList(Request $request)
    {
        $data = BusinessType::lists()
            ->where(BusinessType::STATUS, BusinessTypeStatus::getEnable())
            ->get();

        return $this->responseWithData($data);
    }

    //Get Business Type Has Transaction List
    public function getBusinessTypeHasTransactionList(Request $request)
    {
        $data = BusinessType::lists()
            ->where(BusinessType::ID, '!=', BusinessTypeEnum::getDelivery())
            ->where(BusinessType::HAS_TRANSACTION, BusinessTypeHasTransaction::getYes())
            ->orWhere(BusinessType::ID, BusinessTypeEnum::getAttraction())
            ->get();

        return $this->responseWithData($data);
    }

    //Get Agency List
    public function getAgencyList(Request $request)
    {
        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = [
            'search' => $request->input('search'),
            'referral_id' => $request->input('referral_id')
        ];

        $data = Contact::getAgencyList($filter)->paginate($tableSize);
        return $this->responseWithPagination($data);
    }

    //Get Bank List
    public function getBankList()
    {
        $data = Bank::lists()->get();

        return $this->responseWithData($data);
    }

    //Get Social Contact List
    public function getSocialContactList()
    {
        $data = PlaceContactType::getComboList();

        return $this->responseWithData($data);
    }

    //Get Shop Category List
    public function getShopCategoryList(Request $request)
    {
        $this->validate($request, [
            'business_type_id' => 'required|exists:business_type,id'
        ]);

        $tableSize = $request->input('table_size');
        empty($tableSize) ? $tableSize = 10 : $tableSize;

        $filter = [
            'business_type_id' => $request->input('business_type_id'),
            'search' => $request->input('search'),
            'name_by_key' => $request->input('name_by_key')
        ];

        $data = BusinessCategory::lists($filter)
        ->where('business_category.status', BusinessCategoryStatus::getEnabled())
        ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    //Get Product Category By ID
    public function getProductCategoryByID(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:category,id'
        ]);


        $data = Category::find($request->input('id'));

        return $this->responseWithData($data);
    }

    // Get Attribute List
    public function getAttributeList(Request $request)
    {
        $this->validate($request, [
            'attribute_group_id' => 'required|exists:attribute_group,id',
        ]);

        $filter = [
            'attribute_group_id' => $request->input('attribute_group_id'),
        ];

        $data = Attribute::lists($filter)->get();

        return $this->responseWithData($data);
    }

    //Get Massager List
    public function getMassageTherapistList(Request $request)
    {
        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = [
            'search' => $request->input('search'),
            'business_id' => $request->input('business_id')
        ];

        $businessStaff = BusinessStaff::listMassageTherapist($filter)->pluck('contact_id');

        $data = Contact::getMassagerList($filter,$businessStaff)->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    //Change Active In Customer Sale list
    public function changeActiveCustomerSaleList(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:transaction,id',
            'active' => 'required'
        ]);

        $transaction = Transaction::find($request->input('id'));
        $transaction->active = $request->input('active');
        if($transaction->save()) {
            return $this->responseWithSuccess();
        }
    }

    //Get Recipient List
    public function getRecipientList(Request $request)
    {
        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = [
            'search' => $request->input('search'),
        ];

        $data = Contact::getRecipientList($filter)->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    //Get Recipient By Current User ID
    public function getRecipientDetail(Request $request)
    {
        $filter = [
            'current_user_id' => $request->input('current_user_id'),
        ];

        $data = Contact::getRecipientList($filter)->first();

        return $this->responseWithData($data);
    }

    //Get Ktv Girl List
    public function getKtvGirlList(Request $request)
    {
        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = [
            'search' => $request->input('search'),
            'business_id' => $request->input('business_id')
        ];
        $businessStaff = BusinessStaff::listsKTVGirl($filter)
            ->pluck('contact_id');

        $data = Contact::getKtvGirlList($filter, $businessStaff)->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    // Get API Version
    public function getAPIVersionValue(Request $request)
    {
        $data = GeneralSetting::select(
            DB::raw("JSON_UNQUOTE(JSON_EXTRACT(setting.value, '$.version')) as version"),
            DB::raw("JSON_UNQUOTE(JSON_EXTRACT(setting.value, '$.min_version')) as min_version")
        )->where(GeneralSetting::KEY, GeneralSettingKey::getAPIVersion())->first();

        return $this->responseWithData($data);
    }

    //Get Charity Category List
    public function getCharityCategoryList(Request $request)
    {
        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = [
            'search' => $request->input('search'),
        ];

        $data = BusinessCategory::lists($filter)->where('business_category.business_type_id', BusinessTypeEnum::getCharityOrganization())
        ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    //Get Vehicle Type List
    public function getVehicleTypeList(Request $request)
    {
        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = [
            'search' => $request->input('search'),
        ];

        $data = VehicleType::lists($filter)
            ->where('vehicle_type.status', VehicleTypeStatus::getEnabled())
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    //Get Contact Detail Chat
    public function getContactListDetailChat(Request $request)
    {
        $this->validate($request, [
            'contact_list' => 'required',
            'contact_list.*.contact_id' => 'required|exists:contact,id'
        ]);

        if (!empty($request->input('contact_list'))) {
            foreach ($request->input('contact_list') as $obj) {
                $data[] = Contact::where('contact.id', $obj['contact_id'])
                        ->select(
                            'contact.id as contact_id',
                            'contact.fullname as contact_name',
                            'contact.profile_image as contact_image',
                        )
                        ->first();
            }
        }

        return $this->responseWithData($data);
    }

    //Get Driver List
    public function getDriverList(Request $request)
    {
        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = [
            'search' => $request->input('search'),
        ];

        $data = Contact::getDriverList($filter)->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    //Get Item Type List
    public function getItemTypeList(Request $request)
    {
        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = [
            'search' => $request->input('search'),
        ];

        $data = ItemType::lists($filter)->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}
