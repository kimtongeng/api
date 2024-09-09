<?php

namespace App\Http\Controllers\Admin\Common;


use App\Enums\Types\BusinessStatus;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\IsBusinessOwner;
use App\Enums\Types\TransactionFeeStatus;
use App\Models\Business;
use App\Models\Contact;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    const MODULE_KEY = 'dashboard';

    /**
     * Not Yet Done: https://prnt.sc/lmonxGye0R8w
     */

    //Get All Data
    public function getAllData()
    {
        //Recent Real Estate Data
        $recentRealEstate = Business::listProperty(['is_admin_request' => true], 'created_at')->limit(20)->get();

        $response = [
            'count_user_data' => $this->getCountUserData(),
            'count_transaction_fee_data' => $this->getTransactionFeeCount(),
            'recent_real_estate_data' => $recentRealEstate,
            'count_owner_by_business_type' => $this->countOwnerByBusinessType(),
            'count_business_by_business_type' => $this->countBusinessByBusinessType(),
            'count_agency_in_business_real_estate' => $this->countAgencyInBusinessRealEstate(),
            'count_status_business_real_estate' => $this->countStatusBusinessRealEstate()
        ];
        return $this->responseWithData($response);
    }

    //Count User
    private function getCountUserData()
    {
        $totalContact = Contact::getComboList()->count();
        $totalContactCurrentMonth = Contact::whereBetween('created_at', [Carbon::today()->firstOfMonth(), Carbon::today()->lastOfMonth()])->get()->count();
        $totalBlockContact = Contact::where('is_seller', IsBusinessOwner::getSuspend())
            ->orWhere('is_agency', IsBusinessOwner::getSuspend())
            ->orWhere('is_property_owner', IsBusinessOwner::getSuspend())
            ->orWhere('is_hotel_owner', IsBusinessOwner::getSuspend())
            ->orWhere('is_driver', IsBusinessOwner::getSuspend())
            ->orWhere('is_news', IsBusinessOwner::getSuspend())
            ->orWhere('is_sale_assistance', IsBusinessOwner::getSuspend())
            ->count();

        return [
            'total_contact' => $totalContact,
            'total_contact_current_month' => $totalContactCurrentMonth,
            'total_block_contact' => $totalBlockContact
        ];
    }

    //Count Transaction Fee
    private function getTransactionFeeCount()
    {
        $grandTotal = Transaction::join(
            DB::raw('(
                SELECT
                    ct.id AS business_owner_id,
                    ct.fullname AS business_owner_name,
                    bs.business_type_id,
                    bt.name AS business_type_name
                FROM
                    contact ct
                    JOIN business bs ON ct.id = bs.contact_id
                    JOIN business_type bt ON bt.id = bs.business_type_id
                GROUP BY
                    bs.business_type_id,
                    bs.contact_id
                ) ct_gb_bt'),
            function ($join) {
                $join->on('ct_gb_bt.business_owner_id', '=', 'transaction.business_owner_id')
                    ->on('ct_gb_bt.business_type_id', '=', 'transaction.business_type_id');
            }
        )
            ->where('transaction.business_type_id', BusinessTypeEnum::getProperty())
            ->sum('transaction.transaction_fee_amount');

        $totalPaidAmount = Transaction::join(
            DB::raw('(
                SELECT
                    ct.id AS business_owner_id,
                    ct.fullname AS business_owner_name,
                    bs.business_type_id,
                    bt.name AS business_type_name
                FROM
                    contact ct
                    JOIN business bs ON ct.id = bs.contact_id
                    JOIN business_type bt ON bt.id = bs.business_type_id
                GROUP BY
                    bs.business_type_id,
                    bs.contact_id
                ) ct_gb_bt'),
            function ($join) {
                $join->on('ct_gb_bt.business_owner_id', '=', 'transaction.business_owner_id')
                    ->on('ct_gb_bt.business_type_id', '=', 'transaction.business_type_id');
            }
        )
            ->where('transaction.business_type_id', BusinessTypeEnum::getProperty())
            ->where('transaction.transaction_fee_status', TransactionFeeStatus::getBusinessPaid())
            ->sum('transaction.transaction_fee_amount');

        $totalOutstandingAmount = Transaction::join(
            DB::raw('(
                SELECT
                    ct.id AS business_owner_id,
                    ct.fullname AS business_owner_name,
                    bs.business_type_id,
                    bt.name AS business_type_name
                FROM
                    contact ct
                    JOIN business bs ON ct.id = bs.contact_id
                    JOIN business_type bt ON bt.id = bs.business_type_id
                GROUP BY
                    bs.business_type_id,
                    bs.contact_id
                ) ct_gb_bt'),
            function ($join) {
                $join->on('ct_gb_bt.business_owner_id', '=', 'transaction.business_owner_id')
                    ->on('ct_gb_bt.business_type_id', '=', 'transaction.business_type_id');
            }
        )
            ->where('transaction.business_type_id', BusinessTypeEnum::getProperty())
            ->where('transaction.transaction_fee_status', TransactionFeeStatus::getBusinessNotYetPay())
            ->sum('transaction.transaction_fee_amount');

        return [
            'grand_total' => $grandTotal,
            'total_paid_amount' => $totalPaidAmount,
            'total_outstanding_amount' => $totalOutstandingAmount
        ];

    }

    //Count Owner By Business Type
    private function countOwnerByBusinessType()
    {
        $count = DB::SELECT("
        SELECT
            SUM( CASE WHEN bst.business_type_id = '" . BusinessTypeEnum::getProperty() . "' THEN 1 ELSE 0 END ) AS real_estate,
            SUM( CASE WHEN bst.business_type_id = '" . BusinessTypeEnum::getAccommodation() . "' THEN 1 ELSE 0 END ) AS hotel,
            SUM( CASE WHEN bst.business_type_id = '" . BusinessTypeEnum::getShopRetail() . "' THEN 1 ELSE 0 END ) AS retail,
            SUM( CASE WHEN bst.business_type_id = '" . BusinessTypeEnum::getShopWholesale() . "' THEN 1 ELSE 0 END ) AS wholesale,
            SUM( CASE WHEN bst.business_type_id = '" . BusinessTypeEnum::getRestaurant() . "' THEN 1 ELSE 0 END ) AS food,
            SUM( CASE WHEN bst.business_type_id = '" . BusinessTypeEnum::getDelivery() . "' THEN 1 ELSE 0 END ) AS delivery,
            SUM( CASE WHEN bst.business_type_id = '" . BusinessTypeEnum::getAttraction() . "' THEN 1 ELSE 0 END ) AS attraction,
            SUM( CASE WHEN bst.business_type_id = '" . BusinessTypeEnum::getNews() . "' THEN 1 ELSE 0 END ) AS society
        FROM
            ( SELECT bs.contact_id, bs.business_type_id FROM business bs WHERE bs.deleted_at IS NULL GROUP BY bs.business_type_id, bs.contact_id ) bst
        ");

        return $count[0];
    }

    //Count Business By Business Type
    private function countBusinessByBusinessType(){
        $count = DB::SELECT("
        SELECT
            SUM( CASE WHEN bst.business_type_id = '" . BusinessTypeEnum::getProperty() . "' THEN 1 ELSE 0 END ) AS real_estate,
            SUM( CASE WHEN bst.business_type_id = '" . BusinessTypeEnum::getAccommodation() . "' THEN 1 ELSE 0 END ) AS hotel,
            SUM( CASE WHEN bst.business_type_id = '" . BusinessTypeEnum::getShopRetail() . "' THEN 1 ELSE 0 END ) AS retail,
            SUM( CASE WHEN bst.business_type_id = '" . BusinessTypeEnum::getShopWholesale() . "' THEN 1 ELSE 0 END ) AS wholesale,
            SUM( CASE WHEN bst.business_type_id = '" . BusinessTypeEnum::getRestaurant() . "' THEN 1 ELSE 0 END ) AS food,
            SUM( CASE WHEN bst.business_type_id = '" . BusinessTypeEnum::getDelivery() . "' THEN 1 ELSE 0 END ) AS delivery,
            SUM( CASE WHEN bst.business_type_id = '" . BusinessTypeEnum::getAttraction() . "' THEN 1 ELSE 0 END ) AS attraction,
            SUM( CASE WHEN bst.business_type_id = '" . BusinessTypeEnum::getNews() . "' THEN 1 ELSE 0 END ) AS society
        FROM
            ( SELECT bs.business_type_id FROM business bs WHERE bs.deleted_at IS NULL GROUP BY bs.id, bs.business_type_id ) bst
        ");

        return $count[0];
    }

    //Count Agency In Business Real Estate
    private function countAgencyInBusinessRealEstate(){
        $count = Business::join('contact', 'contact.id', 'business.contact_id')
            ->where('business.business_type_id', BusinessTypeEnum::getProperty())
            ->where('contact.is_agency', IsBusinessOwner::getYes())
            ->groupBy('business.business_type_id', 'business.contact_id')
            ->count('business.contact_id');

        return $count;
    }

    //Count Status Business Real Estate
    private function countStatusBusinessRealEstate() {
        $selling = Business::where('status', BusinessStatus::getApproved())
            ->where('business_type_id', BusinessTypeEnum::getProperty())
            ->count();
        $booking = Business::where('status', BusinessStatus::getBooking())
            ->where('business_type_id', BusinessTypeEnum::getProperty())
            ->count();
        $sold_out = Business::where('status', BusinessStatus::getCompletedBooking())
            ->where('business_type_id', BusinessTypeEnum::getProperty())
            ->count();

        return [
            'selling' => $selling,
            'booking' => $booking,
            'sold_out' => $sold_out
        ];
    }
}
