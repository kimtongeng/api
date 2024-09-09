<?php

namespace App\Http\Controllers\Mobile\Common;

use App\Enums\Types\BusinessTypeStatus;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Story;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StoryAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    public function getBusinessTypeListByContact(Request $request)
    {
        $this->validate($request, [
            'current_user_id' => 'required',
        ]);

        $currentUserID = $request->input('current_user_id');

        $data = Contact::join('business', 'contact.id', 'business.contact_id')
            ->join('business_type', 'business_type.id', 'business.business_type_id')
            ->where('contact.id', $currentUserID)
            ->where('business_type.status', BusinessTypeStatus::getEnable())
            ->select(
                'business_type.id',
                'business_type.name',
            )
            ->groupBy('business_type.id')
            ->get();

        return $this->responseWithData($data);
    }


    public function getBusinessListByBusinessType(Request $request)
    {
        $this->validate($request, [
            'current_user_id' => 'required',
            'business_type_id' => 'required',
        ]);

        $currentUserID = $request->input('current_user_id');
        $businessTypeID = $request->input('business_type_id');

        $data = Contact::join('business', 'contact.id', 'business.contact_id')
            ->join('business_type', 'business_type.id', 'business.business_type_id')
            ->where('contact.id', $currentUserID)
            ->where('business_type.id', $businessTypeID)
            ->where('business_type.status', BusinessTypeStatus::getEnable())
            ->select(
                'business.id',
                'business.business_type_id',
                'business.name',
            )
            ->groupBy('business_type.id')
            ->get();

        return $this->responseWithData($data);
    }


    public function addNewStory(Request $request)
    {
        $this->validate($request, [
            'business_type_id' => 'required',
            'business_id' => 'required',
            'owner_id' => 'required',
            'type' => 'required',
            'filename' => 'required',
        ]);

        $story = new Story();
        $story->setData($request);
        $story->created_at = Carbon::now();
        $story->expired_at = Carbon::now()->addHours(12);
        $story->save();

        return $this->responseWithData($story);
    }

    public function getStoryListForOwner(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.owner_id' => 'required',
        ]);
    }

    public function getStoryListForViewer(Request $request)
    {
        $this->validate($request, [
            'filter' => 'nullable',
            'filter.business_type_id' => 'nullable',
        ]);
    }

    public function updateStoryView(Request $request)
    {
        $this->validate($request, [
            'story_id' => 'required',
            'contact_id' => 'required',
        ]);
    }

    public function addStoryEmoji(Request $request)
    {
        $this->validate($request, [
            'story_id' => 'required',
            'contact_id' => 'required',
            'emoji' => 'required',
            'count' => 'required',
        ]);
    }
}
