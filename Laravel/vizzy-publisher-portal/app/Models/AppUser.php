<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class AppUser extends Model
{
    protected $connection= 'vizzyapi';

    protected $table = 'users';

    public static function getUsers()
    {
        $records = AppUser::select(
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
        )->get()->toArray();
        return $records;
    }
}
