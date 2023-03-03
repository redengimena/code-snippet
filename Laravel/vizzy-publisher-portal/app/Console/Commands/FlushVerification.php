<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\PodcastVerification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FlushVerification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcast:flush-verification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush outdated verification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $datetime = Carbon::now()->subMinutes(30)->toDateTimeString();
        PodcastVerification::where('created_at', '<=', $datetime)->delete();                
    }

}
