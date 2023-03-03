<?php

namespace App\Exports;

use App\Models\AppUser;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportAppUsers implements FromCollection, WithHeadings
{
    public function headings():array{
        return[
            'id',
            'name',
            'firstname',
            'lastname',
            'email',
            'provider_name',
            'provider_id',
            'provider_image',
            'image',
            'override_provider',
            'created_at',
            'updated_at'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect(AppUser::getUsers());
    }
}
