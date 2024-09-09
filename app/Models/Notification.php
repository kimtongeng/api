<?php /** @noinspection PhpExpressionResultUnusedInspection */

namespace App\Models;

use App\Enums\Types\AdminNotificationType;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\ContactBroadcastType;
use App\Enums\Types\ContactNotificationType;
use App\Enums\Types\ContactType;
use App\Enums\Types\IsContactLogin;
use App\Enums\Types\IsResizeImage;
use App\Enums\Types\NotificationReadType;
use App\Enums\Types\NotificationSendToPlatform;
use App\Enums\Types\NotificationSendType;
use App\Enums\Types\NotificationViewContactType;
use App\Helpers\FCM;
use App\Helpers\StringHelper;
use App\Helpers\Utils\ImagePath;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\Self_;

use function env;
use function info;

class Notification extends Model
{
    use SoftDeletes;

    const TABLE_NAME = 'notification';
    const ID = 'id';
    const TITLE = 'title';
    const DESCRIPTION = 'description';
    const IMAGE = 'image';
    const CONTACT_BROADCAST_TYPE = 'contact_broadcast_type';
    const CONTACT_NOTI_TYPE = 'contact_noti_type';
    const CONTACT_ID = 'contact_id';
    const ADMIN_NOTI_TYPE = 'admin_noti_type';
    const REFERENCE_ID = 'reference_id';
    const BUSINESS_TYPE = 'business_type';
    const CONTACT_TYPE = 'contact_type';
    const CREATED_BY = 'created_by';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    //Get Lists
    public static function listForAdmin($filter = [])
    {
        $search = isset($filter['search']) ? $filter['search'] : null;
        $notificationId = isset($filter['notification_id']) ? $filter['notification_id'] : null;

        $createdAt = isset($filter['created_at']) ? $filter['created_at'] : null;
        $startDate = empty($createdAt['startDate']) ? null : Carbon::parse($createdAt['startDate'])->format('Y-m-d');
        $endDate = empty($createdAt['endDate']) ? null : Carbon::parse($createdAt['endDate'])->format('Y-m-d');

        return self::leftjoin('notification_view', function ($join) {
            $join->on('notification_view.notification_id', 'notification.id')
                ->where('notification_view.contact_id', Auth::guard('admin')->user()->id)
                ->where('notification_view.contact_type', NotificationViewContactType::getAdmin());
        })
            ->leftjoin(
                'business',
                function ($join) {
                    $join->on('business.id', '=', 'notification.reference_id')
                        ->where('notification.contact_noti_type', ContactNotificationType::getPropertyDetail())
                        ->whereNull('notification.deleted_at');
                }
            )
            ->leftjoin(
                'property_type',
                function ($join) {
                    $join->on('property_type.id', '=', 'notification.reference_id')
                        ->where('notification.contact_noti_type', ContactNotificationType::getPropertyType())
                        ->whereNull('notification.deleted_at');
                }
            )
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('notification.title', 'LIKE', '%' . $search . '%')
                        ->orWhere('notification.description', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($notificationId, function ($query) use ($notificationId) {
                $query->where(function ($query) use ($notificationId) {
                    $query->where('notification.id', $notificationId);
                });
            })
            ->when(!empty($startDate) && !empty($endDate), function ($query) use ($startDate, $endDate) {
                $query->whereRaw('Date(notification.created_at) between "' . $startDate . '" and "' . $endDate . '"');
            })
            ->whereNull('notification.deleted_at')
            ->select(
                'notification.id as id',
                'notification.title',
                'notification.description',
                'notification.image',
                'notification.contact_id',
                'notification.reference_id',
                DB::raw('CASE
                    WHEN business.name IS NOT NULL THEN business.name
                    WHEN property_type.name IS NOT NULL THEN property_type.name
                END reference_name'),
                'notification.contact_noti_type',
                'notification.admin_noti_type',
                'notification.business_type',
                DB::raw("CASE WHEN notification_view.id IS NULL THEN 0 ELSE 1 END status"),
                'notification.created_at',
            )
            ->orderBy('notification.id', 'desc');
    }

    //set data
    public function setData($data)
    {
        $this->{self::TITLE} = $data[self::TITLE];
        $this->{self::DESCRIPTION} = $data[self::DESCRIPTION];
        isset($data[self::IMAGE]) && $this->{self::IMAGE} = $data[self::IMAGE];
        $this->{self::CONTACT_BROADCAST_TYPE} = $data[self::CONTACT_BROADCAST_TYPE];
        $this->{self::CONTACT_NOTI_TYPE} = $data[self::CONTACT_NOTI_TYPE];
        $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
        $this->{self::ADMIN_NOTI_TYPE} = $data[self::ADMIN_NOTI_TYPE];
        $this->{self::BUSINESS_TYPE} = $data[self::BUSINESS_TYPE];
        $this->{self::REFERENCE_ID} = $data[self::REFERENCE_ID];
        isset($data[self::CONTACT_TYPE]) && $this->{self::CONTACT_TYPE} = $data[self::CONTACT_TYPE];
        $this->{self::CREATED_BY} = $data[self::CREATED_BY];
        $this->{self::CREATED_AT} = Carbon::now();
        $this->{self::UPDATED_AT} = Carbon::now();
    }

    //Get Total Not Read
    public static function getNotiCountNotRead()
    {
        $totalNotRead = 0;
        $countData = self::listForAdmin()
            ->whereNotNull('notification.admin_noti_type')
            ->get();
        foreach ($countData as $obj) {
            if ($obj['status'] == NotificationReadType::getNotRead()) {
                $totalNotRead += 1;
            }
        }

        return $totalNotRead;
    }

    //Get Notification TopNav Bar
    public static function notificationBadgeData()
    {
        $list = self::listForAdmin()
            ->whereNotNull('notification.admin_noti_type')
            ->limit(10)
            ->get();

        $response = [
            'total_not_read' => self::getNotiCountNotRead(),
            'list' => $list
        ];

        return $response;
    }

    //Get Noti Send Type By Contact Noti Type
    public static function getNotiTypeByContactNotiType($contactNotiType)
    {
        $notiSendType = 0;
        if ($contactNotiType == ContactNotificationType::getLink()) {
            $notiSendType = NotificationSendType::getLink();
        } else if ($contactNotiType == ContactNotificationType::getDetail()) {
            $notiSendType = NotificationSendType::getDetail();
        } else if ($contactNotiType == ContactNotificationType::getProperty()) {
            $notiSendType = NotificationSendType::getProperty();
        } else if ($contactNotiType == ContactNotificationType::getPropertyDetail()) {
            $notiSendType = NotificationSendType::getPropertyDetail();
        } else if ($contactNotiType == ContactNotificationType::getPropertyType()) {
            $notiSendType = NotificationSendType::getPropertyType();
        } else if ($contactNotiType == ContactNotificationType::getPropertyBooking()) {
            $notiSendType = NotificationSendType::getPropertyBooking();
        } else if ($contactNotiType == ContactNotificationType::getPropertyBookingApproved()) {
            $notiSendType = NotificationSendType::getPropertyBookingApproved();
        } else if ($contactNotiType == ContactNotificationType::getPropertyBookingRejected()) {
            $notiSendType = NotificationSendType::getPropertyBookingRejected();
        } else if ($contactNotiType == ContactNotificationType::getPropertyBookingCancelled()) {
            $notiSendType = NotificationSendType::getPropertyBookingCancelled();
        } else if ($contactNotiType == ContactNotificationType::getPropertyBookingCompleted()) {
            $notiSendType = NotificationSendType::getPropertyBookingCompleted();
        } else if ($contactNotiType == ContactNotificationType::getAppBlocked()) {
            $notiSendType = NotificationSendType::getAppBlocked();
        } else if ($contactNotiType == ContactNotificationType::getAppUnblocked()) {
            $notiSendType = NotificationSendType::getAppUnblocked();
        } else if ($contactNotiType == ContactNotificationType::getOwnerWithdrawMultiPropertyCommission()) {
            $notiSendType = NotificationSendType::getOwnerWithdrawMultiPropertyCommission();
        } else if ($contactNotiType == ContactNotificationType::getOwnerWithdrawSinglePropertyCommission()) {
            $notiSendType = NotificationSendType::getOwnerWithdrawSinglePropertyCommission();
        } else if ($contactNotiType == ContactNotificationType::getOwnerPropertyAddSaleAssistance()) {
            $notiSendType = NotificationSendType::getOwnerPropertyAddSaleAssistance();
        } else if ($contactNotiType == ContactNotificationType::getAgencyConfirmedWithdrawnMultiPropertyCommission()) {
            $notiSendType = NotificationSendType::getAgencyConfirmedWithdrawnMultiPropertyCommission();
        } else if ($contactNotiType == ContactNotificationType::getAgencyConfirmedWithdrawnSinglePropertyCommission()) {
            $notiSendType = NotificationSendType::getAgencyConfirmedWithdrawnSinglePropertyCommission();
        } else if ($contactNotiType == ContactNotificationType::getAgencyRejectedWithdrawnMultiPropertyCommission()) {
            $notiSendType = NotificationSendType::getAgencyRejectedWithdrawnMultiPropertyCommission();
        } else if ($contactNotiType == ContactNotificationType::getAgencyRejectedWithdrawnSinglePropertyCommission()) {
            $notiSendType = NotificationSendType::getAgencyRejectedWithdrawnSinglePropertyCommission();
        } else if ($contactNotiType == ContactNotificationType::getProductOrder()) {
            $notiSendType = NotificationSendType::getProductOrder();
        } else if ($contactNotiType == ContactNotificationType::getProductOrderApproved()) {
            $notiSendType = NotificationSendType::getProductOrderApproved();
        } else if ($contactNotiType == ContactNotificationType::getProductOrderRejected()) {
            $notiSendType = NotificationSendType::getProductOrderRejected();
        } else if ($contactNotiType == ContactNotificationType::getCharityDonation()) {
            $notiSendType = NotificationSendType::getCharityDonation();
        }else if ($contactNotiType == ContactNotificationType::getCharityDonationApproved()) {
            $notiSendType = NotificationSendType::getCharityDonationApproved();
        }else if ($contactNotiType == ContactNotificationType::getCharityDonationRejected()) {
            $notiSendType = NotificationSendType::getCharityDonationRejected();
        }else if ($contactNotiType == ContactNotificationType::getAccommodationBooking()) {
            $notiSendType = NotificationSendType::getAccommodationBooking();
        }else if ($contactNotiType == ContactNotificationType::getAccommodationReject()) {
            $notiSendType = NotificationSendType::getAccommodationReject();
        }else if ($contactNotiType == ContactNotificationType::getAccommodationCancel()) {
            $notiSendType = NotificationSendType::getAccommodationCancel();
        }else if ($contactNotiType == ContactNotificationType::getAccommodationBookingPayment()) {
            $notiSendType = NotificationSendType::getAccommodationBookingPayment();
        }else if ($contactNotiType == ContactNotificationType::getAccommodationRejectPayment()) {
            $notiSendType = NotificationSendType::getAccommodationRejectPayment();
        }else if ($contactNotiType == ContactNotificationType::getAccommodationAuditingPayment()) {
            $notiSendType = NotificationSendType::getAccommodationAuditingPayment();
        }else if ($contactNotiType == ContactNotificationType::getAccommodationBookingApprove()) {
            $notiSendType = NotificationSendType::getAccommodationBookingApprove();
        }else if ($contactNotiType == ContactNotificationType::getMassageTherapistAdd()) {
            $notiSendType = NotificationSendType::getMassageTherapistAdd();
        }else if ($contactNotiType == ContactNotificationType::getMassageTherapistApprove()) {
            $notiSendType = NotificationSendType::getMassageTherapistApprove();
        }else if ($contactNotiType == ContactNotificationType::getMassageTherapistReject()) {
            $notiSendType = NotificationSendType::getMassageTherapistReject();
        }else if ($contactNotiType == ContactNotificationType::getMassageShopBooking()) {
            $notiSendType = NotificationSendType::getMassageShopBooking();
        }else if ($contactNotiType == ContactNotificationType::getMassageShopPayment()) {
            $notiSendType = NotificationSendType::getMassageShopPayment();
        }else if ($contactNotiType == ContactNotificationType::getMassageShopReject()) {
            $notiSendType = NotificationSendType::getMassageShopReject();
        }else if ($contactNotiType == ContactNotificationType::getMassageShopCancel()) {
            $notiSendType = NotificationSendType::getMassageShopCancel();
        }else if ($contactNotiType == ContactNotificationType::getMassageShopAuditingPayment()) {
            $notiSendType = NotificationSendType::getMassageShopAuditingPayment();
        }else if ($contactNotiType == ContactNotificationType::getMassageShopRejectPayment()) {
            $notiSendType = NotificationSendType::getMassageShopRejectPayment();
        }else if ($contactNotiType == ContactNotificationType::getMassageShopApprove()) {
            $notiSendType = NotificationSendType::getMassageShopApprove();
        }else if ($contactNotiType == ContactNotificationType::getMassageShopForMassager()) {
            $notiSendType = NotificationSendType::getMassageShopForMassager();
        }else if ($contactNotiType == ContactNotificationType::getAttractionBooking()) {
            $notiSendType = NotificationSendType::getAttractionBooking();
        }else if ($contactNotiType == ContactNotificationType::getAttractionApprove()) {
            $notiSendType = NotificationSendType::getAttractionApprove();
        }else if ($contactNotiType == ContactNotificationType::getAttractionReject()) {
            $notiSendType = NotificationSendType::getAttractionReject();
        }else if ($contactNotiType == ContactNotificationType::getAttractionCancel()) {
            $notiSendType = NotificationSendType::getAttractionCancel();
        }else if ($contactNotiType == ContactNotificationType::getLatestNews()) {
            $notiSendType = NotificationSendType::getLatestNews();
        }else if ($contactNotiType == ContactNotificationType::getPosterComment()) {
            $notiSendType = NotificationSendType::getPosterComment();
        }else if ($contactNotiType == ContactNotificationType::getParticipantComment()) {
            $notiSendType = NotificationSendType::getParticipantComment();
        }else if ($contactNotiType == ContactNotificationType::getKtvGirlAdd()) {
            $notiSendType = NotificationSendType::getKtvGirlAdd();
        }else if ($contactNotiType == ContactNotificationType::getKtvGirlApprove()) {
            $notiSendType = NotificationSendType::getKtvGirlApprove();
        }else if ($contactNotiType == ContactNotificationType::getKtvGirlReject()) {
            $notiSendType = NotificationSendType::getKtvGirlReject();
        }else if ($contactNotiType == ContactNotificationType::getKtvBooking()) {
            $notiSendType = NotificationSendType::getKtvBooking();
        }else if ($contactNotiType == ContactNotificationType::getKtvApprove()) {
            $notiSendType = NotificationSendType::getKtvApprove();
        }else if ($contactNotiType == ContactNotificationType::getKtvReject()) {
            $notiSendType = NotificationSendType::getKtvReject();
        }else if ($contactNotiType == ContactNotificationType::getKtvCancel()) {
            $notiSendType = NotificationSendType::getKtvCancel();
        }else if ($contactNotiType == ContactNotificationType::getKtvAddPayment()) {
            $notiSendType = NotificationSendType::getKtvAddPayment();
        }else if ($contactNotiType == ContactNotificationType::getKtvRejectPayment()) {
            $notiSendType = NotificationSendType::getKtvRejectPayment();
        }else if ($contactNotiType == ContactNotificationType::getKtvAuditingPayment()) {
            $notiSendType = NotificationSendType::getKtvAuditingPayment();
        }else if ($contactNotiType == ContactNotificationType::getShareBusinessPermission()) {
            $notiSendType = NotificationSendType::getShareBusinessPermission();
        }else if ($contactNotiType == ContactNotificationType::getUpdateBusinessPermission()) {
            $notiSendType = NotificationSendType::getUpdateBusinessPermission();
        }else if ($contactNotiType == ContactNotificationType::getDeleteBusinessPermission()) {
            $notiSendType = NotificationSendType::getDeleteBusinessPermission();
        }else if ($contactNotiType == ContactNotificationType::getBookingShopBusinessForUserShare()) {
            $notiSendType = NotificationSendType::getBookingShopBusinessForUserShare();
        }else if ($contactNotiType == ContactNotificationType::getCancelShopBusinessForUserShare()) {
            $notiSendType = NotificationSendType::getCancelShopBusinessForUserShare();
        }else if ($contactNotiType == ContactNotificationType::getMassageTherapistUpdate()) {
            $notiSendType = NotificationSendType::getMassageTherapistUpdate();
        }else if ($contactNotiType == ContactNotificationType::getKtvGirlUpdate()) {
            $notiSendType = NotificationSendType::getKtvGirlUpdate();
        }
        return $notiSendType;
    }

    //Get Noti Send Type By Admin Noti Type
    public static function getNotiTypeByAdminNotiType($adminNotiType)
    {
        $notiSendType = 0;
        if ($adminNotiType == AdminNotificationType::getOwnerPayTransactionFee()) {
            $notiSendType = NotificationSendType::getOwnerPayTransactionFee();
        }
        return $notiSendType;
    }


    //Send Broadcast From Admin
    public static function sendBroadcastFromAdmin($businessType, $contactNotiType, $notiSendType, $data, $isSend = true, $action = 'add')
    {
        $sendResponse = '';
        if (!empty($data)) {
            $notiImage = null;
            $titleMessage = $data->title;
            $descriptionMessage = $data->description;

            if ($action == 'add') {
                $notification = new self();

                //Image Upload
                if (!empty($data->image)) {
                    $notiImage = StringHelper::uploadImage($data->image, ImagePath::notificationImagePath);
                }
            } else if ($action == 'update') {
                $notification = self::find($data->notification_id);

                //Image Update
                if (!empty($data->image)) {
                    $notiImage = StringHelper::editImage($data->image, $data->old_image, ImagePath::notificationImagePath);
                }
            }

            $notification_data = [
                self::ADMIN_NOTI_TYPE => null,
                self::CONTACT_ID => null,
                self::BUSINESS_TYPE => $businessType,
                self::CONTACT_BROADCAST_TYPE => ContactBroadcastType::getAdvertise(),
                self::CONTACT_NOTI_TYPE => $contactNotiType,
                self::REFERENCE_ID => $data->reference_id,
                self::TITLE => $titleMessage,
                self::DESCRIPTION => $descriptionMessage,
                self::IMAGE => $notiImage,
                self::CREATED_BY => Auth::guard('admin')->user()->id,
            ];

            $notification->setData($notification_data);

            if ($notification->save()) {
                if ($isSend == true) {
                    NotificationView::where(NotificationView::NOTIFICATION_ID, $notification->{self::ID})->delete();

                    $notificationTitle = $notification->title;
                    $notificationBody = $notification->description;

                    $dataKey = [
                        'platform' => NotificationSendToPlatform::getMobile(),
                        'type' => $notiSendType,
                        'notification_id' => $notification->{Notification::ID},
                        'reference_id' => $notification->{Notification::REFERENCE_ID},
                        'image' => $notiImage,
                    ];

                    $sendResponse = FCM::send(
                        "/topics/" . env('TOPIC_ANNOUNCEMENT'),
                        $notificationTitle,
                        $notificationBody,
                        $dataKey
                    );

                    info('Mobile Notification Broadcast');
                    info($sendResponse);
                }
            }
        }

        return $sendResponse;
    }

    //Share Permission Notification
    public static function sharePermissionBusinessNotification($contactNotiType, $contactID, $referenceID, $data)
    {
        $sendResponse = '';
        if (!empty($data)) {
            $titleMessage = '';
            $descriptionMessage = '';

            if($contactNotiType == ContactNotificationType::getShareBusinessPermission()) {
                $titleMessage = 'Share Permission Business';
                $descriptionMessage = 'You have been added by the Owner for Business Permission.';
            } else if ($contactNotiType == ContactNotificationType::getUpdateBusinessPermission()) {
                $titleMessage = 'Update Permission Business';
                $descriptionMessage = 'Your Permission of the Business have been Updated.';
            } else if ($contactNotiType == ContactNotificationType::getDeleteBusinessPermission()) {
                $titleMessage = 'Delete Permission Business';
                $descriptionMessage = 'Your Permission of the Business have been Deleted.';
            } else {
                return 'Invalid Contact Noti Type';
            }

            $notification_data = [
                self::ADMIN_NOTI_TYPE => null,
                self::BUSINESS_TYPE => $data['business_type_id'],
                self::CONTACT_BROADCAST_TYPE => ContactBroadcastType::getSelf(),
                self::CONTACT_NOTI_TYPE => $contactNotiType,
                self::CONTACT_ID => $contactID,
                self::REFERENCE_ID => $referenceID,
                self::TITLE => $titleMessage,
                self::DESCRIPTION => $descriptionMessage,
                self::IMAGE => $data['business_image'],
                self::CREATED_BY => Auth::guard('mobile')->user()->id,
            ];
            $notification = new self();
            $notification->setData($notification_data);

            if ($notification->save()) {
                $contactDeviceData = ContactDevice::where(ContactDevice::CONTACT_ID, $contactID)
                    ->whereNotNull(ContactDevice::FCM_TOKEN)
                    ->where(ContactDevice::IS_LOGIN, IsContactLogin::getYes())
                    ->get();

                foreach ($contactDeviceData as $item) {
                    $dataKey = [
                        'platform' => NotificationSendToPlatform::getMobile(),
                        'type' => self::getNotiTypeByContactNotiType($notification->{self::CONTACT_NOTI_TYPE}),
                        'notification_id' => $notification->{self::ID},
                        'reference_id' => $referenceID,
                        'business_type_id' => $notification->{self::BUSINESS_TYPE}
                    ];

                    $sendResponse = FCM::send(
                        $item[ContactDevice::FCM_TOKEN],
                        $titleMessage,
                        $descriptionMessage,
                        $dataKey
                    );
                }
            }
        }
        return $sendResponse;
    }

    //Property Notification
    public static function propertyNotification($contactNotiType, $contactID, $referenceID, $data)
    {
        $sendResponse = '';
        if (!empty($data)) {
            $titleMessage = '';
            $descriptionMessage = '';

            if ($contactNotiType == ContactNotificationType::getPropertyBooking()) {
                $titleMessage = 'Property Booking';
                $descriptionMessage = 'Your Property: ' . $data['name'] . ' has been booking.';
            } else if ($contactNotiType == ContactNotificationType::getPropertyBookingApproved()) {
                $titleMessage = 'Property Booking Approved';
                $descriptionMessage = 'Property: ' . $data['name'] . ' has been approved booking.';
            } else if ($contactNotiType == ContactNotificationType::getPropertyBookingRejected()) {
                $titleMessage = 'Property Booking Rejected';
                $descriptionMessage = 'Property: ' . $data['name'] . ' has been rejected booking.';
            } else if ($contactNotiType == ContactNotificationType::getPropertyBookingCancelled()) {
                $titleMessage = 'Property Booking Cancelled';
                $descriptionMessage = 'Your Property: ' . $data['name'] . ' has been cancelled booking.';
            } else if ($contactNotiType == ContactNotificationType::getPropertyBookingCompleted()) {
                $titleMessage = 'Property Booking Completed';
                $descriptionMessage = 'Property: ' . $data['name'] . ' has been completed booking.';
            } else if ($contactNotiType == ContactNotificationType::getOwnerWithdrawMultiPropertyCommission() || $contactNotiType == ContactNotificationType::getOwnerWithdrawSinglePropertyCommission()) {
                $titleMessage = 'Property Commission';
                $descriptionMessage = 'Your commission for property: ' . $data['name'] . ' has been withdrawn.';
            } else if ($contactNotiType == ContactNotificationType::getOwnerPropertyAddSaleAssistance()) {
                $titleMessage = 'Sale Assistance Property';
                $descriptionMessage = 'Owner: ' . $data['owner_name'] . ' has added you for sale assistance of project: ' . Str::limit($data['name'], 20) . '...';
            } else if ($contactNotiType == ContactNotificationType::getAgencyConfirmedWithdrawnMultiPropertyCommission() || $contactNotiType == ContactNotificationType::getAgencyConfirmedWithdrawnSinglePropertyCommission()) {
                $titleMessage = 'Property Commission';
                $descriptionMessage = 'Withdrawn Commission for property: ' . $data['name'] . ' has been confirmed.';
            } else if ($contactNotiType == ContactNotificationType::getAgencyRejectedWithdrawnMultiPropertyCommission() || $contactNotiType == ContactNotificationType::getAgencyRejectedWithdrawnSinglePropertyCommission()) {
                $titleMessage = 'Property Commission';
                $descriptionMessage = 'Withdrawn Commission for property: ' . $data['name'] . ' has been rejected.';
            } else {
                return 'Invalid Contact Noti Type';
            }

            $notification_data = [
                self::ADMIN_NOTI_TYPE => null,
                self::BUSINESS_TYPE => BusinessTypeEnum::getProperty(),
                self::CONTACT_BROADCAST_TYPE => ContactBroadcastType::getSelf(),
                self::CONTACT_NOTI_TYPE => $contactNotiType,
                self::CONTACT_ID => $contactID,
                self::REFERENCE_ID => $referenceID,
                self::TITLE => $titleMessage,
                self::DESCRIPTION => $descriptionMessage,
                self::IMAGE => null,
                self::CREATED_BY => Auth::guard('mobile')->user()->id,
            ];
            $notification = new self();
            $notification->setData($notification_data);

            if ($notification->save()) {
                $contactDeviceData = ContactDevice::where(ContactDevice::CONTACT_ID, $contactID)
                    ->whereNotNull(ContactDevice::FCM_TOKEN)
                    ->where(ContactDevice::IS_LOGIN, IsContactLogin::getYes())
                    ->get();

                foreach ($contactDeviceData as $item) {
                    $dataKey = [
                        'platform' => NotificationSendToPlatform::getMobile(),
                        'type' => self::getNotiTypeByContactNotiType($notification->{self::CONTACT_NOTI_TYPE}),
                        'notification_id' => $notification->{self::ID},
                        'reference_id' => $referenceID
                    ];

                    $sendResponse = FCM::send(
                        $item[ContactDevice::FCM_TOKEN],
                        $titleMessage,
                        $descriptionMessage,
                        $dataKey
                    );
                }
            }
        }
        return $sendResponse;
    }

    //App Blocked Notification
    public static function appBlockedNotification($businessType, $contactID, $blockedType)
    {
        $sendResponse = '';
        $titleMessage = '';
        $descriptionMessage = '';

        if ($businessType == BusinessTypeEnum::getProperty()) {
            if ($blockedType == ContactNotificationType::getAppBlocked()) {
                $titleMessage = 'App Blocked';
                $descriptionMessage = 'Your property business has been blocked due to late transaction fee payment. Please make payment to reopen your business';
            } else if ($blockedType == ContactNotificationType::getAppUnBlocked()) {
                $titleMessage = 'App Unblocked';
                $descriptionMessage = 'Your property business has been unblocked.';
            }
        } else if ($businessType == BusinessTypeEnum::getAttraction()) {
            if ($blockedType == ContactNotificationType::getAppBlocked()) {
                $titleMessage = 'App Blocked';
                $descriptionMessage = 'Your attraction business has been blocked due to late transaction fee payment. Please make payment to reopen your business';
            } else if ($blockedType == ContactNotificationType::getAppUnblocked()) {
                $titleMessage = 'App Unblocked';
                $descriptionMessage = 'Your attraction business has been unblocked';
            }
        } else if ($businessType == BusinessTypeEnum::getAccommodation()) {
            if ($blockedType == ContactNotificationType::getAppBlocked()) {
                $titleMessage = 'App Blocked';
                $descriptionMessage = 'Your hotel business has been blocked due to late transaction fee payment. Please make payment to reopen your business';
            } else if ($blockedType == ContactNotificationType::getAppUnblocked()) {
                $titleMessage = 'App Unblocked';
                $descriptionMessage = 'Your hotel business has been unblocked.';
            }
        } else if ($blockedType == BusinessTypeEnum::getMassage()) {
            if ($blockedType == ContactNotificationType::getAppBlocked()) {
                $titleMessage = 'App Blocked';
                $descriptionMessage = 'Your massage business has been blocked due to late transaction fee payment. Please make payment to reopen your business';
            } else if ($blockedType == ContactNotificationType::getAppUnblocked()) {
                $titleMessage = 'App Unblocked';
                $descriptionMessage = 'Your massage business has been unblocked';
            }
        } else if ($businessType == BusinessTypeEnum::getShopRetail() ||
            $businessType == BusinessTypeEnum::getShopWholesale() ||
            $businessType == BusinessTypeEnum::getShopLocalProduct() ||
            $businessType == BusinessTypeEnum::getRestaurant() ||
            $businessType == BusinessTypeEnum::getService() ||
            $businessType == BusinessTypeEnum::getModernCommunity()
        ) {
            if ($blockedType == ContactNotificationType::getAppBlocked()) {
                $titleMessage = 'App Blocked';
                $descriptionMessage = 'Your shop business has been blocked due to late transaction fee payment. Please make payment to reopen your business';
            } else if ($blockedType == ContactNotificationType::getAppUnblocked()) {
                $titleMessage = 'App Unblocked';
                $descriptionMessage = 'Your shop business has been unblocked';
            }
        } else if ($businessType == BusinessTypeEnum::getKtv()) {
            if ($blockedType == ContactNotificationType::getAppBlocked()) {
                $titleMessage = 'App Blocked';
                $descriptionMessage = 'Your KTV business has been blocked due to late transaction fee payment. Please make payment to reopen your business';
            } else if ($blockedType == ContactNotificationType::getAppUnblocked()) {
                $titleMessage = 'App Unblocked';
                $descriptionMessage = 'Your KTV business has been unblocked';
            }
        } else {
            return 'Invalid Business Type';
        }

        $notification_data = [
            self::ADMIN_NOTI_TYPE => null,
            self::BUSINESS_TYPE => $businessType,
            self::CONTACT_BROADCAST_TYPE => ContactBroadcastType::getSelf(),
            self::CONTACT_NOTI_TYPE => $blockedType,
            self::CONTACT_ID => $contactID,
            self::REFERENCE_ID => null,
            self::TITLE => $titleMessage,
            self::DESCRIPTION => $descriptionMessage,
            self::IMAGE => null,
            self::CREATED_BY => 1
        ];
        $notification = new self();
        $notification->setData($notification_data);

        if ($notification->save()) {
            $contactDeviceData = ContactDevice::where(ContactDevice::CONTACT_ID, $contactID)
                ->whereNotNull(ContactDevice::FCM_TOKEN)
                ->where(ContactDevice::IS_LOGIN, IsContactLogin::getYes())
                ->get();

            foreach ($contactDeviceData as $item) {
                $dataKey = [
                    'platform' => NotificationSendToPlatform::getMobile(),
                    'type' => self::getNotiTypeByContactNotiType($notification->{self::CONTACT_NOTI_TYPE}),
                    'notification_id' => $notification->{self::ID},
                    'reference_id' => null
                ];

                $sendResponse = FCM::send(
                    $item[ContactDevice::FCM_TOKEN],
                    $titleMessage,
                    $descriptionMessage,
                    $dataKey
                );
            }

            info('Mobile Notification Blocked App: ' . $businessType . ' => ' . $sendResponse);
        }
    }

    //Pay Transaction Fee
    public static function payTransactionFee($adminNotiType, $contactData, $businessType = null)
    {
        $sendResponse = '';
        $titleMessage = '';
        $descriptionMessage = '';

        if ($adminNotiType == AdminNotificationType::getOwnerPayTransactionFee()) {
            if ($businessType == BusinessTypeEnum::getProperty()) {
                $titleMessage = 'Transaction Fee';
                $descriptionMessage = 'Owner: ' . $contactData->{Contact::FULLNAME} . ' has been paid transaction fee for business property';
            } else if ($businessType == BusinessTypeEnum::getAttraction()) {
                $titleMessage = 'Transaction Fee';
                $descriptionMessage = 'Owner: ' . $contactData->{Contact::FULLNAME} . ' has been paid transaction fee for business attraction';
            } else if ($businessType == BusinessTypeEnum::getAccommodation()) {
                $titleMessage = 'Transaction Fee';
                $descriptionMessage = 'Owner: ' . $contactData->{Contact::FULLNAME} . ' has been paid transaction fee for business hotel';
            } else if ($businessType == BusinessTypeEnum::getMassage()) {
                $titleMessage = 'Transaction Fee';
                $descriptionMessage = 'Owner: ' . $contactData->{Contact::FULLNAME} . ' has been paid transaction fee for business massage';
            } else if ($businessType == BusinessTypeEnum::getShopRetail() ||
                $businessType == BusinessTypeEnum::getShopWholesale() ||
                $businessType == BusinessTypeEnum::getRestaurant() ||
                $businessType == BusinessTypeEnum::getShopLocalProduct() ||
                $businessType == BusinessTypeEnum::getService() ||
                $businessType == BusinessTypeEnum::getModernCommunity()
            ) {
                $titleMessage = 'Transaction Fee';
                $descriptionMessage = 'Owner: ' . $contactData->{Contact::FULLNAME} . ' has been paid transaction fee for business shop';
            } else if ($businessType == BusinessTypeEnum::getKtv()) {
                $titleMessage = 'Transaction Fee';
                $descriptionMessage = 'Owner: ' . $contactData->{Contact::FULLNAME} . ' has been paid transaction fee for business KTV';
            }
        }

        $notification_data = [
            self::ADMIN_NOTI_TYPE => $adminNotiType,
            self::BUSINESS_TYPE => $businessType,
            self::CONTACT_BROADCAST_TYPE => null,
            self::CONTACT_NOTI_TYPE => null,
            self::CONTACT_ID => $contactData->{Contact::ID},
            self::REFERENCE_ID => null,
            self::TITLE => $titleMessage,
            self::DESCRIPTION => $descriptionMessage,
            self::IMAGE => null,
            self::CREATED_BY => Auth::guard('mobile')->user()->id,
        ];

        $notification = new self();
        $notification->setData($notification_data);

        if ($notification->save()) {
            $dataKey = [
                'platform' => NotificationSendToPlatform::getWeb(),
                'type' => self::getNotiTypeByAdminNotiType($adminNotiType),
                'notification_id' => $notification->{self::ID},
                'reference_id' => null
            ];

            $sendResponse = FCM::send(
                "/topics/" . env('TOPIC_MAIN_ADMIN'),
                $titleMessage,
                $descriptionMessage,
                $dataKey
            );
        }

        return $sendResponse;
    }

    //Send Notification When Chat
    public static function sendNotificationWhenChat($sendTo, $title = '', $text = '', $platForm)
    {
        $dataKey = [
            'platform' => $platForm,
            'type' => NotificationSendType::getChat(),
        ];

        $send = FCM::send(
            $sendTo,
            $title,
            $text,
            $dataKey
        );

        return $send;
    }

    //Send Notification Chat To Group
    public static function sendNotificationToGroupChat($sendTo, $title = '', $text = '')
    {
        $dataKey = [
            'platform' => NotificationSendToPlatform::getMobile(),
            'type' => NotificationSendType::getChat(),
        ];

        $send = FCM::send(
            $sendTo,
            $title,
            $text,
            $dataKey
        );

        return $send;
    }

    //Shop Notification
    public static function shopNotification($contactNotiType, $contactID, $referenceID, $data)
    {
        $sendResponse = '';
        if (!empty($data)) {
            $titleMessage = '';
            $descriptionMessage = '';

            if ($contactNotiType == ContactNotificationType::getProductOrder()) {
                $titleMessage = 'New Order Product';
                $descriptionMessage = 'Product has been order by ' . $data['customer_name'];
            } else if ($contactNotiType == ContactNotificationType::getProductOrderApproved()) {
                $titleMessage = 'Product Order Approved';
                $descriptionMessage = 'Order: ' . $data['code'] . ' has been approved.';
            } else if ($contactNotiType == ContactNotificationType::getProductOrderRejected()) {
                $titleMessage = 'Product Order Rejected';
                $descriptionMessage = 'Order: ' . $data['code'] . ' has been rejected.';
            } else if ($contactNotiType == ContactNotificationType::getProductOrderCancelled()) {
                $titleMessage = 'Product Order Cancelled';
                $descriptionMessage = 'Order: ' . $data['code'] . ' has been cancelled.';
            } else if ($contactNotiType == ContactNotificationType::getBookingShopBusinessForUserShare()) {
                $titleMessage = 'Product Order';
                $descriptionMessage = 'Have New Order in ' . $data['business_name'];
            } else if ($contactNotiType == ContactNotificationType::getCancelShopBusinessForUserShare()) {
                $titleMessage = 'Product Cancel';
                $descriptionMessage = 'Product ' . $data['code'] . ' has been Cancel by ' . $data['customer_name'];
            } else {
                return 'Invalid Contact Noti Type';
            }

            $notification_data = [
                self::ADMIN_NOTI_TYPE => null,
                self::BUSINESS_TYPE => $data['business_type_id'],
                self::CONTACT_BROADCAST_TYPE => ContactBroadcastType::getSelf(),
                self::CONTACT_NOTI_TYPE => $contactNotiType,
                self::CONTACT_ID => $contactID,
                self::REFERENCE_ID => $referenceID,
                self::TITLE => $titleMessage,
                self::DESCRIPTION => $descriptionMessage,
                self::IMAGE => '/shop/logo/' . $data['image'],
                self::CONTACT_TYPE => ContactType::getSingleType(),
                self::CREATED_BY => Auth::guard('mobile')->user()->id,
            ];
            $notification = new self();
            $notification->setData($notification_data);

            if ($notification->save()) {
                $contactDeviceData = ContactDevice::where(ContactDevice::CONTACT_ID, $contactID)
                    ->whereNotNull(ContactDevice::FCM_TOKEN)
                    ->where(ContactDevice::IS_LOGIN, IsContactLogin::getYes())
                    ->get();

                foreach ($contactDeviceData as $item) {
                    $dataKey = [
                        'platform' => NotificationSendToPlatform::getMobile(),
                        'type' => self::getNotiTypeByContactNotiType($notification->{self::CONTACT_NOTI_TYPE}),
                        'notification_id' => $notification->{self::ID},
                        'reference_id' => $referenceID
                    ];

                    $sendResponse = FCM::send(
                        $item[ContactDevice::FCM_TOKEN],
                        $titleMessage,
                        $descriptionMessage,
                        $dataKey
                    );
                }
            }
        }
        return $sendResponse;
    }

    //Charity Notification
    public static function charityNotification($contactNotiType, $contactID, $referenceID, $data)
    {
        $sendResponse = '';
        if (!empty($data)) {
            $titleMessage = '';
            $descriptionMessage = '';

            if ($contactNotiType == ContactNotificationType::getCharityDonation()) {
                $titleMessage = 'Charity Donation';
                $descriptionMessage = 'Your Organization: ' . $data['organization_name'] . ' has been donation: ' . $data['donation_amount'] . '$ from ' . $data['customer_name'];
            } else if ($contactNotiType == ContactNotificationType::getCharityDonationApproved()) {
                $titleMessage = 'Charity Donation Approved';
                $descriptionMessage = 'Your donation: ' . $data['donation_amount'] . '$ for ' . $data['organization_name'] . ' has been approved.';
            } else if ($contactNotiType == ContactNotificationType::getCharityDonationRejected()) {
                $titleMessage = 'Charity Donation Rejected';
                $descriptionMessage = 'Your donation: ' . $data['donation_amount'] . '$ for ' . $data['organization_name'] . ' has been rejected.';
            } else {
                return 'Invalid Contact Noti Type';
            }

            $notification_data = [
                self::ADMIN_NOTI_TYPE => null,
                self::BUSINESS_TYPE => BusinessTypeEnum::getCharityOrganization(),
                self::CONTACT_BROADCAST_TYPE => ContactBroadcastType::getSelf(),
                self::CONTACT_NOTI_TYPE => $contactNotiType,
                self::CONTACT_ID => $contactID,
                self::REFERENCE_ID => $referenceID,
                self::TITLE => $titleMessage,
                self::DESCRIPTION => $descriptionMessage,
                self::IMAGE => null,
                self::CONTACT_TYPE => ContactType::getSingleType(),
                self::CREATED_BY => Auth::guard('mobile')->user()->id,
            ];
            $notification = new self();
            $notification->setData($notification_data);

            if ($notification->save()) {
                $contactDeviceData = ContactDevice::where(ContactDevice::CONTACT_ID, $contactID)
                    ->whereNotNull(ContactDevice::FCM_TOKEN)
                    ->where(ContactDevice::IS_LOGIN, IsContactLogin::getYes())
                    ->get();

                foreach ($contactDeviceData as $item) {
                    $dataKey = [
                        'platform' => NotificationSendToPlatform::getMobile(),
                        'type' => self::getNotiTypeByContactNotiType($notification->{self::CONTACT_NOTI_TYPE}),
                        'notification_id' => $notification->{self::ID},
                        'reference_id' => $referenceID
                    ];

                    $sendResponse = FCM::send(
                        $item[ContactDevice::FCM_TOKEN],
                        $titleMessage,
                        $descriptionMessage,
                        $dataKey
                    );
                }
            }
        }
        return $sendResponse;
    }

    //Accommodation Notification
    public static function accommodationNotification($contactNotiType, $contactID, $referenceID, $data)
    {
        $sendResponse = '';
        if(!empty($data)) {
            $titleMessage = '';
            $descriptionMessage = '';

            if ($contactNotiType == ContactNotificationType::getAccommodationBooking()) {
                $titleMessage = 'Hotel Booking';
                $descriptionMessage = 'Your Hotel ' . $data['accommodation_name'] . ' has been booking by ' . $data['customer_name'];
            } else if ($contactNotiType == ContactNotificationType::getAccommodationReject()) {
                $titleMessage = 'Hotel Reject Booking';
                $descriptionMessage = 'Your Hotel Booking at ' . $data['business_name'] . ' has been reject';
            } else if ($contactNotiType == ContactNotificationType::getAccommodationCancel()) {
                $titleMessage = 'Hotel Cancel Booking';
                $descriptionMessage = 'Your Hotel ' . $data['business_name'] . ' has been cancel by ' . $data['customer_name'];
            } else if ($contactNotiType == ContactNotificationType::getAccommodationBookingPayment()) {
                $titleMessage = 'Hotel Confirm Your Booking';
                $descriptionMessage = 'Hotel ' . $data['business_name'] . ' Confirm Your Booking Please Add payment';
            } else if ($contactNotiType == ContactNotificationType::getAccommodationRejectPayment()) {
                $titleMessage = 'Hotel Reject Payment';
                $descriptionMessage = 'Your Payment for ' . $data['business_name'] . ' has been Reject';
            } else if ($contactNotiType == ContactNotificationType::getAccommodationAuditingPayment()) {
                $titleMessage = 'Hotel Auditing Payment';
                $descriptionMessage = 'Customer ' . $data['customer_name'] . ' has been add payment for your hotel ' . $data['business_name'];
            } else if ($contactNotiType == ContactNotificationType::getAccommodationBookingApprove()) {
                $titleMessage = 'Hotel Approve Booking';
                $descriptionMessage = 'Your Hotel ' . $data['business_name'] . ' Booking Has been Approve';
            } else {
                return 'Invalid Contact Noti Type';
            }

            $notification_data = [
                self::ADMIN_NOTI_TYPE => null,
                self::BUSINESS_TYPE => BusinessTypeEnum::getAccommodation(),
                self::CONTACT_BROADCAST_TYPE => ContactBroadcastType::getSelf(),
                self::CONTACT_NOTI_TYPE => $contactNotiType,
                self::CONTACT_ID => $contactID,
                self::REFERENCE_ID => $referenceID,
                self::TITLE => $titleMessage,
                self::DESCRIPTION => $descriptionMessage,
                self::IMAGE => '/accommodation/logo/' . $data['accommodation_image'],
                self::CREATED_BY => Auth::guard('mobile')->user()->id
            ];
            $notification = new self();
            $notification->setData($notification_data);

            if ($notification->save()) {
                $contactDeviceData = ContactDevice::where(ContactDevice::CONTACT_ID, $contactID)
                    ->whereNotNull(ContactDevice::FCM_TOKEN)
                    ->where(ContactDevice::IS_LOGIN, IsContactLogin::getYes())
                    ->get();

                foreach ($contactDeviceData as $item) {
                    $dataKey = [
                        'platform' => NotificationSendToPlatform::getMobile(),
                        'type' => self::getNotiTypeByContactNotiType($notification->{self::CONTACT_NOTI_TYPE}),
                        'notification_id' => $notification->{self::ID},
                        'reference_id' => $referenceID
                    ];

                    $sendResponse = FCM::send(
                        $item[ContactDevice::FCM_TOKEN],
                        $titleMessage,
                        $descriptionMessage,
                        $dataKey
                    );
                }
            }
        }
        return $sendResponse;
    }

    //Massage Therapist Notification
    public static function massageTherapistNotification($contactNotiType, $contactID, $referenceID, $data)
    {
        $sendResponse = '';
        if(!empty($data)) {
            $titleMessage = '';
            $descriptionMessage = '';

            if($contactNotiType == ContactNotificationType::getMassageTherapistAdd()) {
                $titleMessage = 'Massage Therapist Add';
                $descriptionMessage = 'You have been add to Massage Shop ' . $data['business_name'];
            } else if ($contactNotiType == ContactNotificationType::getMassageTherapistUpdate()) {
                $titleMessage = 'Massage Therapist Update';
                $descriptionMessage = 'Massage Owner ' . $data['business_name'] . ' have been updated your action';
            } else if ($contactNotiType == ContactNotificationType::getMassageTherapistApprove()) {
                $titleMessage = 'Massage Therapist Approve';
                $descriptionMessage = 'Massage Therapist ' . $data['name'] . ' have been Approve';
            } else if ($contactNotiType == ContactNotificationType::getMassageTherapistReject()) {
                $titleMessage = 'Massage Therapist Reject';
                $descriptionMessage = 'Massage Therapist ' . $data['name'] . ' have been Reject';
            } else {
                return 'Invalid Contact Noti Type';
            }

            $notification_data = [
                self::ADMIN_NOTI_TYPE => null,
                self::BUSINESS_TYPE => BusinessTypeEnum::getMassage(),
                self::CONTACT_BROADCAST_TYPE => ContactBroadcastType::getSelf(),
                self::CONTACT_NOTI_TYPE => $contactNotiType,
                self::CONTACT_ID => $contactID,
                self::REFERENCE_ID => $referenceID,
                self::TITLE => $titleMessage,
                self::DESCRIPTION => $descriptionMessage,
                self::IMAGE => '/massage/logo/' . $data['business_image'],
                self::CONTACT_TYPE => ContactType::getSingleType(),
                self::CREATED_BY => Auth::guard('mobile')->user()->id,
            ];
            $notification = new self();
            $notification->setData($notification_data);

            if ($notification->save()) {
                $contactDeviceData = ContactDevice::where(ContactDevice::CONTACT_ID, $contactID)
                    ->whereNotNull(ContactDevice::FCM_TOKEN)
                    ->where(ContactDevice::IS_LOGIN, IsContactLogin::getYes())
                    ->get();

                foreach ($contactDeviceData as $item) {
                    $dataKey = [
                        'platform' => NotificationSendToPlatform::getMobile(),
                        'type' => self::getNotiTypeByContactNotiType($notification->{self::CONTACT_NOTI_TYPE}),
                        'notification_id' => $notification->{self::ID},
                        'reference_id' => $referenceID
                    ];

                    $sendResponse = FCM::send(
                        $item[ContactDevice::FCM_TOKEN],
                        $titleMessage,
                        $descriptionMessage,
                        $dataKey
                    );
                }
            }
        }
        return $sendResponse;
    }

    //Massage Shop Notification
    public static function massageShopNotification($contactNotiType, $contactID, $referenceID, $data)
    {
        $sendResponse = '';
        if(!empty($data)) {
            $titleMessage = '';
            $descriptionMessage = '';

            if($contactNotiType == ContactNotificationType::getMassageShopBooking()) {
                $titleMessage = 'Massage Booking';
                $descriptionMessage = 'Your Massage ' . $data['business_name'] . ' have been booking by ' . $data['customer_name'];
            } else if($contactNotiType == ContactNotificationType::getMassageShopApprove()) {
                $titleMessage = 'Massage Approve Booking';
                $descriptionMessage = 'Your Booking at ' . $data['business_name'] . ' have been Approve';
            } else if($contactNotiType == ContactNotificationType::getMassageShopPayment()) {
                $titleMessage = 'Massage Confirm Your Booking';
                $descriptionMessage = 'Massage ' . $data['business_name'] . ' Confirm Your Booking Please Add Payment';
            } else if($contactNotiType == ContactNotificationType::getMassageShopReject()) {
                $titleMessage = 'Massage Reject Booking';
                $descriptionMessage = 'Your Booking at ' . $data['business_name'] . ' have been Reject';
            } else if($contactNotiType == ContactNotificationType::getMassageShopCancel()) {
                $titleMessage = 'Massage Cancel Booking';
                $descriptionMessage = 'Customer ' . $data['customer_name'] . ' have been Cancel Booking Massage';
            } else if($contactNotiType == ContactNotificationType::getMassageShopAuditingPayment()) {
                $titleMessage = 'Massage Auditing Payment';
                $descriptionMessage = 'Customer ' . $data['customer_name'] . ' have been Add Payment Please Check';
            } else if($contactNotiType == ContactNotificationType::getMassageShopRejectPayment()) {
                $titleMessage = 'Massage Reject Payment';
                $descriptionMessage = 'Your Payment at ' . $data['business_name'] . ' have been Reject';
            } else if($contactNotiType == ContactNotificationType::getMassageShopForMassager()) {
                $titleMessage = 'Massage Approve';
                $descriptionMessage = 'You has New Booking from ' . $data['business_name'] . ' Please Check';
            } else {
                return 'Invalid Contact Noti Type';
            }

            $notification_data = [
                self::ADMIN_NOTI_TYPE => null,
                self::BUSINESS_TYPE => BusinessTypeEnum::getMassage(),
                self::CONTACT_BROADCAST_TYPE => ContactBroadcastType::getSelf(),
                self::CONTACT_NOTI_TYPE => $contactNotiType,
                self::CONTACT_ID => $contactID,
                self::REFERENCE_ID => $referenceID,
                self::TITLE => $titleMessage,
                self::DESCRIPTION => $descriptionMessage,
                self::IMAGE => '/massage/logo/' . $data['business_image'],
                self::CREATED_BY => Auth::guard('mobile')->user()->id,
            ];

            $notification = new self();
            $notification->setData($notification_data);

            if ($notification->save()) {
                $contactDeviceData = ContactDevice::where(ContactDevice::CONTACT_ID, $contactID)
                    ->whereNotNull(ContactDevice::FCM_TOKEN)
                    ->where(ContactDevice::IS_LOGIN, IsContactLogin::getYes())
                    ->get();

                foreach ($contactDeviceData as $item) {
                    $dataKey = [
                        'platform' => NotificationSendToPlatform::getMobile(),
                        'type' => self::getNotiTypeByContactNotiType($notification->{self::CONTACT_NOTI_TYPE}),
                        'notification_id' => $notification->{self::ID},
                        'reference_id' => $referenceID
                    ];
                    $sendResponse = FCM::send(
                        $item[ContactDevice::FCM_TOKEN],
                        $titleMessage,
                        $descriptionMessage,
                        $dataKey
                    );
                }
            }
        }
        return $sendResponse;
    }

    //Attraction Booking Notification
    public static function attractionNotification($contactNotiType, $contactID, $referenceID, $data)
    {
        $sendResponse = '';
        if(!empty($data)) {
            $titleMessage = '';
            $descriptionMessage = '';

            if ($contactNotiType == ContactNotificationType::getAttractionBooking()) {
                $titleMessage = 'Attraction Booking';
                $descriptionMessage = 'Your Attraction ' . $data['business_name'] . ' have been booking by ' . $data['customer_name'];
            } else if ($contactNotiType == ContactNotificationType::getAttractionReject()) {
                $titleMessage = 'Attraction Reject Booking';
                $descriptionMessage = 'Your Booking at ' . $data['business_name'] . ' have been reject';
            } else if ($contactNotiType == ContactNotificationType::getAttractionCancel()) {
                $titleMessage = 'Attraction Cancel Booking';
                $descriptionMessage = 'Customer ' . $data['customer_name'] . ' have been cancel booking attraction';
            } else if ($contactNotiType == ContactNotificationType::getAttractionApprove()) {
                $titleMessage = 'Attraction Approve Booking';
                $descriptionMessage = 'Your Booking at ' . $data['business_name'] . ' have been Approve';
            }

            $notification_data = [
                self::ADMIN_NOTI_TYPE => null,
                self::BUSINESS_TYPE => BusinessTypeEnum::getAttraction(),
                self::CONTACT_BROADCAST_TYPE => ContactBroadcastType::getSelf(),
                self::CONTACT_NOTI_TYPE => $contactNotiType,
                self::CONTACT_ID => $contactID,
                self::REFERENCE_ID => $referenceID,
                self::TITLE => $titleMessage,
                self::DESCRIPTION => $descriptionMessage,
                self::IMAGE => '/attraction/thumbnail/' . $data['business_image'],
                self::CONTACT_TYPE => ContactType::getSingleType(),
                self::CREATED_BY => Auth::guard('mobile')->user()->id
            ];

            $notification = new self();
            $notification->setData($notification_data);

            if ($notification->save()) {
                $contactDeviceData = ContactDevice::where(ContactDevice::CONTACT_ID, $contactID)
                    ->whereNotNull(ContactDevice::FCM_TOKEN)
                    ->where(ContactDevice::IS_LOGIN, IsContactLogin::getYes())
                    ->get();

                foreach ($contactDeviceData as $item) {
                    $dataKey = [
                        'platform' => NotificationSendToPlatform::getMobile(),
                        'type' => self::getNotiTypeByContactNotiType($notification->{self::CONTACT_NOTI_TYPE}),
                        'notification_id' => $notification->{self::ID},
                        'reference_id' => $referenceID
                    ];
                    $sendResponse = FCM::send(
                        $item[ContactDevice::FCM_TOKEN],
                        $titleMessage,
                        $descriptionMessage,
                        $dataKey
                    );
                }
            }
        }
        return $sendResponse;
    }

    //News Notification
    public static function newsNotification($contactNotiType, $topic, $referenceID, $data, $contactData = [])
    {
        if(!empty($data)) {
            $titleMessage = '';
            $descriptionMessage = '';

            if ($contactNotiType == ContactNotificationType::getLatestNews()) {
                $titleMessage = $data['name'];
                if (strlen($data['description']) > 30) {
                    $descriptionMessage = substr($data['description'], 0, 30) . '...';
                } else {
                     $descriptionMessage = $data['description'];
                }
            } else if ($contactNotiType == ContactNotificationType::getParticipantComment()) {
                $titleMessage = 'Participants comment news';
                $descriptionMessage = 'Have Comment of News from Participants';
            } else if ($contactNotiType == ContactNotificationType::getPosterComment()) {
                $titleMessage = 'Poster comment news';
                $descriptionMessage = 'Have Comment of News from Poster';
            }

            $notification_data = [
                self::ADMIN_NOTI_TYPE => null,
                self::BUSINESS_TYPE => BusinessTypeEnum::getNews(),
                self::CONTACT_BROADCAST_TYPE => ContactBroadcastType::getSelf(),
                self::CONTACT_NOTI_TYPE => $contactNotiType,
                self::CONTACT_ID => null,
                self::REFERENCE_ID => $referenceID,
                self::TITLE => $titleMessage,
                self::DESCRIPTION => $descriptionMessage,
                self::IMAGE => '/society/news/thumbnail/' . $data['image'],
                self::CONTACT_TYPE => ContactType::getBroadcastType(),
                self::CREATED_BY => Auth::guard('mobile')->user()->id,
            ];

            $notification = new self();
            $notification->setData($notification_data);

            if ($notification->save()) {
                //Notification Contact
                if (!empty($contactData)) {
                    foreach ($contactData as $obj) {
                        $notification_contact_data = [
                            NotificationContact::NOTIFICATION_ID => $notification->{Notification::ID},
                            NotificationContact::CONTACT_ID => $obj['contact_id'],
                        ];

                        $notification_contact = new NotificationContact();
                        $notification_contact->setData($notification_contact_data);
                        $notification_contact->save();
                    }
                }

                $dataKey = [
                    'platform' => NotificationSendToPlatform::getMobile(),
                    'type' => self::getNotiTypeByContactNotiType($notification->{self::CONTACT_NOTI_TYPE}),
                    'notification_id' => $notification->{self::ID},
                    'reference_id' => $referenceID
                ];

                $sendResponse = FCM::send(
                    "/topics/" . $topic,
                    $titleMessage,
                    $descriptionMessage,
                    $dataKey
                );
                return $sendResponse;
            }
        }
    }

    //KTV Girl Notification
    public static function ktvGirlNotification($contactNotiType, $contactID, $referenceID, $data)
    {
        $sendResponse = '';
        if (!empty($data)) {
            $titleMessage = '';
            $descriptionMessage = '';

            if ($contactNotiType == ContactNotificationType::getKtvGirlAdd()) {
                $titleMessage = 'KTV Girl Add';
                $descriptionMessage = 'You have been add to KTV ' . $data['business_name'];
            } else if ($contactNotiType == ContactNotificationType::getKtvGirlUpdate()) {
                $titleMessage = 'KTV Girl Update';
                $descriptionMessage = 'KTV Owner ' . $data['business_name'] . ' have been update your action';
            } else if ($contactNotiType == ContactNotificationType::getKtvGirlApprove()) {
                $titleMessage = 'KTV Girl Approve';
                $descriptionMessage = 'KTV Girl ' . $data['name'] . ' have been Approve';
            } else if ($contactNotiType == ContactNotificationType::getKtvGirlReject()) {
                $titleMessage = 'KTV Girl Reject';
                $descriptionMessage = 'KTV Girl ' . $data['name'] . ' have been Reject';
            } else {
                return 'Invalid Contact Noti Type';
            }

            $notification_data = [
                self::ADMIN_NOTI_TYPE => null,
                self::BUSINESS_TYPE => BusinessTypeEnum::getKtv(),
                self::CONTACT_BROADCAST_TYPE => ContactBroadcastType::getSelf(),
                self::CONTACT_NOTI_TYPE => $contactNotiType,
                self::CONTACT_ID => $contactID,
                self::REFERENCE_ID => $referenceID,
                self::TITLE => $titleMessage,
                self::DESCRIPTION => $descriptionMessage,
                self::IMAGE => '/ktv/logo/' . $data['business_image'],
                self::CONTACT_TYPE => ContactType::getSingleType(),
                self::CREATED_BY => Auth::guard('mobile')->user()->id,
            ];
            $notification = new self();
            $notification->setData($notification_data);

            if ($notification->save()) {
                $contactDeviceData = ContactDevice::where(ContactDevice::CONTACT_ID, $contactID)
                    ->whereNotNull(ContactDevice::FCM_TOKEN)
                    ->where(ContactDevice::IS_LOGIN, IsContactLogin::getYes())
                    ->get();

                foreach ($contactDeviceData as $item) {
                    $dataKey = [
                        'platform' => NotificationSendToPlatform::getMobile(),
                        'type' => self::getNotiTypeByContactNotiType($notification->{self::CONTACT_NOTI_TYPE}),
                        'notification_id' => $notification->{self::ID},
                        'reference_id' => $referenceID
                    ];

                    $sendResponse = FCM::send(
                        $item[ContactDevice::FCM_TOKEN],
                        $titleMessage,
                        $descriptionMessage,
                        $dataKey
                    );
                }
            }
        }
        return $sendResponse;
    }

    // KTV Notification
    public static function ktvShopNotification($contactNotiType, $contactID, $referenceID, $data)
    {
        $sendResponse = '';
        if (!empty($data)) {
            $titleMessage = '';
            $descriptionMessage = '';

            if ($contactNotiType == ContactNotificationType::getKtvBooking()) {
                $titleMessage = 'KTV Shop Booking';
                $descriptionMessage = 'Your KTV Shop ' . $data['business_name'] . ' have been booking by ' . $data['customer_name'];
            } else if ($contactNotiType == ContactNotificationType::getKtvApprove()) {
                $titleMessage = 'KTV Booking Have Approve';
                $descriptionMessage = 'Your KTV Shop Booking ' . $data['business_name'] . ' have been Approve';
            } else if ($contactNotiType == ContactNotificationType::getKtvAddPayment()) {
                $titleMessage = 'KTV Confirm Booking';
                $descriptionMessage = 'Your KTV Booking ' . $data['business_name'] . ' have been Confirm Please Add Payment';
            } else if ($contactNotiType == ContactNotificationType::getKtvAuditingPayment()) {
                $titleMessage = 'KTV Auditing Payment';
                $descriptionMessage = 'Customer ' . $data['customer_name'] . ' have been Add Payment Please Check';
            } else if ($contactNotiType == ContactNotificationType::getKtvReject()) {
                $titleMessage = 'KTV Reject Booking';
                $descriptionMessage = 'Your KTV Booking ' . $data['business_name'] . ' have been Reject';
            } else if ($contactNotiType == ContactNotificationType::getKtvCancel()) {
                $titleMessage = 'KTV Cancel Booking';
                $descriptionMessage = 'Customer ' . $data['customer_name'] . ' have been Cancel Booking';
            } else if ($contactNotiType == ContactNotificationType::getKtvRejectPayment()) {
                $titleMessage = 'KTV Reject Payment Booking';
                $descriptionMessage = 'Your Payment at ' . $data['business_name'] . ' have been Reject Please Check';
            } else if ($contactNotiType == ContactNotificationType::getKtvForKtvGirl()) {
                $titleMessage = 'KTV Have Approve';
                $descriptionMessage = 'Your have new booking in ' . $data['business_name'] . ' Please Check';
            } else {
                return 'Invalid Contact Noti Type';
            }

            $notification_data = [
                self::ADMIN_NOTI_TYPE => null,
                self::BUSINESS_TYPE => BusinessTypeEnum::getKtv(),
                self::CONTACT_BROADCAST_TYPE => ContactBroadcastType::getSelf(),
                self::CONTACT_NOTI_TYPE => $contactNotiType,
                self::CONTACT_ID => $contactID,
                self::REFERENCE_ID => $referenceID,
                self::TITLE => $titleMessage,
                self::DESCRIPTION => $descriptionMessage,
                self::IMAGE => '/ktv/logo/' . $data['business_image'],
                self::CONTACT_TYPE => ContactType::getSingleType(),
                self::CREATED_BY => Auth::guard('mobile')->user()->id,
            ];
            $notification = new self();
            $notification->setData($notification_data);

            if ($notification->save()) {
                $contactDeviceData = ContactDevice::where(ContactDevice::CONTACT_ID, $contactID)
                    ->whereNotNull(ContactDevice::FCM_TOKEN)
                    ->where(ContactDevice::IS_LOGIN, IsContactLogin::getYes())
                    ->get();

                foreach ($contactDeviceData as $item) {
                    $dataKey = [
                        'platform' => NotificationSendToPlatform::getMobile(),
                        'type' => self::getNotiTypeByContactNotiType($notification->{self::CONTACT_NOTI_TYPE}),
                        'notification_id' => $notification->{self::ID},
                        'reference_id' => $referenceID
                    ];

                    $sendResponse = FCM::send(
                        $item[ContactDevice::FCM_TOKEN],
                        $titleMessage,
                        $descriptionMessage,
                        $dataKey
                    );
                }
            }
        }
        return $sendResponse;
    }
}
