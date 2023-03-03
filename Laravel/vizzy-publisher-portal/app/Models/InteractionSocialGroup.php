<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InteractionSocialGroup extends Model
{
    public $timestamps = false;

    public function card() {
        return $this->belongsTo(VizzyCard::class);
    }

    public function links() {
        return $this->hasMany(InteractionSocialGroupLink::class, 'group_id', 'id');
    }
}
