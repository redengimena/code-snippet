<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vizzy extends Model
{
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_PUBLISHED = 'published';
    const STATUS_REJECTED = 'rejected';
    const STATUS_UNPUBLISHED = 'unpublished';

    public function podcast() {
        return $this->belongsTo(Podcast::class);
    }

    public function cards() {
        return $this->hasMany(VizzyCard::class);
    }

    public function episode() {
        $podcast = $this->podcast;
        $rss = $this->podcast->rss();
        return $rss->getEpisodes()->findByGuid($this->episode_guid);
    }

    public function getEditUrlAttribute() {
        $url = url(route('curator', ['podcast' => $this->podcast, 'guid' => $this->episode_guid]));
        return $url;
    }

    public function getStatusNameAttribute() {
        switch($this->status) {
            case Vizzy::STATUS_DRAFT:
                return 'Draft';
            case Vizzy::STATUS_PENDING:
                return 'Pending Approval';
            case Vizzy::STATUS_PUBLISHED:
                return 'Published';
            case Vizzy::STATUS_REJECTED:
                return 'Rejected';
            case Vizzy::STATUS_UNPUBLISHED:
                return 'Un-published';
            default:
                return 'Draft';
        }
    }

    public function getButtonStatusAttribute() {
        switch($this->status) {
            case Vizzy::STATUS_PENDING:
                return 'Un-Publish';
            case Vizzy::STATUS_PUBLISHED:
                return 'Un-Publish';
            case Vizzy::STATUS_DRAFT:
                return 'Publish';
            case Vizzy::STATUS_REJECTED:
                return 'Publish';
            case Vizzy::STATUS_UNPUBLISHED:
                return 'Publish';
            default:
                return 'Publish';
        }
    }

}
