<?php

namespace App\Http\Controllers\Mobile\Modules\SocietySecurity;

use App\Helpers\FCM;
use App\Models\NewsVisitors;
use Illuminate\Http\Request;
use App\Models\ContactDevice;
use App\Helpers\Utils\ErrorCode;
use App\Enums\Types\IsContactLogin;
use App\Enums\Types\NewsVisitorsStatus;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use Carbon\Carbon;

class NewsVisitorsCrudController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    public function addNewsVisitors(Request $request)
    {
        $this->validate($request, [
            'news_id' => 'required|exists:news,id',
            'contact_id' => 'required|exists:contact,id',
        ]);

        // Check if a record with the same news_id and contact_id already exists
        $existingRecord = NewsVisitors::where('news_id', $request->input('news_id'))
            ->where('contact_id', $request->input('contact_id'))
            ->first();

        if (empty($existingRecord)) {
            $newsVisitors = new NewsVisitors();
            $newsVisitors->setData($request);
            $newsVisitors->{NewsVisitors::STATUS} = NewsVisitorsStatus::getPending();

            if ($newsVisitors->save()) {
                $data = [
                    'id' => $newsVisitors->id,
                    'news_id' => $newsVisitors->news_id,
                    'contact_id' => $newsVisitors->contact_id,
                    'contact_name' => Contact::find($newsVisitors->contact_id)->fullname,
                    'contact_image' => Contact::find($newsVisitors->contact_id)->profile_image,
                    'status' => $newsVisitors->status,
                ];
                return $this->responseWithData($data);
            } else {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else {
            return $this->responseWithData($existingRecord);
        }
    }

    public function joinConversation(Request $request)
    {
        $this->validate($request, [
            'news_id' => 'required|exists:news,id',
            'contact_id' => 'required|exists:contact,id',
        ]);

        $newsVisitors = NewsVisitors::where(NewsVisitors::NEWS_ID, $request->input('news_id'))
        ->where(NewsVisitors::CONTACT_ID, $request->input('contact_id'))->first();
        if (!empty($newsVisitors)) {
            $newsVisitors->{NewsVisitors::STATUS} = NewsVisitorsStatus::getJoin();
            $newsVisitors->{NewsVisitors::UPDATED_AT} = Carbon::now();
            if($newsVisitors->save()) {
                $contactDeviceData = ContactDevice::where(ContactDevice::CONTACT_ID, $request->input('contact_id'))
                ->whereNotNull(ContactDevice::FCM_TOKEN)
                ->where(ContactDevice::IS_LOGIN, IsContactLogin::getYes())
                ->get();

                foreach ($contactDeviceData as $item) {
                    $TOPIC_GROUP_NEWS_COMMENT = env('TOPIC_NEWS_COMMENT') . $request->input('news_id');
                    FCM::subscribeToTopic($TOPIC_GROUP_NEWS_COMMENT, $item[ContactDevice::FCM_TOKEN]);
                }

                $data = [
                    'id' => $newsVisitors->id,
                    'news_id' => $newsVisitors->news_id,
                    'contact_id' => $newsVisitors->contact_id,
                    'contact_name' => Contact::find($newsVisitors->contact_id)->fullname,
                    'contact_image' => Contact::find($newsVisitors->contact_id)->profile_image,
                    'status' => $newsVisitors->status,
                ];
                return $this->responseWithData($data);
            } else {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        }
    }

    public function leaveConversation(Request $request)
    {
        $this->validate($request, [
            'news_id' => 'required',
            'contact_id' => 'required',
        ]);

        $newsVisitors = NewsVisitors::where(NewsVisitors::NEWS_ID, $request->input('news_id'))
            ->where(NewsVisitors::CONTACT_ID, $request->input('contact_id'))->first();

        if(!empty($newsVisitors)) {
            $newsVisitors->{NewsVisitors::STATUS} = NewsVisitorsStatus::getLeave();
            $newsVisitors->{NewsVisitors::UPDATED_AT} = Carbon::now();
            if($newsVisitors->save()) {
                $contactDeviceData = ContactDevice::where(ContactDevice::CONTACT_ID, $request->input('contact_id'))
                ->whereNotNull(ContactDevice::FCM_TOKEN)
                ->where(ContactDevice::IS_LOGIN, IsContactLogin::getYes())
                ->get();

                foreach ($contactDeviceData as $item) {
                    $TOPIC_GROUP_NEWS_COMMENT = env('TOPIC_NEWS_COMMENT') . $request->input('news_id');
                    FCM::unSubscribeToTopic($TOPIC_GROUP_NEWS_COMMENT, $item[ContactDevice::FCM_TOKEN]);
                }

                $data = [
                    'id' => $newsVisitors->id,
                    'news_id' => $newsVisitors->news_id,
                    'contact_id' => $newsVisitors->contact_id,
                    'contact_name' => Contact::find($newsVisitors->contact_id)->fullname,
                    'contact_image' => Contact::find($newsVisitors->contact_id)->profile_image,
                    'status' => $newsVisitors->status,
                ];
                return $this->responseWithData($data);
            }
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    public function getNewsVisitorsList(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.news_id' => 'required|exists:news,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = $request->input('filter');

        $data = NewsVisitors::listNewsVisitors($filter)
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}
