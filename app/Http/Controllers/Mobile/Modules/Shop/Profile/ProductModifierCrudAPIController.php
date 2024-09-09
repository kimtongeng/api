<?php

namespace App\Http\Controllers\Mobile\Modules\Shop\Profile;

use App\Helpers\Utils\ErrorCode;
use App\Http\Controllers\Controller;
use App\Models\Modifier;
use App\Models\ModifierOption;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductModifierCrudAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Check Validation
     *
     */
    private function checkValidation($data)
    {
        $this->validate($data, [
            'id' => !empty($data['id']) ? 'required|exists:modifier,id' : 'nullable',
            'business_id' => 'required|exists:business,id',
            'name' => 'required',
            'choice' => 'required',
            'is_required' => 'required',
            'description' => 'nullable',
            //modifier_option
            'modifier_option' => 'required',
            'modifier_option.*.id' => !empty($data['id']) && !empty($data['modifier_option']) ? 'required' : 'nullable',
            'modifier_option.*.name' => !empty($data['modifier_option']) ? 'required' : 'nullable',
            //deleted_modifier_option
            'deleted_modifier_option.*.id' => !empty($data['id']) && !empty($data['deleted_modifier_option']) ? 'required|exists:modifier_option,id' : 'nullable',
        ]);
    }


    /**
     * Add Modifier
     *
     */
    public function addModifier(Request $request)
    {
        //Check Validation
        $this->checkValidation($request);

        DB::beginTransaction();

        $modifier = new Modifier();

        //Set Data
        $modifier->setData($request);
        $modifier->created_at = Carbon::now();

        //Save Data
        if ($modifier->save()) {
            if (!empty($request->input('modifier_option'))) {
                foreach ($request->input('modifier_option') as $obj) {
                    $data = [
                        'modifier_id' => $modifier->id,
                        'name' => $obj['name'],
                        'price' => empty($obj['price']) ? 0 : $obj['price']
                    ];
                    $modifier_option = new ModifierOption();
                    $modifier_option->setData($data);
                    $modifier_option->save();
                }
            }

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Edit Modifier
     *
     */
    public function editModifier(Request $request)
    {
        //Check Validation
        $this->checkValidation($request);

        DB::beginTransaction();

        $modifier = Modifier::find($request->input('id'));

        if(!empty($modifier)){
            //Set Data
            $modifier->setData($request);
            $modifier->updated_at = Carbon::now();

            //Save Data
            if ($modifier->save()) {
                //Check add or update modifier option
                if (!empty($request->input('modifier_option'))) {
                    foreach ($request->input('modifier_option') as $obj) {
                        if (empty($obj['id'])) {
                            $modifier_option = new ModifierOption();
                        } else {
                            $modifier_option = ModifierOption::find($obj['id']);
                        }

                        $data = [
                            'modifier_id' => $modifier->id,
                            'name' => $obj['name'],
                            'price' => $obj['price']
                        ];

                        $modifier_option->setData($data);
                        $modifier_option->save();
                    }
                }

                //Check has deleted modifier option or not
                if (!empty($request->input('deleted_modifier_option'))) {
                    foreach ($request->input('deleted_modifier_option') as $obj) {
                        if (!empty($obj['id'])) {
                            $modifier_option = ModifierOption::find($obj['id']);
                            $modifier_option->delete();
                        }
                    }
                }

                DB::commit();

                return $this->responseWithSuccess();
            } else {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        }else {
            return $this->responseValidation(ErrorCode::INVALID);
        }
    }

    /**
     * Delete Modifier
     *
     */
    public function deleteModifier(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:modifier,id',
        ]);

        DB::beginTransaction();

        $modifier = Modifier::find($request->input('id'));

        if ($modifier->delete()) {
            //Delete Modifier Option
            ModifierOption::where(ModifierOption::MODIFIER_ID, $modifier->{Modifier::ID})->delete();
        }

        DB::commit();
        return $this->responseWithSuccess();
    }

    /**
     * Get My Modifier
     *
     */
    public function getMyModifier(Request $request){
        $this->validate($request, [
            'business_id' => 'required|exists:business,id'
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = [
            'business_id' => $request->input('business_id'),
            'search' => $request->input('search')
        ];
        $data = Modifier::lists($filter)->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}
