<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PodcastCategory extends Model
{
    use HasFactory;

    public function mappings() {
        return $this->hasMany(PodcastCategoryMapping::class);
    }

    public function mapped_categories() {
        $podcast_categorie = [];
        foreach ($this->mappings as $mappings) {
            $podcast_categorie[] = $mappings->name;
        }

        return $podcast_categorie;
    }

}
