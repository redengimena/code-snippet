<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PodcastsCategory extends Model
{
    public $timestamps = false;
    
    public function podcast() {
        return $this->belongsTo(Podcast::class);
    }
}
