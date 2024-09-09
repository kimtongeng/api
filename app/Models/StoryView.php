<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoryView extends Model
{
    const TABLE_NAME = 'story';
    const ID = 'id';
    const STORY_ID = 'story_id';
    const CONTACT_ID = 'contact_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;
}
