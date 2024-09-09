<?php

namespace App\Http\Controllers\Mobile\Modules\SocietySecurity;

use Carbon\Carbon;
use App\Helpers\FCM;
use App\Models\News;
use App\Models\Contact;
use Mpdf\Tag\NewColumn;
use App\Models\Business;
use App\Models\District;
use App\Models\Province;
use App\Models\BusinessType;
use App\Models\DistrictNews;
use App\Models\GalleryPhoto;
use App\Models\NewsVisitors;
use App\Models\Notification;
use App\Models\ProvinceNews;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Models\ContactDevice;
use App\Models\EventTypeNews;
use App\Helpers\Utils\AudioPath;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use App\Models\BusinessCategory;
use App\Models\PositionGroupNews;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\BusinessStatus;
use App\Enums\Types\IsContactLogin;
use App\Http\Controllers\Controller;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\GalleryPhotoType;
use App\Models\BusinessMultiCategory;
use App\Enums\Types\NewsVisitorsStatus;
use App\Enums\Types\ContactNotificationType;

class NewsCrudAPIController extends Controller
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
            'id' => !empty($data['id']) ? 'required|exists:news,id' : 'nullable',
            'current_user_id' => 'required|exists:contact,id',
            // 'business_type_id' => 'required|exists:business_type,id',
            'event_type_id' => 'required',
            'image' => 'required',
            'old_image' => !empty($data['id']) ? 'required' : 'nullable',
            //gallery_photo
            'gallery_photo' => 'required',
            'gallery_photo.*.image' => 'required',
            //deleted_gallery_photo
            'deleted_gallery_photo.*.id' => !empty($data['id']) && !empty($data['deleted_gallery_photo']) ? 'required|exists:gallery_photo,id' : 'nullable',
            'name' => 'required',
            'audio' => 'nullable',
            'old_audio' => 'nullable',
            'youtube_link' => 'nullable',
            'description' => 'required',
            'position_group_list' => 'required',
            'position_group_list.*.position_group_id' => 'required|exists:business_category,id',
            //deleted_position_group_list
            'deleted_position_group_list.*.id' => !empty($data['id']) && !empty($data['deleted_position_group_list']) ? 'required|exists:position_group_news,id' : 'nullable',
            'province_list' => 'required',
            'province_list.*.province_id' => 'required|exists:province,id',
            //deleted_province_list
            'deleted_province_list.*.id' => !empty($data['id']) && !empty($data['deleted_province_list']) ? 'required|exists:province_news,id' : 'nullable',
            'district_list' => 'nullable',
            'district_list.*.district_id' => !empty($data['district_list']) ? 'required|exists:district,id' : 'nullable',
            //deleted_district_list
            'deleted_district_list.*.id' => !empty($data['id']) && !empty($data['deleted_district_list']) ? 'required|exists:district_news,id' : 'nullable',
            'latitude' => 'required',
            'longitude' => 'required',
            'address' => 'required',
        ]);
    }

    public function addNews(Request $request)
    {
        //Check Validation
        $this->checkValidation($request);

        DB::beginTransaction();

        $news = new News();

        //Merge Value Some Request
        $request->merge([
            News::CONTACT_ID => $request->input('current_user_id'),
            News::COUNTRY_ID => Contact::find($request->input('current_user_id'))->{Contact::COUNTRY_ID},
            News::BUSINESS_TYPE_ID => BusinessTypeEnum::getNews()
        ]);

        $news->setData($request);

        if($news->save()) {
            //Upload Thumbnail
            if(!empty($request->input($news->{News::IMAGE}))) {
                $image = StringHelper::uploadImage($request->input(News::IMAGE) , ImagePath::newsThumbnail);
                $news->{News::IMAGE} = $image;
                $news->save();
            }

            // Upload Gallery
            $gallery_photo_array = [];
            if (!empty($request->input('gallery_photo'))) {
                foreach ($request->input('gallery_photo') as $key => $obj) {
                    $data = [
                        GalleryPhoto::TYPE => GalleryPhotoType::getNewsCover(),
                        GalleryPhoto::TYPE_ID => $news->{News::ID},
                        GalleryPhoto::ORDER => $key + 1
                    ];

                    $gallery_photo = new GalleryPhoto();
                    $gallery_photo->setData($data);
                    if ($gallery_photo->save()) {
                        //Upload Image
                        $image = StringHelper::uploadImage($obj['image'], ImagePath::newsGallery);
                        $gallery_photo->{GalleryPhoto::IMAGE} = $image;
                        $gallery_photo->save();
                        $gallery_photo_array[] = $gallery_photo;
                    }
                }
            }

            //Upload Audio
            if (!empty($request->input($news->{News::AUDIO}))) {
                $audio = StringHelper::uploadAudio($request->input(News::AUDIO) , AudioPath::newsAudio);
                $news->{News::AUDIO} = $audio;
                $news->save();
            }

            //Set Position Group
            $position_group_news_array = [];
            if (!empty($request->input('position_group_list'))) {
                foreach ($request->input('position_group_list') as $obj) {
                    $position_group_news_data = [
                        PositionGroupNews::BUSINESS_ID => $news->{News::ID},
                        PositionGroupNews::POSITION_GROUP_ID => $obj['position_group_id'],
                    ];

                    $position_group_news = new PositionGroupNews();
                    $position_group_news->setData($position_group_news_data);
                    $position_group_news->save();
                    $position_group_news_array[] = $position_group_news;
                }
            }

            //Set Province
            $province_news_array = [];
            if (!empty($request->input('province_list'))) {
                foreach ($request->input('province_list') as $obj) {
                    $province_new_data = [
                        ProvinceNews::BUSINESS_ID => $news->{News::ID},
                        ProvinceNews::PROVINCE_ID => $obj['province_id'],
                    ];
                    $province_news = new ProvinceNews();
                    $province_news->setData($province_new_data);
                    $province_news->save();
                    $province_news_array[] = $province_news;
                }
            }

            //Set District
            $district_news_array = [];
            if (!empty($request->input('district_list'))) {
                foreach ($request->input('district_list') as $obj) {
                    $district_news_data = [
                        DistrictNews::NEWS_ID => $news->{News::ID},
                        DistrictNews::DISTRICT_ID => $obj['district_id'],
                    ];
                    $district_news = new DistrictNews();
                    $district_news->setData($district_news_data);
                    $district_news->save();
                    $district_news_array[] = $district_news;
                }
            }

            // Get Contact
            if (count($request->input('position_group_list')) > 0) {
                $contactGetNotiData = [];
                $positionGroupIds = collect($request->input('position_group_list'))->pluck('position_group_id')->toArray();
                $contacts = Contact::whereIn('position_group_id', $positionGroupIds);
                if(count($request->input('province_list')) > 0) {
                    $provinceGroupIds = collect($request->input('province_list'))->pluck('province_id')->toArray();
                    $province = $contacts->whereIn('province_id', $provinceGroupIds)->get();
                    if(!empty($request->input('district_list')) && count($request->input('district_list')) > 0) {
                        $districtGroupIds = collect($request->input('district_list'))->pluck('district_id')->toArray();
                        $district = $contacts->WhereIn('district_id', $districtGroupIds)->get();
                        foreach ($district as $obj) {
                            $contactDeviceData = ContactDevice::where(ContactDevice::CONTACT_ID, $obj->id)
                            ->whereNotNull(ContactDevice::FCM_TOKEN)
                            ->where(ContactDevice::IS_LOGIN, IsContactLogin::getYes())
                            ->get();

                            foreach ($contactDeviceData as $item) {
                                $TOPIC_GROUP_NEWS = env('TOPIC_NEWS') . $news->{News::ID};
                                FCM::subscribeToTopic($TOPIC_GROUP_NEWS, $item[ContactDevice::FCM_TOKEN]);
                            }

                            $contactGetNotiData[] = ['contact_id' => $obj->id];
                        }
                    } else {
                        foreach ($province as $obj) {
                            $contactDeviceData = ContactDevice::where(ContactDevice::CONTACT_ID, $obj->id)
                            ->whereNotNull(ContactDevice::FCM_TOKEN)
                            ->where(ContactDevice::IS_LOGIN, IsContactLogin::getYes())
                            ->get();

                            foreach ($contactDeviceData as $item) {
                                $TOPIC_GROUP_NEWS = env('TOPIC_NEWS') . $news->{News::ID};
                                FCM::subscribeToTopic($TOPIC_GROUP_NEWS, $item[ContactDevice::FCM_TOKEN]);
                            }

                            $contactGetNotiData[] = ['contact_id' => $obj->id];
                        }
                    }

                    $sendResponse = Notification::newsNotification(
                        ContactNotificationType::getLatestNews(),
                        env('TOPIC_NEWS') . $news->{News::ID},
                        $news->{News::ID},
                        $news,
                        $contactGetNotiData
                    );
                    info('Mobile Notification Latest News: ' . $sendResponse);
                }
            }

            $contactDeviceData = ContactDevice::where(ContactDevice::CONTACT_ID, $news->{News::CONTACT_ID})
            ->whereNotNull(ContactDevice::FCM_TOKEN)
            ->where(ContactDevice::IS_LOGIN, IsContactLogin::getYes())
            ->get();

            foreach ($contactDeviceData as $item) {
                $TOPIC_GROUP_NEWS_COMMENT = env('TOPIC_NEWS_COMMENT') . $news->{News::ID};
                FCM::subscribeToTopic($TOPIC_GROUP_NEWS_COMMENT, $item[ContactDevice::FCM_TOKEN]);
            }

            DB::commit();

            $data = [
                'news' => [
                    'id' => $news->id,
                    'business_type_id' => $news->business_type_id,
                    'business_type_name' => BusinessType::find($news->business_type_id)->name,
                    'event_type_id' => $news->event_type_id,
                    'event_type_name' => BusinessCategory::find($news->event_type_id)->name,
                    'contact_id' => $news->contact_id,
                    'contact_name' => $news->contact_name,
                    'name' => $news->name,
                    'image' => $news->image,
                    'audio' => $news->audio,
                    'youtube_link' => $news->youtube_link,
                    'latitude' => $news->latitude,
                    'longitude' => $news->longitude,
                    'address' => $news->address,
                    'description' => $news->description,
                    'gallery_photo' => $gallery_photo_array,
                    'position_group_news_list' => $position_group_news_array,
                    'province_news_list' => $province_news_array,
                    'district_news_list' => $district_news_array,
                ]
            ];
            return $this->responseWithData($data);
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    //edit news
    public function editNews(Request $request)
    {
        //Check Validation
        $this->checkValidation($request);

        DB::beginTransaction();

        $news = News::find($request->input(News::ID));

        if (!empty($news)) {
            //Merge Value Some Request
            $request->merge([
                News::CONTACT_ID => $request->input('current_user_id'),
                News::COUNTRY_ID => Contact::find($request->input('current_user_id'))->{Contact::COUNTRY_ID},
                News::BUSINESS_TYPE_ID => BusinessTypeEnum::getNews()
            ]);

            //Set Data
            $news->setData($request);

            if ($news->save()) {
                // Update Logo
                $image = StringHelper::editImage(
                    $request->input(News::IMAGE),
                    $request->input('old_image'),
                    ImagePath::newsThumbnail
                );
                $news->{News::IMAGE} = $image;
                $news->save();

                $gallery_photo_array = [];
                //Upload or Update Gallery Photo
                if (!empty($request->input('gallery_photo'))) {
                    foreach ($request->input('gallery_photo') as $key => $obj) {
                        $orderNumber = $key + 1;
                        if (empty($obj[GalleryPhoto::ID])) {
                            $data = [
                                GalleryPhoto::TYPE => GalleryPhotoType::getNewsCover(),
                                GalleryPhoto::TYPE_ID => $news->{News::ID},
                                GalleryPhoto::ORDER => $orderNumber
                            ];

                            $gallery_photo =  new GalleryPhoto();

                            $gallery_photo->setData($data);
                            if ($gallery_photo->save()) {
                                //Upload Cover
                                $image = StringHelper::uploadImage($obj[GalleryPhoto::IMAGE], ImagePath::newsGallery);
                                $gallery_photo->{GalleryPhoto::IMAGE} = $image;
                                $gallery_photo->save();
                            }
                        } else {
                            $gallery_photo = GalleryPhoto::find($obj[GalleryPhoto::ID]);
                            $gallery_photo->{GalleryPhoto::ORDER} = $orderNumber;
                            $gallery_photo->save();
                            $gallery_photo_array[] = $gallery_photo;
                        }
                    }
                }

                //Check have deleted Cover
                if (!empty($request->input('deleted_gallery_photo'))) {
                    foreach ($request['deleted_gallery_photo'] as $obj) {
                        if (!empty($obj[GalleryPhoto::ID])) {
                            $gallery_photo = GalleryPhoto::find($obj[GalleryPhoto::ID]);
                            $gallery_photo->delete();
                            StringHelper::deleteImage($gallery_photo->{GalleryPhoto::IMAGE}, ImagePath::newsGallery);
                        }
                    }
                }

                //Edit Audio
                $audio = StringHelper::editAudio(
                    $request->input(News::AUDIO),
                    $request->input('old_audio'),
                    AudioPath::newsAudio
                );
                $news->{News::AUDIO} = $audio;
                $news->save();

                //Set Or Update Position Group
                $position_group_news_array = [];
                if (!empty($request->input('position_group_list'))) {
                    foreach ($request->input('position_group_list') as $obj) {
                        $position_group_news_data = [
                            PositionGroupNews::BUSINESS_ID => $news->{News::ID},
                            PositionGroupNews::POSITION_GROUP_ID => $obj['position_group_id'],
                        ];

                        if (empty($obj[PositionGroupNews::ID])) {
                            $position_group_news = new PositionGroupNews();
                        } else {
                            $position_group_news = PositionGroupNews::find($obj[PositionGroupNews::ID]);
                        }
                        $position_group_news->setData($position_group_news_data);
                        $position_group_news->save();
                        $position_group_news_array[] = $position_group_news;
                    }
                }

                //Unsubscribe Old Position Group Contact
                if (count($request->input('deleted_position_group_list')) > 0) {
                    $positionGroupIds = collect($request->input('deleted_position_group_list'))->pluck('id')->toArray();
                    $positionGroupNewsList = PositionGroupNews::whereIn('id', $positionGroupIds)->get();
                    $contactId = [];

                    foreach ($positionGroupNewsList as $obj) {
                        $contactsForPositionGroup = Contact::where('position_group_id', $obj['position_group_id'])->get();
                        $contacts = array_merge($contactId, $contactsForPositionGroup->toArray());
                    }

                    if (count($request->input('province_list')) > 0) {
                        $provinceGroupIds = collect($request->input('province_list'))->pluck('province_id')->toArray();
                        if (!empty($request->input('district_list')) && count($request->input('district_list')) > 0) {
                            $districtGroupIds = collect($request->input('district_list'))->pluck('district_id')->toArray();
                            foreach ($contacts as $obj) {
                                if (in_array($obj['district_id'], $districtGroupIds)) {
                                    $contactDeviceData = ContactDevice::where(ContactDevice::CONTACT_ID, $obj['id'])
                                        ->whereNotNull(ContactDevice::FCM_TOKEN)
                                        ->where(ContactDevice::IS_LOGIN, IsContactLogin::getYes())
                                        ->get();

                                    foreach ($contactDeviceData as $item) {
                                        $TOPIC_GROUP_NEWS = env('TOPIC_NEWS') . $news->{News::ID};
                                        FCM::unSubscribeToTopic($TOPIC_GROUP_NEWS, $item->{ContactDevice::FCM_TOKEN});
                                    }
                                }
                            }
                        } else {
                            foreach ($contacts as $obj) {
                                if (in_array($obj['province_id'], $provinceGroupIds)) {
                                    $contactDeviceData = ContactDevice::where(ContactDevice::CONTACT_ID, $obj['id'])
                                        ->whereNotNull(ContactDevice::FCM_TOKEN)
                                        ->where(ContactDevice::IS_LOGIN, IsContactLogin::getYes())
                                        ->get();

                                    foreach ($contactDeviceData as $item) {
                                        $TOPIC_GROUP_NEWS = env('TOPIC_NEWS') . $news->{News::ID};
                                        FCM::unSubscribeToTopic($TOPIC_GROUP_NEWS, $item->{ContactDevice::FCM_TOKEN});
                                    }
                                }
                            }
                        }
                    }
                }

                //Check Have Value Delete Position Group
                if (!empty($request->input('deleted_position_group_list'))) {
                    foreach ($request->input('deleted_position_group_list') as $obj) {
                        if (!empty($obj[PositionGroupNews::ID])) {
                            $position_group_news = PositionGroupNews::find($obj[PositionGroupNews::ID]);
                            $position_group_news->delete();
                        }
                    }
                }

                //Insert Or Update Province
                $province_news_array = [];
                if (!empty($request->input('province_list'))) {
                    foreach ($request->input('province_list') as $obj) {
                        $province_new_data = [
                            ProvinceNews::BUSINESS_ID => $news->{News::ID},
                            ProvinceNews::PROVINCE_ID => $obj['province_id']
                        ];

                        if (empty ($obj[ProvinceNews::ID])) {
                            $province_news = new ProvinceNews();
                        } else {
                            $province_news = ProvinceNews::find($obj[ProvinceNews::ID]);
                        }
                        $province_news->setData($province_new_data);
                        $province_news->save();
                        $province_news_array[] = $province_news;
                    }
                }

                //Check Have Value Delete Province
                if (!empty($request->input('deleted_province_list'))) {
                    foreach ($request->input('deleted_province_list') as $obj) {
                        if (!empty($obj[ProvinceNews::ID])) {
                            $province_news = ProvinceNews::find($obj[ProvinceNews::ID]);
                            $province_news->delete();
                        }
                    }
                }

                //Set Or Update District
                $district_news_array = [];
                if (!empty($request->input('district_list'))) {
                    foreach ($request->input('district_list') as $obj) {
                        $district_news_data = [
                            DistrictNews::NEWS_ID => $news->{News::ID},
                            DistrictNews::DISTRICT_ID => $obj['district_id'],
                        ];
                        if (empty($obj[DistrictNews::ID])) {
                            $district_news = new DistrictNews();
                        } else {
                            $district_news = DistrictNews::find($obj[DistrictNews::ID]);
                        }
                        $district_news->setData($district_news_data);
                        $district_news->save();
                        $district_news_array[] = $district_news;
                    }
                }

                //Check Have Value Delete District
                if (!empty($request->input('deleted_district_list'))) {
                    foreach ($request->input('deleted_district_list') as $obj) {
                        if (!empty($obj[DistrictNews::ID])) {
                            $district_news = DistrictNews::find($obj[DistrictNews::ID]);
                            $district_news->save();
                        }
                    }
                }

                // Get Contact
                if (count($request->input('position_group_list')) > 0) {
                    $contactGetNotiData = [];
                    $positionGroupIds = collect($request->input('position_group_list'))->pluck('position_group_id')->toArray();
                    $contacts = Contact::whereIn('position_group_id', $positionGroupIds);
                    if (count($request->input('province_list')) > 0) {
                        $provinceGroupIds = collect($request->input('province_list'))->pluck('province_id')->toArray();
                        $province = $contacts->whereIn('province_id', $provinceGroupIds)->get();
                        if (!empty($request->input('district_list')) && count($request->input('district_list')) > 0) {
                            $districtGroupIds = collect($request->input('district_list'))->pluck('district_id')->toArray();
                            $district = $contacts->WhereIn('district_id', $districtGroupIds)->get();
                            foreach ($district as $obj) {
                                $contactDeviceData = ContactDevice::where(ContactDevice::CONTACT_ID, $obj->id)
                                    ->whereNotNull(ContactDevice::FCM_TOKEN)
                                    ->where(ContactDevice::IS_LOGIN, IsContactLogin::getYes())
                                    ->get();

                                foreach ($contactDeviceData as $item) {
                                    $TOPIC_GROUP_NEWS = env('TOPIC_NEWS') . $news->{News::ID};
                                    FCM::subscribeToTopic($TOPIC_GROUP_NEWS, $item[ContactDevice::FCM_TOKEN]);
                                }

                                $contactGetNotiData[] = ['contact_id' => $obj->id];
                            }
                        } else {
                            foreach ($province as $obj) {
                                $contactDeviceData = ContactDevice::where(ContactDevice::CONTACT_ID, $obj->id)
                                    ->whereNotNull(ContactDevice::FCM_TOKEN)
                                    ->where(ContactDevice::IS_LOGIN, IsContactLogin::getYes())
                                    ->get();

                                foreach ($contactDeviceData as $item) {
                                    $TOPIC_GROUP_NEWS = env('TOPIC_NEWS') . $news->{News::ID};
                                    FCM::subscribeToTopic($TOPIC_GROUP_NEWS, $item[ContactDevice::FCM_TOKEN]);
                                }

                                $contactGetNotiData[] = ['contact_id' => $obj->id];
                            }
                        }

                        $sendResponse = Notification::newsNotification(
                            ContactNotificationType::getLatestNews(),
                            env('TOPIC_NEWS') . $news->{News::ID},
                            $news->{News::ID},
                            $news,
                            $contactGetNotiData
                        );
                        info('Mobile Notification Latest News: ' . $sendResponse);
                    }
                }

                DB::commit();
                $data = [
                    'news' => [
                        'id' => $news->id,
                        'business_type_id' => $news->business_type_id,
                        'business_type_name' => BusinessType::find($news->business_type_id)->name,
                        'event_type_id' => $news->event_type_id,
                        'event_type_name' => BusinessCategory::find($news->event_type_id)->name,
                        'contact_id' => $news->contact_id,
                        'contact_name' => $news->contact_name,
                        'name' => $news->name,
                        'image' => $news->image,
                        'audio' => $news->audio,
                        'youtube_link' => $news->youtube_link,
                        'latitude' => $news->latitude,
                        'longitude' => $news->longitude,
                        'address' => $news->address,
                        'description' => $news->description,
                        'gallery_photo' => $gallery_photo_array,
                        'position_group_news_list' => $position_group_news_array,
                        'province_news_list' => $province_news_array,
                        'district_news_list' => $district_news_array,
                    ]
                ];
                return $this->responseWithData($data);
            } else {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else {
            return $this->responseValidation(ErrorCode::INVALID);
        }
    }

    //delete News
    public function deleteNews(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:news,id',
        ]);

        DB::beginTransaction();

        $news = News::find($request->input(News::ID));

        if ($news->delete()) {
            //Delete Logo
            StringHelper::deleteImage($news->{News::IMAGE}, ImagePath::newsThumbnail);

            //Delete Gallery Photo
            $gallery_photo_list = GalleryPhoto::where(GalleryPhoto::TYPE, GalleryPhotoType::getNewsCover())
                ->where(GalleryPhoto::TYPE_ID, $news->{News::ID})
                ->get();
            foreach ($gallery_photo_list as $obj) {
                $gallery_photo = GalleryPhoto::find($obj[GalleryPhoto::ID]);
                StringHelper::deleteImage($gallery_photo->{GalleryPhoto::IMAGE}, ImagePath::newsGallery);
                $gallery_photo->delete();
            }

            //Delete Audio
            StringHelper::deleteAudio($news->{News::AUDIO}, AudioPath::newsAudio);

            //Delete Position Group news
            PositionGroupNews::where(PositionGroupNews::BUSINESS_ID, $news->{News::ID})->delete();

            //Delete Province List
            ProvinceNews::where(ProvinceNews::BUSINESS_ID, $news->{News::ID})->delete();

            //Delete District List
            DistrictNews::where(DistrictNews::NEWS_ID, $news->{News::ID})->delete();
        }

        DB::commit();
        return $this->responseWithSuccess();
    }

    public function getNewsList(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = $request->input('filter');

        $data = News::listNews($filter)->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}
