<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasPermissionsTrait;
use Illuminate\Support\Facades\DB;
use Lab404\Impersonate\Models\Impersonate;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasPermissionsTrait, Impersonate;

    const STATUS_REGISTERED = 'Registered';
    const STATUS_VERIFIED = 'Verified';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public static function boot()
    {
        parent::boot();

        $disk = app('filesystem')->disk(config('mediaManager.storage_disk'));

        static::created(function ($model) use ($disk) {
            $disk->makeDirectory('uploads/' . $model->id);
        });

        static::deleted(function ($model) use ($disk) {
            $disk->deleteDirectory('uploads/' . $model->id);
        });
    }

    public function getFullnameAttribute() {
        $fullname = [];
        $fullname[] = $this->firstname;
        $fullname[] = $this->lastname;
        return implode(' ', $fullname);
    }

    public function getStatusAttribute() {
        if ($this->email_verified_at) {
            return USER::STATUS_VERIFIED;
        }

        return USER::STATUS_REGISTERED;
    }

    /**
     * @return bool
     */
    public function canImpersonate()
    {
        // For example
        return $this->hasRole('admin');
    }

    /**
     * @return bool
     */
    public function canBeImpersonated()
    {
        // For example
        // return !$this->hasRole('admin');
        return true;
    }


    public function podcasts() {
        return $this->hasMany(Podcast::class);
    }

    public function audios() {
        return $this->hasMany(Audio::class);
    }

    public function getPodcastUrlsAttribute() {
        $urls = [];
        foreach ($this->podcasts as $podcast) {
            $urls[] = $podcast->feed_url;
        }

        return "['" . implode("','",$urls). "']";
    }

    public static function getUsers()
    {
        $records = DB::table('users')->select(
            'id',
            'firstname',
            'lastname',
            'email',
            'company',
            'phone',
        )->get()->toArray();
        return $records;
    }
}
