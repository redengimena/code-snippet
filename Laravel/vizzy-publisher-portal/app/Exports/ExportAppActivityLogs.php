<?php

namespace App\Exports;

use App\Models\AppActivityLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportAppActivityLogs implements FromCollection, WithHeadings
{
    public function headings():array{
        return[
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
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect(AppActivityLog::getActivityLog());
    }
}
