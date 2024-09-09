<?php

namespace App\Http\Controllers\Mobile\Modules\Massage\Profile;

use App\Enums\Types\ProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class MassageServiceListAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    public function getMassageServiceFilterSort(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_id' => 'required|exists:business,id',
        ]);

        $tableSize = $request->input('table_size') ? 10 : $request->input('table_size');
        $filter = $request->input('filter');
        $sort = $request->input('sort');

        $data = Product::listMassageService($filter, $sort)
        ->where('product.status', '=', ProductStatus::getEnabled())
        ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}
