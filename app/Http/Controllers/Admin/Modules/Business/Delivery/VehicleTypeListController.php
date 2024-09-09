<?php

namespace App\Http\Controllers\Admin\Modules\Business\Delivery;

use Carbon\Carbon;
use App\Models\UserLog;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Helpers\Utils\ImagePath;
use App\Models\VehicleType;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\VehicleTypeStatus;
use App\Models\VehicleDistancePrice;

class VehicleTypeListController extends Controller
{
    const MODULE_KEY = 'vehicle_type';

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

        $filter['created_at_range'] = empty($filter['date_time_picker']) ? null : $filter['date_time_picker'];
        $data = VehicleType::lists($filter, $sortBy, $sortType)
            ->addSelect(
                DB::raw("
                    CASE WHEN vehicle_type.status = '" . VehicleTypeStatus::getEnabled() . "'
                    THEN 'true'
                    ELSE 'false'
                    END status
                ")
            )
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

    public function store(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getCreatePermission())) {
            /** check validation */
            $this->checkValidation($request);

            DB::beginTransaction();

            $vehicle_type = new VehicleType();
            $vehicle_type->setData($request);
            $vehicle_type->created_at = Carbon::now();

            if ($vehicle_type->save()) {
                if (!empty($request->input('image'))) {
                    //Upload Image
                    $image = StringHelper::uploadImage($request->input('image'), ImagePath::vehicleDeliveryType);
                    $vehicle_type->image = $image;
                    $vehicle_type->save();
                }

                //Vehicle Distance Price
                if (!empty($request->input('vehicle_price'))) {
                    foreach ($request->input('vehicle_price') as $obj) {
                        $vehicle_distance_price_data = [
                            VehicleDistancePrice::VEHICLE_ID => $vehicle_type->{VehicleType::ID},
                            VehicleDistancePrice::MIN_DISTANCE => $obj['min_distance'],
                            VehicleDistancePrice::MAX_DISTANCE => $obj['max_distance'],
                            VehicleDistancePrice::PRICE => $obj['price'],
                        ];

                        $vehicle_distance_price = new VehicleDistancePrice();
                        $vehicle_distance_price->setData($vehicle_distance_price_data);
                        $vehicle_distance_price->save();
                    }
                }

                $description = 'Id : ' . $vehicle_type->id . ', Name : ' . $vehicle_type->name;
                UserLog::setLog(self::MODULE_KEY, Permission::getCreatePermission(), $description);
            }

            DB::commit();

            return $this->responseWithData($vehicle_type);
        } else {
            return $this->responseNoPermission();
        }
    }

    public function update(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getUpdatePermission())) {
            /** check validation */
            $this->checkValidation($request);

            DB::beginTransaction();

            $vehicle_type = VehicleType::find($request->input('id'));
            $vehicle_type->setData($request);
            $vehicle_type->updated_at = Carbon::now();

            if ($vehicle_type->save()) {
                if (!empty($request->input('image'))) {
                    // Update Image
                    $image = StringHelper::editImage(
                        $request->input('image'),
                        $request->input('old_image'),
                        ImagePath::vehicleDeliveryType
                    );
                    $vehicle_type->image = $image;
                    $vehicle_type->save();
                }

                //Vehicle Distance Price
                if (!empty($request->input('vehicle_price'))) {
                    foreach ($request->input('vehicle_price') as $obj) {
                        $vehicle_distance_price_data = [
                            VehicleDistancePrice::VEHICLE_ID => $vehicle_type->{VehicleType::ID},
                            VehicleDistancePrice::MIN_DISTANCE => $obj['min_distance'],
                            VehicleDistancePrice::MAX_DISTANCE => $obj['max_distance'],
                            VehicleDistancePrice::PRICE => $obj['price'],
                        ];
                        if (empty($obj[VehicleDistancePrice::ID])) {
                            $vehicle_distance_price = new VehicleDistancePrice();
                        } else {
                            $vehicle_distance_price = VehicleDistancePrice::find($obj[VehicleDistancePrice::ID]);
                        }
                        $vehicle_distance_price->setData($vehicle_distance_price_data);
                        $vehicle_distance_price->save();
                    }
                }

                //Deleted Vehicle Distance Price
                if (!empty($request->input('deleted_vehicle_price'))) {
                    foreach ($request->input('deleted_vehicle_price') as $obj) {
                        if (!empty($obj[VehicleDistancePrice::ID])) {
                            VehicleDistancePrice::find($obj[VehicleDistancePrice::ID])->delete();
                        }
                    }
                }

                $description = 'Id : ' . $vehicle_type->id . ', Name : ' . $vehicle_type->name;
                UserLog::setLog(self::MODULE_KEY, Permission::getUpdatePermission(), $description);
            }

            DB::commit();

            return $this->responseWithData($vehicle_type);
        } else {
            return $this->responseNoPermission();
        }
    }

    public function delete(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getDeletePermission())) {
            $this->validate($request, [
                'id' => 'required|exists:vehicle_type,id'
            ]);

            DB::beginTransaction();

            $vehicle_type = VehicleType::find($request['id']);


            if ($vehicle_type->delete()) {
                StringHelper::deleteImage($vehicle_type->image, ImagePath::vehicleDeliveryType);

                //Delete Vehicle Distance Price
                VehicleDistancePrice::where(VehicleDistancePrice::VEHICLE_ID, $vehicle_type->{VehicleType::ID})->delete();

                VehicleType::where(VehicleType::ID, $vehicle_type->id)->delete();

                $description = 'Id : ' . $vehicle_type->id . ', Name : ' . $vehicle_type->name;
                UserLog::setLog(self::MODULE_KEY, Permission::getDeletePermission(), $description);
            }
            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseNoPermission();
        }
    }

    private function checkValidation($data)
    {
        $this->validate($data, [
            'id' => !empty($data['id']) ? 'required|exists:vehicle_type,id' : 'nullable',
            'name' => 'required',
            'status' => 'required',
            'vehicle_price' => 'nullable',
            'vehicle_price.*.min_distance' => !empty($data['vehicle_price']) ? 'required' : 'null',
            'vehicle_price.*.max_distance' => !empty($data['vehicle_price']) ? 'required' : 'null',
            'vehicle_price.*.price' => !empty($data['vehicle_price']) ? 'required' : 'null',
            'deleted_vehicle_price' => 'nullable',
            'deleted_vehicle_price.*.id' => !empty($data['id']) && !empty($data['deleted_vehicle_price']) ? 'required' : 'null',
        ]);
    }

    public function changeStatus(Request $request)
    {
        /** check validation */
        $this->validate($request, [
            'id' => 'required|exists:vehicle_type,id'
        ]);

        DB::beginTransaction();

        $vehicle_type = VehicleType::find($request->input('id'));
        $vehicle_type->status = $request->input('status');
        if ($vehicle_type->save()) {
            $description = ' Id : ' . $vehicle_type->id . ', Change Status To: ' . $vehicle_type->status;
            UserLog::setLog(self::MODULE_KEY, Permission::getUpdatePermission(), $description);
        }

        DB::commit();
        return $this->responseWithSuccess();
    }

    public function getAutoOrder()
    {
        $lastOrder = 0;

        $data = VehicleType::orderBy('order', 'DESC')
            ->first();
        if (!empty($data)) {
            $lastOrder = $data->order + 1;
        }

        return $this->responseWithData($lastOrder);
    }
}
