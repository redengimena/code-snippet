<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PodcastCategoryMapping extends Model
{
    use HasFactory;

    public function podcastCategory() {
        return $this->belongsTo(PodcastCategory::class);
    }
}
