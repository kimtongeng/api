<?php

namespace App\Http\Controllers\Mobile\Modules\SocietySecurity;

use App\Enums\Types\BusinessCategoryType;
use App\Enums\Types\BusinessTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\BusinessCategory;
use Illuminate\Http\Request;

class EventTypeListAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    public function getEventTypeList(Request $request)
    {
        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $data = BusinessCategory::lists()
            ->where('business_category.business_type_id', BusinessTypeEnum::getNews())
            ->where('business_category.type', BusinessCategoryType::getEventType())
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}
