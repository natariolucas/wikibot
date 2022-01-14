<?php

namespace App\Jobs;

use App\Http\Controllers\WikipediaApi;
use App\Models\Tweets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProccessAnswerMention implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $tweet;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Tweets $tweet)
    {
        $this->tweet = $tweet;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $api = new WikipediaApi();
        $termToSearch = $this->tweet->getAttribute('term_to_search');
        $api->searchPageSnippet($termToSearch);
    }
}
