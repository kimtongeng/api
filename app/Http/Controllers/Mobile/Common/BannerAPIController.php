<?php


namespace App\Http\Controllers\Mobile\Common;

use App\Enums\Types\PositionPlatformType;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Get Banner By Screen Position
     *
     */
    public function getBannerByScreenPosition(Request $request)
    {
        $this->validate($request, [
            'screen' => 'required',
            'position' => 'required',
        ]);

        $screen = $request->input('screen');
        $position = $request->input('position');
        $platformType = PositionPlatformType::getMobile();

        $referenceIdRq = $request->input('reference_id');
        $referenceId = isset($referenceIdRq) ? $referenceIdRq : null;

        $data = Banner::getBannerDisplay($screen, $position, $referenceId, $platformType);

        return $this->responseWithData($data);
    }
}
