<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoryEmoji extends Model
{
    const TABLE_NAME = 'story';
    const ID = 'id';
    const STORY_ID = 'story_id';
    const CONTACT_ID = 'contact_id';
    const EMOJI = 'emoji';
    const COUNT = 'count';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;
}
