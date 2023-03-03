<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Balping\HashSlug\HasHashSlug;

class Audio extends Model
{
    use HasFactory;
    use HasHashSlug;

    public function audioChapters() {
        return $this->hasMany(AudioChapter::class);
    }

    public function getLastSavedAttribute()
    {
        return 'Last saved at ' . $this->updated_at->format('H:ia');
    }

    public function getAudioUrlAttribute()
    {
        return Storage::disk('public')->url($this->path);
    }

    /**
     * Helper function extract id3 tags from mp3
     */
    public function getID3InfoAttribute()
    {
        $file_path = Storage::disk('public')->path($this->path);
        $getID3 = new \getID3;
        $info = $getID3->analyze($file_path);
        return $info;
    }

}
