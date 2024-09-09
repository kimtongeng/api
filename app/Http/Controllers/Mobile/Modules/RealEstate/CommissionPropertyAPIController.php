<?php


namespace App\Http\Controllers\Mobile\Modules\RealEstate;

use App\Enums\Types\AgencyType;
use App\Enums\Types\ContactNotificationType;
use App\Enums\Types\PropertyCommissionWithdrawingStatus;
use App\Enums\Types\PropertyTypeEnum;
use App\Helpers\StringHelper;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessPermission;
use App\Models\Notification;
use App\Models\PropertyAsset;
use App\Models\PropertyCommission;
use App\Models\PropertyCommissionWithdrawing;
use App\Models\PropertyType;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommissionPropertyAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    //Commission Property List
    public function getCommissionPropertyList(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_owner_id' => !empty($request->input('filter.business_owner_id')) ? 'required|exists:contact,id' : 'nullable',
            'filter.agency_id' => !empty($request->input('filter.agency_id')) ? 'required|exists:contact,id' : 'nullable',
            'filter.sale_assistance_id' => !empty($request->input('filter.sale_assistance_id')) ? 'required|exists:contact,id' : 'nullable',
            'filter.property_type' => 'required'
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = $request->input('filter');

        $data = PropertyCommission::listProperty($filter)
            ->orderBy('property_commission.id', 'DESC')
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    //Commission Property Asset List
    public function getCommissionPropertyAssetList(Request $request)
    {
        $this->validate($request, [
            'property_id' => 'required|exists:business,id',
            'business_owner_id' => !empty($request->input('business_owner_id')) ? 'required|exists:contact,id' : 'nullable',
            'agency_id' => !empty($request->input('agency_id')) ? 'required|exists:contact,id' : 'nullable',
            'sale_assistance_id' => !empty($request->input('sale_assistance_id')) ? 'required|exists:contact,id' : 'nullable'
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = [
            'property_id' => $request->input('property_id'),
            'business_owner_id' => $request->input('business_owner_id'),
            'agency_id' => $request->input('agency_id'),
            'sale_assistance_id' => $request->input('sale_assistance_id'),
            'agency_type' => $request->input('agency_type')
        ];

        $data = PropertyCommission::listAsset($filter)
            ->orderBy('property_commission.id', 'DESC')
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    //Withdraw Commission Property
    public function withdrawCommissionProperty(Request $request)
    {
        //Check Validation
        $this->validate($request, [
            'property_commission_id' => 'required|exists:property_commission,id',
            'contact_id' => 'required|exists:contact,id',
            'withdraw_amount' => 'required',
            'transaction_date' => 'required',
            'transaction_image' => 'required',
        ]);

        DB::beginTransaction();

        $propertyCommission = PropertyCommission::find($request->input(PropertyCommissionWithdrawing::PROPERTY_COMMISSION_ID));
        $withdrawnAmount = floatval($propertyCommission->{PropertyCommission::WITHDRAWN_AMOUNT}) + floatval($request->input(PropertyCommissionWithdrawing::WITHDRAW_AMOUNT));

        if ($withdrawnAmount <= $propertyCommission->{PropertyCommission::COMMISSION_AMOUNT}) {
            //Set Commission History
            $propertyCommissionWithdrawing = new PropertyCommissionWithdrawing();
            $propertyCommissionWithdrawing->setData($request);
            $propertyCommissionWithdrawing->status = PropertyCommissionWithdrawingStatus::getPending();
            if ($propertyCommissionWithdrawing->save()) {
                //Upload Transaction Image
                if (!empty($request->input(PropertyCommissionWithdrawing::TRANSACTION_IMAGE))) {
                    $image = StringHelper::uploadImage($request->input(PropertyCommissionWithdrawing::TRANSACTION_IMAGE), ImagePath::propertyCommissionTransaction);
                    $propertyCommissionWithdrawing->{PropertyCommissionWithdrawing::TRANSACTION_IMAGE} = $image;
                    $propertyCommissionWithdrawing->save();
                }

                //Send Notification
                $notificationData = [];
                $businessData = Business::find($propertyCommission->{PropertyCommission::PROPERTY_ID});
                $contactNotiType = 0;
                if (!empty($propertyCommission->{PropertyCommission::PROPERTY_ASSET_ID})) {
                    //Notification
                    $contactNotiType = ContactNotificationType::getOwnerWithdrawSinglePropertyCommission();

                    //Asset
                    $propertyAssetData = PropertyAsset::find($propertyCommission->{PropertyCommission::PROPERTY_ASSET_ID});
                    $notificationData['name'] = $businessData->{Business::NAME} . ' - ' . $propertyAssetData->{PropertyAsset::CODE};
                } else {
                    //Notification
                    $contactNotiType = ContactNotificationType::getOwnerWithdrawMultiPropertyCommission();

                    //Property
                    $notificationData['name'] = $businessData->{Business::NAME};
                }

                $sendResponse = Notification::propertyNotification(
                    $contactNotiType,
                    $propertyCommission->{PropertyCommission::AGENCY_ID},
                    $propertyCommission->{PropertyCommission::ID},
                    $notificationData
                );
                info('Mobile Notification Withdraw Property Commission: ' . $sendResponse);

                $remainAmount = floatval($propertyCommission->{PropertyCommission::COMMISSION_AMOUNT}) - floatval($withdrawnAmount);
                $response = ['remain_amount' => number_format($remainAmount, 2)];

                DB::commit();

                return $this->responseWithData($response);
            } else {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    //Confirm Withdrawn Commission
    public function confirmRejectWithdrawnCommissionProperty(Request $request)
    {
        //Check Validation
        $this->validate($request, [
            'id' => 'required|exists:property_commission_withdrawing,id',
            'status' => 'required|numeric|min:1|max:2'
        ]);

        DB::beginTransaction();

        //Get Relevant Data
        $propertyWithdrawingCommission = PropertyCommissionWithdrawing::find($request->input('id'));
        $propertyCommission = PropertyCommission::find($propertyWithdrawingCommission->property_commission_id);

        //Get Notification Data
        $notificationData = [];
        $notificationLogInfo = '';
        $businessData = Business::withTrashed()->find($propertyCommission->{PropertyCommission::PROPERTY_ID});
        if (!empty($propertyCommission->{PropertyCommission::PROPERTY_ASSET_ID})) {
            //Asset
            $propertyAssetData = PropertyAsset::withTrashed()->find($propertyCommission->{PropertyCommission::PROPERTY_ASSET_ID});
            $notificationData['name'] = $businessData->{Business::NAME} . ' - ' . $propertyAssetData->{PropertyAsset::CODE};
        } else {
            //Property
            $notificationData['name'] = $businessData->{Business::NAME};
        }

        //Confirm or Reject
        if ($request->input('status') == PropertyCommissionWithdrawingStatus::getConfirmed()) {
            $withdrawnAmount = floatval($propertyCommission->{PropertyCommission::WITHDRAWN_AMOUNT}) + floatval($propertyWithdrawingCommission->{PropertyCommissionWithdrawing::WITHDRAW_AMOUNT});
            if ($withdrawnAmount <= $propertyCommission->{PropertyCommission::COMMISSION_AMOUNT}) {

                $propertyWithdrawingCommission->status = PropertyCommissionWithdrawingStatus::getConfirmed();
                if ($propertyWithdrawingCommission->save()) {
                    //Set Increase Commission
                    $propertyCommission->{PropertyCommission::WITHDRAWN_AMOUNT} = $withdrawnAmount;
                    $propertyCommission->save();

                    //Set Notification Data
                    if (!empty($propertyCommission->{PropertyCommission::PROPERTY_ASSET_ID})) {
                        $contactNotiType = ContactNotificationType::getAgencyConfirmedWithdrawnSinglePropertyCommission();
                    } else {
                        $contactNotiType = ContactNotificationType::getAgencyConfirmedWithdrawnMultiPropertyCommission();
                    }

                    $notificationLogInfo = 'Mobile Notification Confirmed Withdraw Property Commission: ';
                } else {
                    return $this->responseValidation(ErrorCode::ACTION_FAILED);
                }
            } else {
                $dataWithdrawn = PropertyCommissionWithdrawing::where('property_commission_id', $propertyCommission->id)
                    ->where('status', PropertyCommissionWithdrawingStatus::getPending())
                    ->get();
                if (!empty($dataWithdrawn)) {
                    foreach ($dataWithdrawn as $obj) {
                        $withdrawnHistoryData = PropertyCommissionWithdrawing::find($obj['id']);
                        $withdrawnHistoryData->status = PropertyCommissionWithdrawingStatus::getRejected();
                        $withdrawnHistoryData->save();

                    }
                }

                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else if ($request->input('status') == PropertyCommissionWithdrawingStatus::getRejected()) {
            $propertyWithdrawingCommission->status = PropertyCommissionWithdrawingStatus::getRejected();
            if ($propertyWithdrawingCommission->save()) {
                //Set Notification Data
                if (!empty($propertyCommission->{PropertyCommission::PROPERTY_ASSET_ID})) {
                    $contactNotiType = ContactNotificationType::getAgencyRejectedWithdrawnSinglePropertyCommission();
                } else {
                    $contactNotiType = ContactNotificationType::getAgencyRejectedWithdrawnMultiPropertyCommission();
                }
                $notificationLogInfo = 'Mobile Notification Rejected Withdraw Property Commission: ';
            } else {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else {
            return $this->responseValidation(ErrorCode::INVALID);
        }

        //Send Notification
        $sendResponse = Notification::propertyNotification(
            $contactNotiType,
            $businessData->{Business::CONTACT_ID},
            $propertyCommission->{PropertyCommission::ID},
            $notificationData
        );
        info($notificationLogInfo . $sendResponse);

        DB::commit();
    }

    //Get Withdrawn Commission Property List
    public function getWithdrawnCommissionPropertyList(Request $request)
    {
        $this->validate($request, [
            'property_commission_id' => 'required|exists:property_commission,id',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = [
            'property_commission_id' => $request->input('property_commission_id')
        ];

        $data = PropertyCommissionWithdrawing::lists($filter)->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    //Count Pending Withdrawing Property Commission
    public function countPendingWithdrawingPropertyCommission(Request $request)
    {
        $this->validate($request, [
            'current_user_id' => 'required', //Can be Property Owner, Sale Assistance, Agency
        ]);

        $currentUserID = $request->input('current_user_id');
        $propertyOwnerData = PropertyCommission::join('transaction', 'transaction.id', 'property_commission.transaction_id')
            ->join('property_commission_withdrawing', 'property_commission_withdrawing.property_commission_id', 'property_commission.id')
            ->leftjoin('business_share_contact', 'business_share_contact.business_id', 'property_commission.property_id')
            ->leftjoin('business_contact_permission', 'business_contact_permission.business_share_contact_id', 'business_share_contact.id')
            ->leftjoin('business_permission', 'business_permission.id', 'business_contact_permission.business_permission_id')
            ->where(function ($query) use ($currentUserID) {
                $query->where('transaction.business_owner_id', $currentUserID)
                    ->orWhere(function ($query) use ($currentUserID) {
                        $query->where('business_share_contact.contact_id', $currentUserID)
                            ->where('business_permission.action', BusinessPermission::VIEW_COMMISSION_LIST_PROPERTY);
                    });
            })
            ->where('property_commission_withdrawing.status', PropertyCommissionWithdrawingStatus::getPending())
            ->groupBy('property_commission_withdrawing.id')
            ->get();

        $saleAssistanceData = PropertyCommission::join('transaction', 'transaction.id', 'property_commission.transaction_id')
            ->join('property_commission_withdrawing', 'property_commission_withdrawing.property_commission_id', 'property_commission.id')
            ->where('property_commission.agency_id', $currentUserID)
            ->where('property_commission.agency_type', AgencyType::getSaleAssistance())
            ->where('property_commission_withdrawing.status', PropertyCommissionWithdrawingStatus::getPending())
            ->get();

        $agencyData = PropertyCommission::join('transaction', 'transaction.id', 'property_commission.transaction_id')
            ->join('property_commission_withdrawing', 'property_commission_withdrawing.property_commission_id', 'property_commission.id')
            ->where('property_commission.agency_id', $currentUserID)
            ->where(function ($query) {
                $query->where('property_commission.agency_type', AgencyType::getBase())
                    ->OrWhere('property_commission.agency_type', AgencyType::getReferral());
            })
            ->where('property_commission_withdrawing.status', PropertyCommissionWithdrawingStatus::getPending())
            ->get();

            $response = [
                'property_owner' => [
                    'project' =>  $propertyOwnerData->whereNotNull('property_asset_id')->count(),
                    'single' => $propertyOwnerData->whereNull('property_asset_id')->count()
                ],
                'sale_assistance' => [
                    'project' => $saleAssistanceData->whereNotNull('property_asset_id')->count(),
                    'single' => $saleAssistanceData->whereNull('property_asset_id')->count()
                ],
                'agency' => [
                    'project' => $agencyData->whereNotNull('property_asset_id')->count(),
                    'single' => $agencyData->whereNull('property_asset_id')->count()
                ],
            ];

        return $this->responseWithData($response);
    }

}
