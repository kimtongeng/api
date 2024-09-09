<?php


namespace App\Http\Controllers\Mobile\Common;

use App\Enums\Types\BusinessTypeEnum;
use App\Models\Video;
use Illuminate\Http\Request;
use App\Models\PlaceVideoList;
use App\Enums\Types\VideoStatus;
use App\Enums\Types\PositionType;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\PositionStatus;
use App\Http\Controllers\Controller;
use App\Enums\Types\PositionPlatformType;
use App\Models\Business;

class VideoAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Get Video By Screen Position
     *
     */
    public function getVideoByScreenPosition(Request $request)
    {
        $this->validate($request, [
            'screen' => 'required',
            'position' => 'required',
            'see_more' => 'nullable',
            'search' => 'nullable',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $screen = $request->input('screen');
        $position = $request->input('position');
        $filter = [
            'search' => $request->input('search')
        ];
        $platformType = PositionPlatformType::getMobile();

        $data = Video::getVideoDisplay($screen, $position, $platformType, $filter)
        ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     * Get Video Attraction Screen Position
     *
     */
    public function getVideoAttractionScreen(Request $request)
    {
        $this->validate($request, [
            'table_size' => 'nullable'
        ]);

        //Count Total Video
        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $data = PlaceVideoList::select(
            'place_video_list.id',
            'place_video_list.business_id',
            'place_video_list.link'
        )
        ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     * Get Video Charity Screen Position
     *
     */
    public function getVideoCharityScreen(Request $request)
    {
        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $data = Business::select(
            'business.id',
            'business.name',
            'business.video_link',
        )
        ->whereNotNull('business.video_link')
        ->where('business.business_type_id', BusinessTypeEnum::getCharityOrganization())
        ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}
