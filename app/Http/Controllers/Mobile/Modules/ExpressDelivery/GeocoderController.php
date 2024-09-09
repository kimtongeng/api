<?php

namespace App\Http\Controllers\Mobile\Modules\ExpressDelivery;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class GeocoderController extends Controller
{
    public function forward(Request $request)
    {
        try {
            $geocoderType = strtolower(env('geocoderType') ?? "google");
            $addresses = [];

            //google
            $googleMapKey = env('googleMapKey');
            $resultLimit = $request->result_limit ?? 5;
            $locationType = $request->location_type ?? "";
            $api = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$request->lat},{$request->lng}&key=$googleMapKey";
            $api .= "&location_type=$locationType&limit=$resultLimit";
            $response = Http::get($api);

            if ($response->successful()) {
                //
                foreach ($response["results"] as $address) {
                    $addresses[] = [
                        "geometry" => [
                            "location" => [
                                "lat" => $address["geometry"]["location"]["lat"],
                                "lng" => $address["geometry"]["location"]["lng"],
                            ],
                        ],
                        "formatted_address" => $address["formatted_address"] ?? '',
                        "country" => $this->getTypeFromAddressComponents("country", $address),
                        "country_code" => $this->getTypeFromAddressComponents("country", $address, "short_name"),
                        "postal_code" => $this->getTypeFromAddressComponents("postal_code", $address),
                        "locality" => $this->getTypeFromAddressComponents("locality", $address),
                        "subLocality" => $this->getTypeFromAddressComponents("sublocality", $address),
                        "administrative_area_level_1" => $this->getTypeFromAddressComponents("administrative_area_level_1", $address),
                        "administrative_area_level_2" => $this->getTypeFromAddressComponents("administrative_area_level_2", $address),
                        "thorough_fare" => $this->getTypeFromAddressComponents("thorough_fare", $address),
                        "sub_thorough_fare" => $this->getTypeFromAddressComponents("sub_thorough_fare", $address),
                    ];
                }
            } else {
                Log::info("GeocoderController Error", [$response->json()]);
                throw new \Exception($response->json()["meta"]["message"] ?? $response->json()['message'] ?? "Failed", 1);
            }


            return response()->json([
                "data" => $addresses,
            ], 200);

            return [
                $request->all(),
                $geocoderType,
                $api,
                $response->json(),
            ];
        } catch (\Exception $ex) {
            return response()->json([
                "message" => $ex->getMessage(),
            ], 400);
        }
    }

    public function reverse(Request $request)
    {
        try {
            $geocoderType = env('geocoderType') ?? "google";
            $countiresSearch = env('placeFilterCountryCodes');
            $addresses = [];

            //google
            $googleMapKey = env('googleMapKey');
            $api = "https://maps.googleapis.com/maps/api/place/textsearch/json?query={$request->keyword}&key=$googleMapKey&region={$countiresSearch}&location={$request->locoation}";
            $response = Http::get($api);

            if ($response->successful()) {

                //
                foreach ($response["results"] as $address) {

                    $addresses[] = [
                        "geometry" => [
                            "location" => [
                                "lat" => $address["geometry"]["location"]["lat"],
                                "lng" => $address["geometry"]["location"]["lng"],
                            ],
                        ],
                        "place_id" => $address["place_id"] ?? '',
                        "formatted_address" => $address["formatted_address"] ?? '',
                        "country" => "",
                        "country_code" => "",
                        "postal_code" => "",
                        "locality" => "",
                        "subLocality" => "",
                        "administrative_area_level_1" => "",
                        "administrative_area_level_2" => "",
                        "thorough_fare" => "",
                        "sub_thorough_fare" => "",
                    ];
                }
            } else {
                throw new \Exception($response->json()["meta"]["message"] ?? $response->json(), 1);
            }

            return response()->json([
                "data" => $addresses,
            ], 200);
        } catch (\Exception $ex) {
            return response()->json([
                "message" => $ex->getMessage(),
            ], 400);
        }
    }

    public function getTypeFromAddressComponents(
        $type,
        $searchResult,
        $nameTye = "long_name"
    ) {
        //
        $result = "";
        //
        foreach ($searchResult["address_components"] as $componenet) {
            $found = in_array($type, $componenet["types"]);
            if ($found) {
                $result = $componenet[$nameTye];
                break;
            }
        }
        return $result;
    }

    public function newReverse(Request $request)
    {

        try {
            $geocoderType = strtolower(env('geocoderType') ?? "google");
            $countiresSearch = env('placeFilterCountryCodes');
            $addresses = [];

            if ($geocoderType != "google") {
                return $this->reverse($request);
            } else {
                //google
                $googleMapKey = env('googleMapKey');
                $api = "https://maps.googleapis.com/maps/api/place/autocomplete/json?input={$request->keyword}&key=$googleMapKey&location={$request->locoation}&region=" . ($request->region ?? $countiresSearch) . "";
                $response = Http::get($api);

                if ($response->successful()) {

                    //
                    foreach ($response["predictions"] as $address) {
                        $addresses[] = $address;
                    }
                } else {
                    throw new \Exception($response->json()["meta"]["message"] ?? $response->json(), 1);
                }
            }

            return response()->json([
                "data" => $addresses,
            ], 200);
        } catch (\Exception $ex) {
            return response()->json([
                "message" => $ex->getMessage(),
            ], 400);
        }
    }

    public function reverseDetails(Request $request)
    {

        try {

            $addressData = null;
            //google
            $googleMapKey = env('googleMapKey');
            $api = "https://maps.googleapis.com/maps/api/place/details/json?fields=address_component,formatted_address,name,geometry&place_id={$request->place_id}&key=$googleMapKey";
            $response = Http::get($api);

            if ($response->successful()) {
                // https: //maps.googleapis.com/maps/api/place/details/json?fields=address_component,formatted_address,name,geometry&place_id=
                //
                $address = $response["result"];
                if ($request->plain ?? false) {
                    $addressData = $address;
                } else {
                    $addressData = [
                        "geometry" => [
                            "location" => [
                                "lat" => $address["geometry"]["location"]["lat"],
                                "lng" => $address["geometry"]["location"]["lng"],
                            ],
                        ],
                        "formatted_address" => $address["formatted_address"] ?? '',
                        "country" => $this->getTypeFromAddressComponents("country", $address),
                        "country_code" => $this->getTypeFromAddressComponents("country", $address, "short_name"),
                        "postal_code" => $this->getTypeFromAddressComponents("postal_code", $address),
                        "locality" => $this->getTypeFromAddressComponents("locality", $address),
                        "subLocality" => $this->getTypeFromAddressComponents("sublocality", $address),
                        "administrative_area_level_1" => $this->getTypeFromAddressComponents("administrative_area_level_1", $address),
                        "administrative_area_level_2" => $this->getTypeFromAddressComponents("administrative_area_level_2", $address),
                        "thorough_fare" => $this->getTypeFromAddressComponents("thorough_fare", $address),
                        "sub_thorough_fare" => $this->getTypeFromAddressComponents("sub_thorough_fare", $address),
                    ];
                }
            } else {
                throw new \Exception($response->json()["meta"]["message"] ?? $response->json(), 1);
            }
            return response()->json($addressData, 200);
        } catch (\Exception $ex) {
            return response()->json([
                "message" => $ex->getMessage(),
            ], 400);
        }
    }
}
