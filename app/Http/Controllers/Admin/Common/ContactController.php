<?php

namespace App\Http\Controllers\Admin\Common;

use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\ContactNotificationType;
use App\Enums\Types\ContactStatus;
use App\Enums\Types\IsBusinessOwner;
use App\Helpers\StringHelper;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use App\Http\Controllers\Controller;
use App\Models\BusinessType;
use App\Models\Contact;
use App\Models\ContactDevice;
use App\Models\Notification;
use App\Models\Permission;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    const MODULE_KEY = 'contact';

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

    private function getList($tableSize, $filter = [], $sortBy = '', $sortType = '')
    {
        if (empty($tableSize)) {
            $tableSize = 10;
        }

        $filter['created_at_range'] = empty($filter['date_time_picker']) ? null : $filter['date_time_picker'];
        $data = Contact::listForAdmin($filter, $sortBy, $sortType)
            ->addSelect(
                DB::raw("
                    CASE WHEN contact.status = '" . ContactStatus::getActivated() . "'
                    THEN 'true'
                    ELSE 'false'
                    END status
                "),
                DB::raw("
                    CASE WHEN contact.is_property_owner = '" . IsBusinessOwner::getYes() . "'
                    THEN 'true'
                    ELSE 'false'
                    END is_property_owner_active
                "),
                DB::raw("
                    CASE WHEN contact.is_hotel_owner = '" . IsBusinessOwner::getYes() . "'
                    THEN 'true'
                    ELSE 'false'
                    END is_hotel_owner_active
                "),
                DB::raw("
                    CASE WHEN contact.is_attraction_owner = '" . IsBusinessOwner::getYes() . "'
                    THEN 'true'
                    ELSE 'false'
                    END is_attraction_owner_active
                "),
                DB::raw("
                    CASE WHEN contact.is_massage_owner = '" . IsBusinessOwner::getYes() . "'
                    THEN 'true'
                    ELSE 'false'
                    END is_massage_owner_active
                "),
                DB::raw("
                    CASE WHEN contact.is_seller = '" . IsBusinessOwner::getYes() . "'
                    THEN 'true'
                    ELSE 'false'
                    END is_seller_active
                "),
                DB::raw("
                    CASE WHEN contact.is_ktv_owner = '" . IsBusinessOwner::getYes() . "'
                    THEN 'true'
                    ELSE 'false'
                    END is_ktv_owner_active
                "),
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

    /**
     * Get Combo List
     *
     */
    public function getComboList(Request $request)
    {
        $data = Contact::getComboList();

        return $this->responseWithData($data);
    }

    public function delete(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getDeletePermission())) {

            $this->validate($request, [
                'id' => 'required|exists:contact,id'
            ]);

            DB::beginTransaction();

            $contact = Contact::find($request['id']);

            StringHelper::deleteImage($contact->profile_image, ImagePath::contactImagePath);
            StringHelper::deleteImage($contact->cover_image, ImagePath::contactImagePath);

            $description = 'Id : ' . $contact->id . ', Name : ' . $contact->getName();
            if ($contact->delete()) {
                UserLog::setLog(self::MODULE_KEY, Permission::getDeletePermission(), $description);
            }
            DB::commit();
            return $this->responseWithSuccess();
        } else {
            return $this->responseNoPermission();
        }
    }

    public function changeStatus(Request $request)
    {
        /** check validation */
        $this->validate($request, [
            'id' => 'required|exists:contact,id'
        ]);

        DB::beginTransaction();

        $contact = Contact::find($request->input('id'));
        $contact->status = $request->input('status');
        if ($contact->save()) {
            $description = ' Id : ' . $contact->id . ', Change Status To: ' . $contact->status;
            UserLog::setLog(self::MODULE_KEY, Permission::getUpdatePermission(), $description);
        }

        DB::commit();
        return $this->responseWithSuccess();
    }

    public function getDevice(Request $request)
    {
        $this->validate($request, [
            'contact_id' => 'required|exists:contact,id'
        ]);

        $data = ContactDevice::where(ContactDevice::CONTACT_ID, $request->input(ContactDevice::CONTACT_ID))
            ->whereNotNull(ContactDevice::FCM_TOKEN)
            ->orderBy(ContactDevice::ID, 'DESC')
            ->get();

        return $this->responseWithData($data);
    }

    public function blockBusiness(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:contact,id',
            'business_type' => 'required|exists:business_type,id',
            'active' => 'required|boolean',
        ]);

        $contact = Contact::find($request->input('id'));
        $active = $request->input('active') == false ? IsBusinessOwner::getSuspend() : IsBusinessOwner::getYes();

        if ($request->input('business_type') == BusinessTypeEnum::getProperty()) {
            $contact->{Contact::IS_PROPERTY_OWNER} = $active;
        } else if ($request->input('business_type') == BusinessTypeEnum::getAccommodation()) {
            $contact->{Contact::IS_HOTEL_OWNER} = $active;
        } else if ($request->input('business_type') == BusinessTypeEnum::getAttraction()) {
            $contact->{Contact::IS_ATTRACTION_OWNER} = $active;
        } else if ($request->input('business_type') == BusinessTypeEnum::getMassage()) {
            $contact->{contact::IS_MASSAGE_OWNER} = $active;
        } else if ($request->input('business_type') == BusinessTypeEnum::getShopRetail() ||
            $request->input('business_type') == BusinessTypeEnum::getShopWholesale() ||
            $request->input('business_type') == BusinessTypeEnum::getRestaurant() ||
            $request->input('business_type') == BusinessTypeEnum::getShopLocalProduct() ||
            $request->input('business_type') == BusinessTypeEnum::getService()
        ) {
            $contact->{Contact::IS_SELLER} =  $active;
        } else if ($request->input('business_type') == BusinessTypeEnum::getKtv()) {
            $contact->{Contact::IS_KTV_OWNER} = $active;
        }

        if ($contact->save()) {
            $blockedType = 0;

            if ($request->input('business_type') == BusinessTypeEnum::getProperty()) {
                if ($contact->{Contact::IS_PROPERTY_OWNER} == IsBusinessOwner::getSuspend()) {
                    $blockedType = ContactNotificationType::getAppBlocked();
                } else if ($contact->{Contact::IS_PROPERTY_OWNER} == IsBusinessOwner::getYes()) {
                    $blockedType = ContactNotificationType::getAppUnblocked();
                }
            } else if ($request->input('business_type') == BusinessTypeEnum::getAccommodation()) {
                if ($contact->{Contact::IS_HOTEL_OWNER} == IsBusinessOwner::getSuspend()) {
                    $blockedType = ContactNotificationType::getAppBlocked();
                } else if ($contact->{Contact::IS_HOTEL_OWNER} == IsBusinessOwner::getYes()) {
                    $blockedType = ContactNotificationType::getAppUnblocked();
                }
            } else if ($request->input('business_type') == BusinessTypeEnum::getAttraction()) {
                if ($contact->{Contact::IS_ATTRACTION_OWNER} == IsBusinessOwner::getSuspend()) {
                    $blockedType = ContactNotificationType::getAppBlocked();
                } else if ($contact->{Contact::IS_ATTRACTION_OWNER} == IsBusinessOwner::getYes()) {
                    $blockedType = ContactNotificationType::getAppUnblocked();
                }
            } else if ($request->input('business_type') == BusinessTypeEnum::getMassage()) {
                if ($contact->{Contact::IS_MASSAGE_OWNER} == IsBusinessOwner::getSuspend()) {
                    $blockedType = ContactNotificationType::getAppBlocked();
                } else if ($contact->{Contact::IS_MASSAGE_OWNER} == IsBusinessOwner::getYes()) {
                    $blockedType = ContactNotificationType::getAppUnblocked();
                }
            } else if (
                $request->input('business_type') == BusinessTypeEnum::getShopRetail() ||
                $request->input('business_type') == BusinessTypeEnum::getShopWholesale() ||
                $request->input('business_type') == BusinessTypeEnum::getRestaurant() ||
                $request->input('business_type') == BusinessTypeEnum::getShopLocalProduct() ||
                $request->input('business_type') == BusinessTypeEnum::getService()
            ) {
                if ($contact->{Contact::IS_SELLER} == IsBusinessOwner::getSuspend()) {
                    $blockedType = ContactNotificationType::getAppBlocked();
                } else if ($contact->{Contact::IS_SELLER} == IsBusinessOwner::getYes()) {
                    $blockedType = ContactNotificationType::getAppUnblocked();
                }
            } else if ($request->input('business_type') == BusinessTypeEnum::getKtv()) {
                if ($contact->{Contact::IS_KTV_OWNER} == IsBusinessOwner::getSuspend()) {
                    $blockedType = ContactNotificationType::getAppBlocked();
                } else if ($contact->{Contact::IS_KTV_OWNER} == IsBusinessOwner::getYes()) {
                    $blockedType = ContactNotificationType::getAppUnblocked();
                }
            }

            Notification::appBlockedNotification($request->input('business_type'), $contact->{Contact::ID}, $blockedType);
            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::INVALID);
        }
    }
}
