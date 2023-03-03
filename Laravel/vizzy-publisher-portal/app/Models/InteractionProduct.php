<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InteractionProduct extends Model
{
    public $timestamps = false;
    
    public function card() {
        return $this->belongsTo(VizzyCard::class);
    }
}
