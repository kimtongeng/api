<?php

namespace App\Http\Controllers\Mobile\Modules\Attraction;

use Carbon\Carbon;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Models\PlacePriceList;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AttractionPlacePriceCrudAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    //check validation
    private function checkValidation($data)
    {
        $this->validate($data, [
            'id' => !empty($data['id']) ? 'required|exists:place_price_list,id' : 'nullable',
            'business_id' => 'required|exists:business,id',
            'category_id' => 'required|exists:category,id',
            'name' => 'required',
            'image' => 'nullable',
            'price' => 'required',
            'option' => 'required',
            'description' => 'nullable',
        ]);
    }

    //add Place Price
    public function addPlacePrice(Request $request)
    {
        //check validation
        $this->checkValidation($request);

        DB::beginTransaction();

        $place_price = new PlacePriceList();

        $place_price->setData($request);
        $place_price->created_at = Carbon::now();

        if ($place_price->save()) {
            //Upload Thumbnail
            if (!empty($request->input(PlacePriceList::IMAGE))) {
                $image = StringHelper::uploadImage($request->input(PlacePriceList::IMAGE), ImagePath::attractionPlaceList);
                $place_price->{PlacePriceList::IMAGE} = $image;
                $place_price->save();
            }

            //Set Discount
            $discountAmount = floatval($request->input('discount_amount'));
            $discountType = 0;
            if (!empty($discountAmount)) {
                if (
                    $discountAmount > 0
                ) {
                    $discountType = $request->input('discount_type');
                }
            }
            $place_price->discount_amount = $discountAmount;
            $place_price->discount_type = $discountType;
            $grand_total = $place_price->getSellPrice();
            $place_price->sell_price = $grand_total;
            $place_price->save();
        }

        DB::commit();

        return $this->responseWithData($place_price);
    }

    //edit PLace
    public function editPlacePrice(Request $request)
    {
        //check validation
        $this->checkValidation($request);

        DB::beginTransaction();

        $place_price = PlacePriceList::find($request->input(PlacePriceList::ID));

        if(!empty($place_price)) {
            $place_price->setData($request);
            $place_price->updated_at = Carbon::now();

            if($place_price->save()) {
                //Update Thumbnail
                $image = StringHelper::editImage(
                    $request->input(PlacePriceList::IMAGE),
                    $request->input('old_image'),
                    ImagePath::attractionPlaceList
                );
                $place_price->{PlacePriceList::IMAGE} = $image;
                $place_price->save();


                //Set Discount
                $discountAmount = floatval($request->input('discount_amount'));
                $discountType = 0;
                if (!empty($discountAmount)) {
                    if (
                        $discountAmount > 0
                    ) {
                        $discountType = $request->input('discount_type');
                    }
                }
                $place_price->discount_amount = $discountAmount;
                $place_price->discount_type = $discountType;
                $grand_total = $place_price->getSellPrice();
                $place_price->sell_price = $grand_total;
                $place_price->save();
            }

            DB::commit();

            return $this->responseWithData($place_price);
        } else {
            return $this->responseValidation(ErrorCode::INVALID);
        }
    }

    //delete PLace Price
    public function deletePlacePrice(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:place_price_list,id',
        ]);

        DB::beginTransaction();

        $place_price = PlacePriceList::find($request->input(PlacePriceList::ID));

        //Delete Thumbnail
        StringHelper::deleteImage($place_price->{PlacePriceList::IMAGE}, ImagePath::attractionPlaceList);

        $place_price->delete();

        DB::commit();
        return $this->responseWithSuccess();
    }

    //Get Place Price List
    public function getPlacePriceList(Request $request)
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
                    $query->join('category', 'category.id','=', 'place_price_list.category_id')
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
