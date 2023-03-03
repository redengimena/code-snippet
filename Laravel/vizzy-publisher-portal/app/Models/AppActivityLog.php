<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppActivityLog extends Model
{
    protected $connection= 'vizzyapi';

    protected $table = 'activity_logs';

    public static function getActivityLog()
    {
        $records = AppActivityLog::select(
            'user_id',
            'action_type',
            'target_type',
            'show_id',
            'episode_id',
            'card_id',
            'tray_icon',
            'content',
            'ip',
            'agent',
            'created_at',
            'updated_at'
        )->get()->toArray();
        return $records;
    }
}
