<?php


namespace App\Http\Controllers\Mobile\Modules\RealEstate;

use Carbon\Carbon;
use App\Models\Contact;
use App\Models\Business;
use App\Models\PrefixCode;
use App\Models\Transaction;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Models\PropertyAsset;
use App\Models\GeneralSetting;
use App\Enums\Types\AgencyType;
use App\Enums\Types\AppTypeEnum;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use App\Models\PropertyCommission;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\BusinessStatus;
use App\Enums\Types\CommissionType;
use App\Http\Controllers\Controller;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\TransactionStatus;
use App\Enums\Types\PropertyAssetStatus;
use App\Enums\Types\ContactNotificationType;
use App\Models\BusinessType;

class SalePropertyAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    //Property Booking
    public function propertyBooking(Request $request)
    {
        $this->validate($request, [
            'business_type_id' => 'required|exists:business_type,id',
            'property_id' => 'required|exists:business,id',
            'property_asset_id' => !empty($request->input('property_asset_id')) ? 'required|exists:property_asset,id' : 'nullable',
            'property_owner_id' => 'required|exists:contact,id',
            'customer_id' => 'required',
            'customer_name' => 'required',
            'customer_phone' => 'required',
            'customer_id_card' => 'required',
            'bank_account_id' => 'required',
            'transaction_date' => 'required',
            'expire_date' => 'required',
            'total_amount' => 'required',
            'sell_amount' => 'required',
            'image' => 'required',
        ]);

        DB::beginTransaction();

        //Check can book or not
        $businessData = Business::find($request->input('property_id'));
        if (!empty($request->input('property_asset_id'))) {
            //Check can book or not (Multi Property)
            $propertyAssetData = PropertyAsset::find($request->input('property_asset_id'));
            if ($propertyAssetData->{PropertyAsset::STATUS} != PropertyAssetStatus::getNotBooking()) {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }

            //Set Notification Data Name
            $notificationDataName = $businessData->{Business::NAME} . ' - ' . $propertyAssetData->{PropertyAsset::CODE};
        } else {
            //Check can book or not (Single Property)
            if ($businessData->{Business::STATUS} != BusinessStatus::getApproved()) {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }

            //Set Notification Data Name
            $notificationDataName = $businessData->{Business::NAME};
        }

        //Transaction Fee and Amount
        $transactionFee = Business::getAppFeeByBusinessID($request->input('property_id'));
        $transactionFeeAmount = StringHelper::getTransactionFeeAmount($request->input('sell_amount'), $transactionFee);

        //Merge Request Some Value
        $request->merge([
            Transaction::APP_TYPE_ID => AppTypeEnum::getProperty(),
            Transaction::BUSINESS_ID => $request->input('property_id'),
            Transaction::BUSINESS_OWNER_ID => $request->input('property_owner_id'),
            Transaction::TRANSACTION_FEE => $transactionFee,
            Transaction::TRANSACTION_FEE_AMOUNT => $transactionFeeAmount,
            Transaction::CODE => PrefixCode::getAutoCodeByBusiness(Transaction::TABLE_NAME, PrefixCode::TRANSACTION, $request->input('property_id')),
        ]);

        //Set Data
        $transaction = new Transaction();
        $transaction->setData($request);
        $transaction->{Transaction::STATUS} = TransactionStatus::getPending();
        $transaction->{Transaction::CREATED_AT} = Carbon::now();

        if ($transaction->save()) {
            //Upload Transaction Image
            $image = StringHelper::uploadImage($request->input(Transaction::IMAGE), ImagePath::propertyBookingTransaction);
            $transaction->{Transaction::IMAGE} = $image;
            $transaction->save();

            //Set status for property or asset to booking
            if (!empty($request->input('property_asset_id'))) {
                //Multi Property
                $propertyAssetData->{PropertyAsset::STATUS} = PropertyAssetStatus::getBooking();
                $propertyAssetData->save();
            } else {
                //Single Property
                $businessData->{Business::STATUS} = BusinessStatus::getBooking();
                $businessData->save();
            }

            //Send notification
            $notificationData['name'] = $notificationDataName;
            $sendResponse = Notification::propertyNotification(
                ContactNotificationType::getPropertyBooking(),
                $transaction->{Transaction::BUSINESS_OWNER_ID},
                $transaction->{Transaction::ID},
                $notificationData
            );
            info('Mobile Notification Property Booking: ' . $sendResponse);

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    //Set Commission For Agency, Sub Agency
    private function setPropertyCommission($transaction, $business, $propertyAssetID = null, $setReferral = true, $setSaleAssistance = true)
    {
        //Agency Data
        $agencyID = $transaction->{Transaction::CUSTOMER_ID};
        $agencyData = Contact::find($agencyID);

        //Commission Percentage and Amount
        $commission = $business->{Business::AGENCY_COMMISSION};
        $commissionType = $business->{Business::AGENCY_COMMISSION_TYPE};
        if ($commission > 0) {
            $commissionAmount = $commission;
            if ($commissionType == CommissionType::getPercentage()) {
                $commissionAmount = StringHelper::getCommissionAmount($transaction->{Transaction::SELL_AMOUNT}, $commission);
            }
            $propertyCommissionData = [
                PropertyCommission::TRANSACTION_ID => $transaction->{Transaction::ID},
                PropertyCommission::PROPERTY_ID => $transaction->{Transaction::BUSINESS_ID},
                PropertyCommission::AGENCY_ID => $agencyID,
                PropertyCommission::AGENCY_TYPE => AgencyType::getBase(),
                PropertyCommission::PROPERTY_ASSET_ID => $propertyAssetID,
                PropertyCommission::COMMISSION => $commission,
                PropertyCommission::COMMISSION_TYPE => $commissionType,
                PropertyCommission::COMMISSION_AMOUNT => $commissionAmount,
                PropertyCommission::WITHDRAWN_AMOUNT => 0,
            ];

            $property_commission = new PropertyCommission();
            $property_commission->setData($propertyCommissionData);
            $property_commission->{PropertyCommission::CREATED_AT} = Carbon::now();

            if ($property_commission->save()) {
                //Set Commission for referral agency
                if ($setReferral && $agencyData->hasReferralAgency()) {
                    //Commission Percentage
                    $commission = $business->{Business::REF_AGENCY_COMMISSION};
                    $commissionType = $business->{Business::REF_AGENCY_COMMISSION_TYPE};
                    if ($commission > 0) {
                        $commissionAmount = $commission;
                        if ($commissionType == CommissionType::getPercentage()) {
                            $commissionAmount = StringHelper::getCommissionAmount($transaction->{Transaction::SELL_AMOUNT}, $commission);
                        }
                        $propertyCommissionData[PropertyCommission::AGENCY_ID] = $agencyData->{Contact::REFERRAL_AGENCY_ID};
                        $propertyCommissionData[PropertyCommission::AGENCY_TYPE] = AgencyType::getReferral();
                        $propertyCommissionData[PropertyCommission::COMMISSION] = $commission;
                        $propertyCommissionData[PropertyCommission::COMMISSION_TYPE] = $commissionType;
                        $propertyCommissionData[PropertyCommission::COMMISSION_AMOUNT] = $commissionAmount;

                        $property_commission = new PropertyCommission();
                        $property_commission->setData($propertyCommissionData);
                        $property_commission->{PropertyCommission::CREATED_AT} = Carbon::now();
                        $property_commission->save();
                    }
                }

                //Set Commission for sale assistance
                if ($setSaleAssistance && !empty($business->{Business::SALE_ASSISTANCE_ID})) {
                    //Commission Percentage
                    $commission = $business->{Business::SALE_ASSISTANCE_COMMISSION};
                    $commissionType = $business->{Business::SALE_ASSISTANCE_COMMISSION_TYPE};
                    if ($commission > 0) {
                        $commissionAmount = $commission;
                        if ($commissionType == CommissionType::getPercentage()) {
                            $commissionAmount = StringHelper::getCommissionAmount($transaction->{Transaction::SELL_AMOUNT}, $commission);
                        }
                        $propertyCommissionData[PropertyCommission::AGENCY_ID] = $business->{Business::SALE_ASSISTANCE_ID};
                        $propertyCommissionData[PropertyCommission::AGENCY_TYPE] = AgencyType::getSaleAssistance();
                        $propertyCommissionData[PropertyCommission::COMMISSION] = $commission;
                        $propertyCommissionData[PropertyCommission::COMMISSION_TYPE] = $commissionType;
                        $propertyCommissionData[PropertyCommission::COMMISSION_AMOUNT] = $commissionAmount;

                        $property_commission = new PropertyCommission();
                        $property_commission->setData($propertyCommissionData);
                        $property_commission->{PropertyCommission::CREATED_AT} = Carbon::now();
                        $property_commission->save();
                    }
                }
            }
        }
    }

    //Sale List
    public function getSaleListProperty(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.business_owner_id' => !empty($request->input('business_owner_id')) ? 'required|exists:contact,id' : 'nullable',
            'filter.agency_id' => !empty($request->input('agency_id')) ? 'required|exists:contact,id' : 'nullable',
            'filter.sale_assistance_id' => !empty($request->input('sale_assistance_id')) ? 'required|exists:contact,id' : 'nullable',
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');
        $filter = $request->input('filter');
        $sort = $request->input('sort');

        $data = Transaction::listPropertyAndAsset($filter, $sort)->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    //Sale Detail
    public function getSaleDetailProperty(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.sale_id' => 'required|exists:transaction,id',
        ]);

        $filter = $request->input('filter');

        $data = Transaction::listPropertyAndAsset($filter)->first();

        return $this->responseWithData($data);
    }

    //Change Sale Property Status
    public function changeStatusSaleProperty(Request $request)
    {
        //Check Validation
        $this->validate($request, [
            'sale_id' => 'required|exists:transaction,id',
            'status' => 'required|numeric|min:2|max:5',
            'remark' => $request->input('status') == TransactionStatus::getRejected() || $request->input('status') == TransactionStatus::getCancelled() ? 'required' : 'nullable'
        ]);

        DB::beginTransaction();

        //Current Request Sale Data
        $saleID = $request->input('sale_id');
        $statusRequest = $request->input(Transaction::STATUS);

        //Get Old Sale Data
        $transaction = Transaction::find($saleID);
        $oldStatusInDB = $transaction->{Transaction::STATUS};

        //Get Sale with property data
        $filter['sale_id'] = $saleID;
        $salePropertyData = Transaction::listPropertyAndAsset($filter)->first();

        //Check validation status
        if ($oldStatusInDB == TransactionStatus::getPending()) {
            //Only Status Pending
            if ($statusRequest != TransactionStatus::getApproved() &&
                $statusRequest != TransactionStatus::getRejected() &&
                $statusRequest != TransactionStatus::getCancelled()
            ) {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else if ($oldStatusInDB == TransactionStatus::getApproved()) {
            //Only Status Approved
            if ($statusRequest != TransactionStatus::getCompleted() &&
                $statusRequest != TransactionStatus::getCancelled()
            ) {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }

        //Change to current status
        $transaction->{Transaction::STATUS} = $statusRequest;
        if ($transaction->save()) {
            $contactNotiType = 0;
            $contactIDForNotification = $transaction->{Transaction::CUSTOMER_ID};

            //Set property or asset status by request change status
            $business = Business::find($transaction->{Transaction::BUSINESS_ID});
            if ($transaction->{Transaction::STATUS} == TransactionStatus::getCompleted()) {
                //Set Notification Data
                $contactNotiType = ContactNotificationType::getPropertyBookingCompleted();

                //Set status to sold out for multi or single property
                if (!empty($transaction->{Transaction::PROPERTY_ASSET_ID})) {
                    //Multi Property
                    $propertyAsset = PropertyAsset::find($transaction->{Transaction::PROPERTY_ASSET_ID});
                    $propertyAsset->{PropertyAsset::STATUS} = PropertyAssetStatus::getCompletedBooking();
                    $propertyAsset->save();
                } else {
                    //Single Property
                    $business->{Business::STATUS} = BusinessStatus::getCompletedBooking();
                    $business->save();
                }

                //Set commission for base agency and referral agency
                $this->setPropertyCommission(
                    $transaction,
                    $business,
                    $transaction->{Transaction::PROPERTY_ASSET_ID},
                    true,
                    true,
                );
            } else if ($transaction->{Transaction::STATUS} == TransactionStatus::getRejected()) {
                //Set Remark
                $transaction->{Transaction::REMARK} = $request->input('remark');
                $transaction->save();

                //Set Notification Data
                $contactNotiType = ContactNotificationType::getPropertyBookingRejected();

                //Set status to can booking for multi or single property
                if (!empty($transaction->{Transaction::PROPERTY_ASSET_ID})) {
                    //Multi Property
                    $propertyAsset = PropertyAsset::find($transaction->{Transaction::PROPERTY_ASSET_ID});
                    $propertyAsset->{PropertyAsset::STATUS} = PropertyAssetStatus::getNotBooking();
                    $propertyAsset->save();
                } else {
                    //Single Property
                    $business->{Business::STATUS} = BusinessStatus::getApproved();
                    $business->save();
                }

            } else if ($transaction->{Transaction::STATUS} == TransactionStatus::getCancelled()) {
                //Set Remark
                $transaction->{Transaction::REMARK} = $request->input('remark');
                $transaction->save();

                //Set Notification Data
                $contactNotiType = ContactNotificationType::getPropertyBookingCancelled();
                $contactIDForNotification = $transaction->{Transaction::BUSINESS_OWNER_ID};

                //Set status to can booking for multi or single property
                if (!empty($transaction->{Transaction::PROPERTY_ASSET_ID})) {
                    //Multi Property
                    $propertyAsset = PropertyAsset::find($transaction->{Transaction::PROPERTY_ASSET_ID});
                    $propertyAsset->{PropertyAsset::STATUS} = PropertyAssetStatus::getNotBooking();
                    $propertyAsset->save();
                } else {
                    //Single Property
                    $business->{Business::STATUS} = BusinessStatus::getApproved();
                    $business->save();
                }

            } else if ($transaction->{Transaction::STATUS} == TransactionStatus::getApproved()) {
                //Set Notification Data
                $contactNotiType = ContactNotificationType::getPropertyBookingApproved();

                //Upload sign image if user not upload sign image
                $contact = Contact::find($transaction->{Transaction::BUSINESS_OWNER_ID});
                if (!empty($contact) && empty($contact->signature_image)) {
                    if (!empty($request->input('signature_image'))) {
                        $contact->signature_image = StringHelper::uploadImage($request->input('signature_image'), ImagePath::contactSignatureImagePath);
                        $contact->save();
                    }
                }
            }

            //Set Base Agency 50% of booking amount when already confirmed and agency cancelled
            if ($oldStatusInDB == TransactionStatus::getApproved() && $statusRequest == TransactionStatus::getCancelled()) {
                //Delete old commission
                PropertyCommission::where(PropertyCommission::TRANSACTION_ID, $transaction->{Transaction::ID})->delete();

                //Add New Commission
                $business->{Business::AGENCY_COMMISSION} = 50; //50%
                $business->{Business::AGENCY_COMMISSION_TYPE} = CommissionType::getPercentage(); //%
                $this->setPropertyCommission(
                    $transaction,
                    $business,
                    $transaction->{Transaction::PROPERTY_ASSET_ID},
                    false,
                    false,
                );
            }

            //Send notification
            $sendResponse = Notification::propertyNotification(
                $contactNotiType,
                $contactIDForNotification,
                $transaction->{Transaction::ID},
                $salePropertyData
            );
            info('Mobile Notification Change Status Sale Property: ' . $sendResponse);
        }

        DB::commit();

        return $this->responseWithSuccess();
    }
}
