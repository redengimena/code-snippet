<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PodcastCategory;
use App\Models\UserPodcastCategory;
use App\Models\PodcastCategoryMapping;
use App\Http\Resources\PodcastCategory as PodcastCategoryResource;
use Carbon\Carbon;

class PodcastCategoryController extends Controller
{
    
    /**
     * Get all podcast categories
     *     
     * @param GuzzleHttp\Request
     */
    public function podcastCategories(Request $request) {
        $categories = PodcastCategory::get();
        
        return $this->sendResponse(PodcastCategoryResource::collection($categories));
    }

    /**
     * Get single podcast category
     *     
     * @param GuzzleHttp\Request
     */
    public function editPodcastCategory(Request $request, PodcastCategory $podcastCategory) {      
        return $this->sendResponse(new PodcastCategoryResource($podcastCategory));
    }

    /**
     * Update single podcast category
     *     
     * @param GuzzleHttp\Request
     */
    public function updatePodcastCategory(Request $request, PodcastCategory $podcastCategory) {
        $request_data = $request->all();
        $name = $request_data['name'];
        $image = $request_data['image'] ? $request_data['image'] : '';
        $mappings = $request_data['mapping'] ? $request_data['mapping'] : [];

        $podcastCategory->name = $name;
        $podcastCategory->image = $image;
        $podcastCategory->save();

        $podcastCategory->mappings()->delete();
        foreach ($mappings as $mapping) {
            $m = new PodcastCategoryMapping();
            $m->podcast_category_id = $podcastCategory->id;
            $m->name = $mapping;
            $m->save();
        }
      
        return $this->sendResponse(new PodcastCategoryResource($podcastCategory));
    }

    /**
     * Create new podcast category
     *     
     * @param GuzzleHttp\Request
     */
    public function storePodcastCategory(Request $request) {
        $name = $request->input('name');
        $image = $request->input('image') ? $request->input('image') : '';
        $mappings = $request->input('mapping') ? $request->input('mapping') : [];

        $podcastCategory = new PodcastCategory();
        $podcastCategory->name = $name;
        $podcastCategory->image = $image;
        $podcastCategory->save();

        foreach ($mappings as $mapping) {
            $m = new PodcastCategoryMapping();
            $m->podcast_category_id = $podcastCategory->id;
            $m->name = $mapping;
            $m->save();
        }
      
        return $this->sendResponse(new PodcastCategoryResource($podcastCategory));
    }

    /**
     * Delete podcast category
     *     
     * @param GuzzleHttp\Request
     */
    public function deletePodcastCategory(Request $request, PodcastCategory $podcastCategory) {
        $podcastCategory->delete();

        return $this->sendResponse(null, 'Podcast category has been deleted.');
    }

    /**
     * Update user selected podcast category
     *     
     * @param GuzzleHttp\Request
     */
    public function updateUserPodcastCategory(Request $request) {
        $categories = $request->input('categories');
        
        if ($categories) {
            $request->user()->podcast_categories()->delete();
            $cats = PodcastCategory::whereIn('id', $categories)->get();
            foreach ($cats as $cat) {
              $c = new UserPodcastCategory();
              $c->user_id = $request->user()->id;
              $c->podcast_category_id = $cat->id;
              $c->save();
            }            
        }

        return $this->sendResponse(null, 'User podcast categories has been saved successfully.');
    }

}