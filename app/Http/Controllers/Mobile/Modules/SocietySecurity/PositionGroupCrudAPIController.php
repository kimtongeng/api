<?php

namespace App\Http\Controllers\Mobile\Modules\SocietySecurity;

use App\Enums\Types\BusinessCategoryStatus;
use App\Enums\Types\BusinessCategoryType;
use App\Enums\Types\BusinessTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\BusinessCategory;
use Illuminate\Http\Request;

class PositionGroupCrudAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    //Add Position Group
    public function addPosition(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $position = new BusinessCategory();
        $position->setData($request);
        $position->{BusinessCategory::BUSINESS_TYPE_ID} = BusinessTypeEnum::getNews();
        $position->{BusinessCategory::TYPE} = BusinessCategoryType::getPositionGroup();
        $position->{BusinessCategory::STATUS} = BusinessCategoryStatus::getEnabled();

        if ($position->save()) {
            return $this->responseWithData($position);
        }
    }

    //List Position Group
    public function getPositionList(Request $request)
    {
        $data = BusinessCategory::select(
            'id',
            'business_type_id',
            'name',
        )
        ->where('business_category.business_type_id', BusinessTypeEnum::getNews())
        ->where('business_category.type', BusinessCategoryType::getPositionGroup())
        ->where('business_category.status', BusinessCategoryStatus::getEnabled())
        ->get();

        return $this->responseWithData($data);
    }
}
