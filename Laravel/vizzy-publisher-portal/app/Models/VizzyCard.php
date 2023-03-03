<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VizzyCard extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['firstname','lastname','email','password'];

    public function vizzy() {
        return $this->belongsTo(Vizzy::class);
    }

    public function info() {
        return $this->hasMany(InteractionInfo::class, 'card_id', 'id');
    }

    public function social() {
        return $this->hasMany(InteractionSocialGroup::class, 'card_id', 'id');
    }

    public function web() {
        return $this->hasMany(InteractionWeb::class, 'card_id', 'id');
    }

    public function product() {
        return $this->hasMany(InteractionProduct::class, 'card_id', 'id');
    }
}
