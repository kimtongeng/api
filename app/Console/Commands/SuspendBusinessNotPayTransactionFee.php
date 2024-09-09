<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Contact;
use App\Models\Transaction;
use App\Models\Notification;
use App\Models\GeneralSetting;
use Illuminate\Console\Command;
use App\Models\TransactionPayment;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\IsBusinessOwner;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\TransactionFeeStatus;
use App\Enums\Types\ContactNotificationType;

class SuspendBusinessNotPayTransactionFee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suspendBusinessNotPayTransactionFee';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Suspend business when owner not yet pay transaction fee";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $deadline = GeneralSetting::getTransactionPaymentDeadline();

        // if (!empty($deadline)) $deadline = Carbon::createFromFormat('d', $deadline)->format('d');
        // if (!empty($deadline)) $deadline = Carbon::parse($deadline)->format('d');
        $today = Carbon::today()->format('d');
        $lastDayOfMonth = Carbon::today()->lastOfMonth()->format('d');
        $currentMonth = Carbon::today()->format('m');

        // When deadline equal 31 or 30
        if ($deadline == 31 || $deadline == 30) {
            $deadline = $lastDayOfMonth;
        }

        // When current month equal february and deadline equal 29 or 28
        if (($deadline == 29 || $deadline == 28) && $currentMonth == 02) {
            $deadline = $lastDayOfMonth;
        }

        if ($deadline == $today) {

            $filter = [
                'status' => TransactionFeeStatus::getBusinessNotYetPay(),
                'group_by_month' => false,
            ];
            $transactionFeeList = Transaction::listTransactionFeeCommon($filter)
                ->orderBy('transaction.id', "desc")
                ->get();
            $transactionFeeListUnique = collect($transactionFeeList)->unique(function ($item) {
                return $item['business_type_id '] . $item['business_owner_id'];
            });

            //Suspend Business By Type
            foreach ($transactionFeeListUnique as $obj) {
                $contact = Contact::find($obj->{Transaction::BUSINESS_OWNER_ID});

                if (!empty($contact)) {
                    if ($obj->{Transaction::BUSINESS_TYPE_ID} == BusinessTypeEnum::getProperty()) {
                        $contact->{Contact::IS_PROPERTY_OWNER} = IsBusinessOwner::getSuspend();
                    } else if ($obj->{Transaction::BUSINESS_TYPE_ID} == BusinessTypeEnum::getAccommodation()) {
                        $contact->{Contact::IS_HOTEL_OWNER} = IsBusinessOwner::getSuspend();
                    } else if ($obj->{Transaction::BUSINESS_TYPE_ID} == BusinessTypeEnum::getDelivery()) {
                        $contact->{Contact::IS_DRIVER} = IsBusinessOwner::getSuspend();
                    } else if ($obj->{Transaction::BUSINESS_TYPE_ID} == BusinessTypeEnum::getShopRetail() ||
                        $obj->{Transaction::BUSINESS_TYPE_ID} == BusinessTypeEnum::getShopWholesale() ||
                        $obj->{Transaction::BUSINESS_TYPE_ID} == BusinessTypeEnum::getRestaurant() ||
                        $obj->{Transaction::BUSINESS_TYPE_ID} == BusinessTypeEnum::getShopLocalProduct() ||
                        $obj->{Transaction::BUSINESS_TYPE_ID} == BusinessTypeEnum::getService() ||
                        $obj->{Transaction::BUSINESS_TYPE_ID} == BusinessTypeEnum::getModernCommunity()
                    ) {
                        $contact->{Contact::IS_SELLER} = IsBusinessOwner::getSuspend();
                    } else if ($obj->{Transaction::BUSINESS_TYPE_ID} == BusinessTypeEnum::getMassage()) {
                        $contact->{Contact::IS_MASSAGE_OWNER} = IsBusinessOwner::getSuspend();
                    } else if ($obj->{Transaction::BUSINESS_TYPE_ID} == BusinessTypeEnum::getAttraction()) {
                        $contact->{Contact::IS_ATTRACTION_OWNER} = IsBusinessOwner::getSuspend();
                    }

                    if ($contact->save()) {
                        Notification::appBlockedNotification(
                            $obj->{Transaction::BUSINESS_TYPE_ID},
                            $obj->{Transaction::BUSINESS_OWNER_ID},
                            ContactNotificationType::getAppBlocked()
                        );
                    }
                }
            }

            info('Suspend Business Not Pay Transaction Fee CronJob: ' . Carbon::today()->format('Y-m-d'));
        }
    }
}
