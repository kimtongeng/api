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
    public static function getList(){
        return self::select("id","name","image","created_at","numberOrder");
    }
}
