<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessShareContact extends Model
{

    const TABLE_NAME = 'business_share_contact';
    const ID = 'id';
    const BUSINESS_ID = 'business_id';
    const CONTACT_ID = 'contact_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    //Set Data
    public function setData($data)
    {
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
    }

    /*
     * Relationship Area
     * */
    //Business Contact Permission Relationship
    public function businessContactPermission()
    {
        return $this->hasMany(BusinessContactPermission::class, BusinessContactPermission::BUSINESS_SHARE_CONTACT_ID, self::ID)
            ->join('business_permission', 'business_permission.id', 'business_contact_permission.business_permission_id');
    }

    //list
    public static function lists($filter = [])
    {
        $contactID = isset($filter['contact_id']) ? $filter['contact_id'] : null;
        $businessID = isset($filter['business_id']) ? $filter['business_id'] : null;

        return self::select(
            'business_share_contact.id',
            'business_share_contact.business_id',
            'contact.id as contact_id',
            'contact.fullname as contact_name',
            'contact.code as contact_code',
            'contact.profile_image as contact_profile_image'
        )
            ->join('contact', 'contact.id', 'business_share_contact.contact_id')
            ->when($contactID, function ($query) use ($contactID) {
                $query->where('business_share_contact.contact_id', $contactID);
            })
            ->when($businessID, function ($query) use ($businessID) {
                $query->where('business_share_contact.business_id', $businessID);
            })
            ->with([
                'businessContactPermission' => function ($query) {
                    $query->select(
                        'business_contact_permission.*',
                        'business_permission.business_type_id',
                        'business_permission.name',
                        'business_permission.action'
                    )
                        ->get();
                }
            ])
            ->orderBy('business_share_contact.created_at', 'DESC');
    }


    // Find Contact Share By owner business
    public static function getContactSharePermission($business_id, $action='')
    {
        return self::join('business_contact_permission', 'business_share_contact.id', 'business_contact_permission.business_share_contact_id')
            ->join('business_permission', 'business_permission.id', 'business_contact_permission.business_permission_id')
            ->select(
                'business_share_contact.*',
                'business_permission.action',
            )
            ->where('business_share_contact.business_id', $business_id)
            ->where('business_permission.action', $action)
            ->get();
    }

}
