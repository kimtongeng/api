<?php

namespace App\Http\Controllers\Admin\Modules\Business\SocietySecurity;

use App\Models\Contact;
use App\Models\Country;
use App\Models\Province;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\BusinessCategory;
use App\Http\Controllers\Controller;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\BusinessCategoryType;

class NewsRecipientListController extends Controller
{
    const MODULE_KEY = 'news_recipient';

    public function get(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getViewPermission())) {
            $data = $this->getList(
                $request->input('table_size'),
                $request->input('filter'),
                $request->input('sort_by'),
                $request->input('sort_type')
            );
            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    private function getList($tableSize, $filter, $sortBy = '', $sortType = '')
    {
        if (empty($tableSize)) {
            $tableSize = 10;
        }

        $data = Contact::getRecipientList($filter, $sortBy, $sortType)
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

    public function getSelectData(Request $request)
    {

        $position_group = BusinessCategory::listsAdmin(['business_type_id' => BusinessTypeEnum::getNews()])
            ->where('business_category.type', BusinessCategoryType::getPositionGroup())
            ->get();
        $province = Province::lists()
            ->where('province.country_id', Country::getCountryCambodiaID())
            ->get();

        $response = [
            'position_group' => $position_group,
            'province' => $province
        ];
        return $this->responseWithData($response);
    }
}
