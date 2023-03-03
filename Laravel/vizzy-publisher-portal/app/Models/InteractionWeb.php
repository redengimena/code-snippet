<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InteractionWeb extends Model
{
    public $timestamps = false;

    public function card() {
        return $this->belongsTo(VizzyCard::class);
    }

    public function links() {
        return $this->hasMany(InteractionWebLink::class, 'group_id', 'id');
    }
}
