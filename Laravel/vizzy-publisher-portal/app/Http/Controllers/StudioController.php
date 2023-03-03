<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Audio;
use App\Models\AudioChapter;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\WebServices\BitlyApi\BitlyApi;

class StudioController extends Controller
{
    protected $bitlyApi;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BitlyApi $bitlyApi)
    {
        $this->middleware('auth');
        $this->bitlyApi = $bitlyApi;
    }

    /**
     * Show generated MP3.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        return view('studio.index');
    }

    /**
     * Save uploaded MP3.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function upload(Request $request)
    {
        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $folder = 'audio/' . $request->user()->id . '/' . rand(1000,9999);
        $path = $file->storeAs($folder, $filename, 'public');

        $audio = new Audio();
        $audio->user_id = $request->user()->id;
        $audio->filename = $filename;
        $audio->name = $filename;
        $audio->path = $path;

        $id3_info = $audio->ID3Info;

        if (array_key_exists('playtime_seconds', $id3_info)){
            $audio->duration = $id3_info['playtime_seconds'];
        } else {
            // try wapmorgan library
            $file_path = Storage::disk('public')->path($audio->path);
            $mp3 = new \wapmorgan\Mp3Info\Mp3Info($file_path, true);
            if ($mp3->duration) {
                $audio->duration = $mp3->duration;
            }
        }

        $chapters = $this->loadID3Chapter($id3_info, $folder);
        if ($chapters && $chapters != '[]') {
            $has_meta = true;
            $audio->chapters = $chapters;
        } else {
            $has_meta = false;
        }

        $audio->save();

        $edit_url = route('studio-tool', $audio->slug());

        return response()->json(['meta' => $has_meta, 'edit_url' => $edit_url]);
    }

    /**
     * Helper function to load chapter information from id3 tags
     */
    public function loadID3Chapter($info, $audio_folder)
    {
        $output = [];
        foreach ($info['id3v2'] as $frame_name => $frame) {
            if ($frame_name == 'CHAP') {
                foreach ($frame as $item) {
                    $chapter = [
                        'id' => Str::uuid(),
                        'start' => $item['time_begin'],
                        'end' => $item['time_end'],
                        'title' => $item['chapter_name'],
                    ];

                    if (array_key_exists('chapter_url', $item)) {
                        foreach ($item['chapter_url'] as $key => $url) {
                          $chapter['url'] = $url;
                        }
                    }

                    if (array_key_exists('picture_present', $item)) {
                        foreach ($item['subframes'] as $subframe) {
                            if (array_key_exists('image_mime', $subframe)) {
                                $extension = $subframe['image_mime'] == 'image/jpeg' ? 'jpg' : 'png';
                                $data = $subframe['data'];
                                $filename = $audio_folder.'/'.$item['time_begin'].'.'.$extension;
                                Storage::disk('public')->put($filename, $data);
                                $chapter['image'] = $filename;
                            }
                        }
                    }

                    $output[] = $chapter;
                }
            }
        }

        return json_encode($output);
    }

    /**
     * Chapter tool uploaded MP3.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function tool(Request $request, Audio $audio)
    {
        if ($audio->user_id != $request->user()->id) {
            abort(403);
        }

        // delete image if user choose to start fresh
        $fresh = $request->input('fresh');
        if ($fresh && $audio->chapters) {
            $chapters = json_decode($audio->chapters);
            foreach ($chapters as $chapter) {
                if (isset($chapter->image)) {
                    Storage::disk('public')->delete($chapter->image);
                }
            }
            $audio->chapters = null;
            $audio->save();
        }

        $public_url = Storage::disk('public')->url('');

        return view('studio.tool', compact('audio','public_url'));
    }

    /**
     * Save uploaded image to audio folder.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function imageUpload(Request $request, Audio $audio)
    {
        $chapters = json_decode($audio->chapters);

        // store uploaded image
        $file = $request->file('file');
        $filename = rand(1000,9999) . '_' . $file->getClientOriginalName();
        $folder = dirname($audio->path);
        $path = $file->storeAs($folder, $filename, 'public');

        $card_id = $request->input('id');
        $start = (int) $request->input('start');
        $inserted = false;

        if ($card_id) {
            for ($i=0; $i < count($chapters); $i++) {
                if ($chapters[$i]->id == $card_id){

                    // remove original from disk
                    if (isset($chapters[$i]->image)) {
                      Storage::disk('public')->delete($chapters[$i]->image);
                    }

                    $chapters[$i]->image = $folder . '/' . $filename;
                    $current = $chapters[$i];
                }
            }
        } else {
            $current = json_decode(json_encode([
                'id' => Str::uuid(),
                'start' => $start,
                'end' => 0,
                'title' => $request->input('title'),
                'url' => $request->input('url'),
                'image' => $folder.'/'.$filename
            ]));
            $chapters[] = $current;
        }

        $chapters = $this->populateChapterEndTime($chapters, $audio->duration);
        $audio->chapters = json_encode($chapters);
        $audio->save();

        return response()->json([
            'chapters' => $audio->chapters,
            'card' => $current,
            'last_updated' => $audio->last_saved
        ]);
    }

    /**
     * Delete single chapter.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function imageDelete(Request $request, Audio $audio)
    {
        if ($audio->user_id != $request->user()->id) {
            abort(403);
        }

        $chapter = json_decode($request->input('chapter'));
        $chapters = json_decode($audio->chapters);
        for ($i=0; $i < count($chapters); $i++) {
            if ($chapters[$i]->id == $chapter->id) {

                // delete image from disk
                if (isset($chapter->image)) {
                    Storage::disk('public')->delete($chapter->image);
                }

                unset($chapters[$i]);
            }
        }

        $chapters = $this->populateChapterEndTime($chapters, $audio->duration);
        $audio->chapters = json_encode($chapters);
        $audio->save();

        return response()->json([
            'chapters' => $audio->chapters,
            'last_updated' => $audio->last_saved
        ]);
    }

    /**
     * Helper function to sort chapters and populate end time
     */
    protected function populateChapterEndTime($chapters, $audio_duration) {
        usort($chapters, function($a,$b) {
            return $a->start <=> $b->start;
        });

        $start_time = array_map(function($chapter) {
          return $chapter->start;
        }, $chapters);

        for ($i=0; $i < count($chapters); $i++) {
          $end_time = $i == count($start_time)-1 ? $audio_duration*1000 : $start_time[$i+1];
          $chapters[$i]->end = $end_time;
        }

        return $chapters;
    }

    /**
     * Save chapters information.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function save(Request $request, Audio $audio)
    {
        if ($audio->user_id != $request->user()->id) {
            abort(403);
        }

        if ($request->input('chapters')) {
            $chapters = json_decode($request->input('chapters'));
            $chapters = $this->populateChapterEndTime($chapters, $audio->duration);
            $audio->chapters = json_encode($chapters);
        }

        if ($request->input('audio_name')) {
            $audio->name = $request->input('audio_name');
        }

        $audio->save();

        return response()->json([
            'chapters' => $audio->chapters,
            'last_updated' => $audio->last_saved
        ]);
    }

    /**
     * Download mp3 with chapters.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function download(Request $request, Audio $audio)
    {
        if ($audio->user_id != $request->user()->id) {
            abort(403);
        }

        // generate bitlink
        if (config('webservices.bitlyapi.access_token')) {
            $chapters = $this->getChapterWithShortUrl($audio);
        } else {
            $chapters = $audio->chapters;
        }

        // generate chapters.json and save it to audio folder
        $folder = dirname($audio->path); // audio folder
        $chaptersJsonFile = $folder . '/chapters.json';
        Storage::disk('public')->put($chaptersJsonFile, $chapters);

        // prepare arguments for the python mutagen script
        $mutagen_python = config('vizzy.mutagen_python');
        $mutagen_script = config('vizzy.mutagen_script');
        $absolute_path = Storage::disk('public')->path('');
        $audio_path = $audio->path;
        $chapters_path = $chaptersJsonFile;

        $process = new Process([
            $mutagen_python,
            $mutagen_script,
            $absolute_path,
            $audio_path,
            $chapters_path
            ]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $target_path = trim($process->getOutput());

        $output_filename = substr($audio->name,-4) != '.mp3' ? $audio->name.'.mp3' : $audio->name;

        return Storage::disk('public')->download($target_path, $output_filename);
    }

    /**
     * Generate show notes
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showNotes(Request $request, Audio $audio)
    {
        if (config('webservices.bitlyapi.access_token')) {
            $chapters = $this->getChapterWithShortUrl($audio);
        } else {
            $chapters = $audio->chapters;
        }

        return response()->json([
            'chapters' => $chapters
        ]);
    }

    /**
     * Delete audio.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function delete(Request $request, Audio $audio)
    {
        if ($audio->user_id != $request->user()->id) {
            abort(403);
        }

        $folder = dirname($audio->path);

        Storage::disk('public')->deleteDirectory($folder);

        // delete bitlink
        foreach ($audio->audioChapters as $chapter) {
            if ($chapter->shorturl) {
                $this->bitlyApi->deleteBitlink($chapter->shorturl);
            }
        }

        $audio->delete();

        return redirect(route('studio'))->with('success', 'Audio deleted!');;
    }

    /**
     * Save data in to audio chapter table
     */
    public function getChapterWithShortUrl($audio)
    {
        $update_chapters = [];
        foreach (json_decode($audio->chapters) as $chapter) {

            $audioChapter = AudioChapter::where('uuid', $chapter->id)->first();
            if (!$audioChapter) {
                $audioChapter = new AudioChapter();
            }

            $audioChapter->uuid = $chapter->id;
            $audioChapter->audio_id = $audio->id;
            $audioChapter->start = $chapter->start;
            $audioChapter->end = $chapter->end;
            $audioChapter->image = $chapter->image;
            if (isset($chapter->title)) {
                $audioChapter->title = $chapter->title;
            }
            if (isset($chapter->description)) {
                $audioChapter->description = $chapter->description;
            }
            if (isset($chapter->url)) {
                // url updated
                if ($audioChapter->url != $chapter->url) {

                    //delete old bitlink if exists
                    if ($audioChapter->shorturl) {
                        $this->bitlyApi->deleteBitlink(str_replace('https://','',$audioChapter->shorturl));
                    }

                    if ($chapter->url) {
                      $audioChapter->url = $chapter->url;
                      $shorturl = $this->bitlyApi->generateBitlink($chapter->url, $audio);
                      $audioChapter->shorturl = $shorturl;
                      $chapter->shorturl = $shorturl;
                    } else {
                      $audioChapter->url = null;
                      $audioChapter->shorturl = null;
                      $chapter->shorturl = null;
                    }
                }

            }
            $audioChapter->save();

            $update_chapters[] = $audioChapter;
        }

        return json_encode($update_chapters);
    }
}
