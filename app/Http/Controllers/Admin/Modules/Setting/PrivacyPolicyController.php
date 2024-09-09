<?php

namespace App\Http\Controllers\Admin\Modules\Setting;

use App\Enums\Types\ContentCategoryStatus;
use App\Enums\Types\ContentCategoryType;
use App\Enums\Types\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\ContentCategory;
use App\Models\Lib;
use App\Models\Permission;
use App\Models\Post;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function response;

class PrivacyPolicyController extends Controller
{
    const MODULE_KEY = 'privacy_policy';

    //Get Lists
    public function get(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getViewPermission())) {

            $data = $this->getPageContentData($request->input('latin_title'));

            return $this->responseWithData($data);
        } else {
            return $this->responseNoPermission();
        }
    }

    // Update Or Store
    public function update(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getUpdatePermission())) {
            $this->checkValidation($request);

            DB::beginTransaction();

            //Get Category Or Store New
            $content_category = ContentCategory::where(ContentCategory::TYPE, ContentCategoryType::getPageContent())->first();
            if (empty($content_category)) {
                $content_category = new ContentCategory();

                $content_category_data = [
                    ContentCategory::NAME => 'Page Content',
                    ContentCategory::TYPE => ContentCategoryType::getPageContent(),
                    ContentCategory::PARENT_ID => 0,
                    ContentCategory::STATUS => ContentCategoryStatus::getEnable(),
                ];
                $content_category->setData($content_category_data);
                $content_category->save();
            }

            //Update Or Store New Post
            $privacy_policy = Post::find($request['id']);
            if (!empty($privacy_policy)) {
                $privacy_policy->full_desc = $request->input('description');
            } else {
                $privacy_policy = new Post();

                $privacy_policy_data = [
                    Post::TITLE => $request->input('title'),
                    Post::FULL_DESC => $request->input('description'),
                    Post::CATEGORY_ID => $content_category->id,
                    Post::ORDER => 0,
                    Post::STATUS => PostStatus::getEnable(),
                    Post::SHORT_DESC => null,
                ];

                $privacy_policy->setData($privacy_policy_data);
            }

            if ($privacy_policy->save()) {
                $description = 'Id : ' . $privacy_policy->id . ', Title : ' . $privacy_policy->title;
                UserLog::setLog(self::MODULE_KEY, Permission::getUpdatePermission(), $description);
            }

            DB::commit();

            $data = $this->getPageContentData($privacy_policy->title);
            return $this->responseWithData($data);
        } else {
            return response()->json(['success' => 0, 'message' => Lib::PER_FAIL], 403);
        }
    }

    //Check validation function
    public function checkValidation($data)
    {
        $this->validate($data, [
            'title' => 'nullable',
            'description' => 'required',
        ]);
    }

    //Get Page Content
    private function getPageContentData($title)
    {
        $data = Post::getPageContent($title)->first();

        return $data;
    }
}
