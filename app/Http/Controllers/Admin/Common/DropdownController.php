<?php

namespace App\Http\Controllers\Admin\Common;

use App\Http\Controllers\Controller;
use App\Models\Commune;
use App\Models\Country;
use App\Models\District;
use App\Models\Province;
use Illuminate\Http\Request;

class DropdownController extends Controller
{
    //Get Country List
    public function getCountryList(Request $request)
    {
        $data = Country::listsCountry()->get();

        return $this->responseWithData($data);
    }

    //Get Province List By Country
    public function getProvinceListByCountry(Request $request)
    {
        // Filter
        $countryId = $request->input('country_id');

        $data = Province::lists()
        ->where('province.country_id', $countryId)
        ->get();

        return $this->responseWithData($data);
    }

    //Get District List By Province
    public function getDistrictListByProvince(Request $request)
    {
        $this->validate($request, [
            'province_id' => 'required|exists:province,id'
        ]);
        $filter = [
            'province_id' => $request->input('province_id'),
        ];
        $data = District::lists($filter)->get();

        return $this->responseWithData($data);
    }

    //Get Commune List By District
    public function getCommuneListByDistrict(Request $request)
    {
        $this->validate($request, [
            'district_id' => 'required|exists:district,id'
        ]);
        $filter = [
            'district_id' => $request->input('district_id'),
        ];
        $data = Commune::lists($filter)->get();

        return $this->responseWithData($data);
    }
}
