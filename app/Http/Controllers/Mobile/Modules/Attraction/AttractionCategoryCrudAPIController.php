<?php

namespace App\Http\Controllers\Mobile\Modules\Attraction;

use Carbon\Carbon;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Helpers\Utils\ErrorCode;
use App\Http\Controllers\Controller;

class AttractionCategoryCrudAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }


    //add category
    public function addAttractionCategory(Request $request)
    {
        $this->validate($request, [
            'business_id' => 'required',
            'name' => 'required',
        ]);

        $category = new Category();
        $category->setData($request);
        $category->created_at = Carbon::now();
        $category->save();

        if($category->save()) {
            $response = [
                'id' => $category->id,
                'business_id' => $category->business_id,
                'name' => $category->name,
            ];
            return $this->responseWithData($response);
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    //edit category
    public function editAttractionCategory(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:category,id',
            'business_id' => 'required',
            'name' => 'required',
        ]);

        $category = Category::find($request->input(Category::ID));

        if(!empty($category)) {
            $category->setData($request);
            $category->updated_at = Carbon::now();

            if ($category->save()) {
                $response = [
                    'id' => $category->id,
                    'business_id' => $category->business_id,
                    'name' => $category->name,
                ];
                return $this->responseWithData($response);
            } else {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else {
            return $this->responseValidation(ErrorCode::INVALID);
        }
    }

    //delete category
    public function deleteAttractionCategory(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:category,id'
        ]);

        $category = Category::find($request->input(Category::ID));
        $category->delete();

        return $this->responseWithSuccess();
    }

    //get list category
    public function getMyAttractionCategory(Request $request)
    {
        $this->validate($request, [
            'business_id' => 'required',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = [
            'business_id' => $request->input('business_id'),
            'search' => $request->input('search')
        ];

        $data = Category::listAttractionCategory($filter)
        ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}
