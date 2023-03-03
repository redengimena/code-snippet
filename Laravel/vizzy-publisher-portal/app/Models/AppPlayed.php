<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppPlayed extends Model
{
    protected $connection= 'vizzyapi';

    protected $table = 'played';

    public static function getPlayed()
    {
        $records = AppPlayed::select(
            'id',
            'user_id',
            'image',
            'show_name',
            'episode_name',
            'feed_url',
            'episode_guid',
            'elapsed',
            'created_at',
            'updated_at'
        )->get()->toArray();
        return $records;
    }
}
