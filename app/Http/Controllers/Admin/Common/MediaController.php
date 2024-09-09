<?php

namespace App\Http\Controllers\Admin\Common;

use App\Helpers\StringHelper;
use App\Helpers\Utils\ImagePath;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function uploadMedia(Request $request)
    {
        $this->validate($request, [
            'image' => 'required'
        ]);

        $image = StringHelper::uploadImage($request->input('image'), ImagePath::mediaImagePath);

        return $this->responseWithData($image);
    }
}
