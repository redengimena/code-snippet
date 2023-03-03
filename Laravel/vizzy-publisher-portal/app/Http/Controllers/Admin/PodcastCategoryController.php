<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Podcast;
use App\Models\Vizzy;
use App\Models\User;
use App\WebServices\VizzyApi\VizzyApi;
use App\WebServices\PodcastIndexApi\PodcastIndexApi;
use App\Traits\EscapeFileUrlTrait;
use PodcastIndex\Client;


class PodcastCategoryController extends Controller
{
    use EscapeFileUrlTrait;
    
    protected $vizzyApi;
    protected $podcastIndexApi;
    
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(VizzyApi $vizzyApi, PodcastIndexApi $podcastIndexApi)
    {
        $this->middleware(['auth','role:admin']);

        $this->vizzyApi = $vizzyApi;
        $this->podcastIndexApi = $podcastIndexApi;
    }
    
    /**
     * Category listing page.
     *
     * @param \Illuminate\Http\Request
     */
    public function index(Request $request)
    {
        $categories = $this->vizzyApi->getPodcastCategories();
        
        return view('admin.podcast-categories.index', compact('categories'));
    }

    /**
     * Category creation page.
     *
     * @param \Illuminate\Http\Request
     */
    public function create(Request $request)
    {
        return view('admin.podcast-categories.create');
    }

    /**
     * Category show page.
     *
     * @param \Illuminate\Http\Request
     */
    public function show(Request $request, $id)
    {
        return redirect(route('admin.podcast-categories.edit', $id));
    }

    /**
     * Category edit page.
     *
     * @param \Illuminate\Http\Request
     */
    public function edit(Request $request, $id)
    {
        $category = $this->vizzyApi->getPodcastCategory($id);
        if (!$category) {
            abort(404);    
        }

        $subcategories = $this->podcastindexSubCategories();
        
        return view('admin.podcast-categories.edit', compact('category','subcategories'));
    }

    /**
     * Category create.
     *
     * @param \Illuminate\Http\Request
     */
    public function store(Request $request)
    {
        $data = [
            'name' => $request->input('name'),
            'image' => '',
            'mapping' => $request->input('mapping'),
        ];

        // save category and retrieve new id
        $category = $this->vizzyApi->storePodcastCategory($data);
        if ($category) {

            // with new id, upload image to s3
            $path_parts = pathinfo($request->input('image'));
            $id = $category->id;
            $ext = $path_parts['extension'];
            $url = $this->escapefile_url($request->input('image'));
            Storage::disk('s3')->put('category/'.$id.'.'.$ext, file_get_contents($url));
            $path = Storage::disk('s3')->url('category/'.$id.'.'.$ext);
            $data['image'] = $path;

            // store s3 image path
            $result = $this->vizzyApi->updatePodcastCategory($id, $data);
            if ($result) {
                return redirect(route('admin.podcast-categories.index'))->with('success', 'Podcast Category created!');
            } else {
                return redirect(route('admin.podcast-categories.index'))->with('error', 'Podcast Category created but error with uploading image!');
            }
        } else {
            return back()->with('error', 'Error updateing Podcast Category!');
        }
    }

    /**
     * Category update.
     *
     * @param \Illuminate\Http\Request
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'image' => 'required'
        ]);
        
        $data = [
            'name' => $request->input('name'),
            'image' => $request->input('image'),
            'mapping' => $request->input('mapping'),
        ];

        $path_parts = pathinfo($request->input('image'));
        $ext = $path_parts['extension'];
        $url = $this->escapefile_url($request->input('image'));
        Storage::disk('s3')->put('category/'.$id.'.'.$ext, file_get_contents($url));
        $path = Storage::disk('s3')->url('category/'.$id.'.'.$ext);
        $data['image'] = $path;

        $result = $this->vizzyApi->updatePodcastCategory($id, $data);
        if ($result) {        
            return back()->with('success', 'Podcast Category updated!');
        } else {
            return back()->with('error', 'Error updateing Podcast Category!');
        }
    }

    /**
     * Category delete.
     *
     * @param \Illuminate\Http\Request
     */
    public function destroy(Request $request, $id)
    {
        $path_parts = pathinfo($request->input('image'));
        $ext = $path_parts['extension'];        
        Storage::disk('s3')->delete('category/'.$id.'.'.$ext);
        
        $result = $this->vizzyApi->deletePodcastCategory($id);
        if ($result) {        
            return redirect(route('admin.podcast-categories.index'))->with('success', 'Podcast Category deleted!');
        } else {
            return back()->with('error', 'Error deleteing Podcast Category!');
        }
    }

    /**
     * Category from podcastindex
     */
    public function podcastindexSubCategories()
    {
        return $this->podcastIndexApi->getCategories();        
    }

    /**
     * Report
     *
     * @param \Illuminate\Http\Request
     */
    public function report(Request $request)
    {
        $data = [];
        $subcategories = $this->podcastindexSubCategories();
        $categories = $this->vizzyApi->getPodcastCategories();
        
        foreach ($subcategories as $subcategory) {
            $data[$subcategory->name] = [];
            foreach ($categories as $category) {
                if (in_array($subcategory->name, $category->mappings)) {
                    $data[$subcategory->name][] = $category->name;
                }
            }
        }

        ksort($data);
        
        return view('admin.podcast-categories.report', compact('data'));
    }

}
