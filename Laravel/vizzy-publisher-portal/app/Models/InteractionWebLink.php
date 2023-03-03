<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InteractionWebLink extends Model
{
    public $timestamps = false;
    
    public function group() {
        return $this->belongsTo(InteractionSocialGroup::class);
    }
}
