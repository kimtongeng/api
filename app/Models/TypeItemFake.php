<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\FuncCall;

class TypeItemFake extends Model
{
    const TABLE_NAME = "type_item_fakes";
    const NAME = 'name';
    const NUMBER_ORDER = "numberOrder";
    const IMAGE = 'image';

    protected $table = self::TABLE_NAME;
    protected $fillable = [ self::NAME, self::NUMBER_ORDER, self::IMAGE];

    public function setData($data){
        $this->{self::NAME} = $data[self::NAME];
        $this->{self::NUMBER_ORDER} = $data[self::NUMBER_ORDER];
    }

    public static function getList($sort_by,$sort_type,$filter){
        // return self::select("id","name","image","created_at","numberOrder")->orderBy($sort_by,$sort_type);
        $search = isset($filter["search"]) ? $filter["search"] : null;
        $dateRage = isset($filter["datePicker"]) ? $filter["datePicker"] : null;
        $typeName = isset($filter["typeName"]) ? $filter["typeName"]["name"] : null;
        $maxOrderNumber = isset($filter["max"]) ? $filter["max"] : null;
        $minOrderNumber = isset($filter["min"]) ? $filter["min"]: null;
        $afterTime = isset($filter["afterTime"]) ? $filter["afterTime"] : null;
        $beforeTime = isset($filter["beforeTime"]) ? $filter["beforeTime"]:null;

        return self::when($search,function ($query) use ($search){
            $query->where(function ($query) use ($search) {
                $query->where('type_item_fakes.name', 'LIKE', '%' . $search . '%');
            });
        })
        ->when($dateRage , function ($query) use ($dateRage){
            $query->whereBetween('type_item_fakes.created_at',[$dateRage["startDate"],$dateRage["endDate"]]);
        })
        ->when($typeName,function ($query) use ($typeName){
            $query->where("type_item_fakes.name",$typeName);
        })
        ->when($maxOrderNumber,function ($query) use ($maxOrderNumber){
            $query->where("type_item_fakes.numberOrder","<=",$maxOrderNumber);
        })
        ->when($minOrderNumber,function ($query) use ($minOrderNumber){
            $query->where("type_item_fakes.numberOrder",">=",$minOrderNumber);
        })
        ->when($beforeTime,function ($query) use ($beforeTime){
            $query->whereTime("type_item_fakes.created_at","<=",$beforeTime);
        })
        ->when($afterTime,function ($query) use ($afterTime){
            $query->whereTime("type_item_fakes.created_at",">=",$afterTime);
        })
        ->select(
            'type_item_fakes.id',
            'type_item_fakes.name',
            'type_item_fakes.image',
            'type_item_fakes.numberOrder',
            'type_item_fakes.created_at',
        )
        ->when($typeName,function ($query) use ($typeName){
            $query->where("type_item_fakes.name",$typeName);
        })
        ->orderBy($sort_by ? $sort_by : "name", $sort_type?$sort_type:"asc");
    }
}
