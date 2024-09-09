<?php

namespace App\Http\Controllers\Mobile\Modules\KTV\Profile;

use App\Enums\Types\ProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class KTVProductListAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    //List Product
    public function getKTVProductFilterSort(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_id' => 'required|exists:business,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = $request->input('filter');
        $sort = 'newest';

        $data = Product::listsKTV($filter, $sort)
            ->where('product.status',ProductStatus::getEnabled())
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}
