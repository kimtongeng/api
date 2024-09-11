<?php

namespace App\Http\Controllers;

use App\Helpers\ImageHelpers;
use App\Helpers\StringHelper;
use App\Helpers\Utils\ImagePath;
use App\Models\TypeItemFake;
use Illuminate\Http\Request;
use Mpdf\Gif\ImageHeader;

class TypeItemFakeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = $this->getList(
            $request->input("table_size"),
            $request->input("sort_by"),
            $request->input("sort_type"),
            $request->input("filter")
        );
        return $data;
    }
    private function getList($tableSize,$sort_by,$sort_type,$filter){
        if(empty($tableSize)){
            $tableSize = 10;
        }
        
        $data = TypeItemFake::getList($sort_by,$sort_type,$filter)->paginate($tableSize);

        $response = [
            "pagination"=>[
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => intval($data->firstItem()),
                'to' => intval($data->lastItem())
            ],
            "data"=>$data->items()
            ];
      return $response;
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $typeItemFake = new TypeItemFake();
        $typeItemFake->setData($request);
        if(!empty($request->input('image'))){
            $image = StringHelper::uploadImage($request->input("image"),ImagePath::itemType);
            // $image = ImageHelpers::uploadImage($request->input("image"),"/images/delivery/");
            $typeItemFake->image = $image;
            $typeItemFake->save();
        }
        return ["data"=>$typeItemFake];
    }

    private function checkValidate($data){
        // $uniqueName = false;
        // $oldTypeItem = TypeItemFake::find($data["id"]);

        // if(!empty($oldName)){
        //     if($data['name'] == $oldTypeItem->name){
        //         $uniqueName = true;
        //     }
        // }
        // else {
        //     //When Add
        //     $uniqueName = true;
        // }
        // $messages = [
        //     'name.unique' => 'validation_unique_name'
        // ];
        // $this->validate($data,[
        //     "name"=> $uniqueName ? "require|uniqid:type_item_fakes" :  'required'
        // ]);

        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TypeItemFake  $typeItemFake
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {   

        $typeItemFake = TypeItemFake::find($request->input('id'));
        $typeItemFake->setData($request);

        if($typeItemFake->save()){
            $image = StringHelper::editImage($request->input("image"),$request->input("old_image"),ImagePath::itemType);
            // $image = ImageHelpers::updateImage($request->input("image"),$request->input("old_image"),"/images/delivery/");
            $typeItemFake->image = $image;
            $typeItemFake->save();
       
        }
        return $this->responseWithData($typeItemFake);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TypeItemFake  $typeItemFake
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request){
        $typeItemFake = TypeItemFake::find($request->id);
        $typeItemFake->delete();
        return 'success';
    }
}
