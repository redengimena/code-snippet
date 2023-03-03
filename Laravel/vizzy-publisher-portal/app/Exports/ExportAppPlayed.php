<?php

namespace App\Exports;

use App\Models\AppPlayed;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportAppPlayed implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
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
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect(AppPlayed::getPlayed());
    }
}
