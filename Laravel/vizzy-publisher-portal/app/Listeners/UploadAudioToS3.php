<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\VizzyCreated;

class UploadAudioToS3 implements ShouldQueue {

    /**
     * Handle the vizzy created event.
     *
     * @param  VizzyCreated  $event
     * @return void
     */
    public function handle(VizzyCreated $event)
    {
        $vizzy = $event->vizzy;

        // download audio_url and upload to S3
        if (!$vizzy->audio_url) {

            $id = $vizzy->id;

            // get episode audio url
            $episode = $vizzy->episode();
            $uri = $episode->getMedia()->getUri();

            // generate a new filename from the original file name
            $path_parts = pathinfo(parse_url($uri)['path']);
            $filename = 'vizzy' . $id . '-' . $path_parts['filename'] . '.' . $path_parts['extension'];

            // download to temp location
            $response = Http::get($uri);
            Storage::disk('local')->put('temp/'.$filename, $response->body());

            // upload local audio file to S3
            $local_path = Storage::disk('local')->path('temp/'.$filename);
            $resource = fopen($local_path, 'r+');
            Storage::disk('s3')->put('vizzy-audio/'.$filename, $resource);
            fclose($resource);

            // save audio S3 path to vizzy table
            $audio_url = Storage::disk('s3')->url('vizzy-audio/'.$filename);
            $vizzy->audio_url = $audio_url;
            $vizzy->save();

            // delete local audio from temp
            Storage::disk('local')->delete('temp/'.$filename);
        }
    }

  }