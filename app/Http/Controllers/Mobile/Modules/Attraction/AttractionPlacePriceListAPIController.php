<?php

namespace App\Http\Controllers\Mobile\Modules\Attraction;

use App\Enums\Types\PlacePriceStatus;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AttractionPlacePriceListAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    //Get Place Price List
    public function getPlacePriceFilterSort(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_id' => 'required|exists:business,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = $request->input('filter');

        $data = Category::listAttractionCategory($filter)
            ->join('place_price_list', 'category.id', '=', 'place_price_list.category_id')
            ->with([
                'PlacePriceList' => function ($query) {
                    $query->join('category', 'category.id', '=', 'place_price_list.category_id')
                    ->where('place_price_list.option', '=', PlacePriceStatus::getForSale())
                    ->select(
                        'place_price_list.id',
                        'place_price_list.business_id',
                        'place_price_list.category_id',
                        'category.name as category_name',
                        'place_price_list.name',
                        'place_price_list.image',
                        'place_price_list.price',
                        'place_price_list.discount_amount',
                        'place_price_list.discount_type',
                        'place_price_list.sell_price',
                        'place_price_list.description',
                        'place_price_list.option'
                    )
                        ->orderBy('place_price_list.id', 'DESC')
                        ->groupBy('place_price_list.id')
                        ->get();
                }
            ])
            ->orderBy('category.id', 'DESC')
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}
