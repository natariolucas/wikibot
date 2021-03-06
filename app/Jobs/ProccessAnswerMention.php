<?php

namespace App\Jobs;

use App\Models\Tweets;
use Atymic\Twitter\Facade\Twitter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProccessAnswerMention implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tweet;
    protected $wikipediaSnippet;
    protected $maxTweetChars = 280;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Tweets $tweet, array $wikipediaSnippet)
    {
        $this->tweet = $tweet;
        $this->wikipediaSnippet = $wikipediaSnippet;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /** clean html*/
        $dirtyWikipediaContent = $this->wikipediaSnippet['query']["search"][0]['snippet'] ?? null;
        if (empty($dirtyWikipediaContent))
            return;

        $username = $this->tweet->getAttribute('twitter_username');
        $tweetId = $this->tweet->getAttribute('tweet_id');
        if(empty($username)) {
            var_dump('Empty username on tweet ' . $tweetId);
            return;
        }
        $prefixAnswer = "Encontre esto @$username : ";
        $cleanedWikipediaContent = strip_tags($dirtyWikipediaContent);
        $wikipediaContentLengthToTweet = $this->maxTweetChars - strlen($prefixAnswer);
        $truncatedWikipediaContent = $this->truncateText($cleanedWikipediaContent, $wikipediaContentLengthToTweet);
        $answer = $prefixAnswer . $truncatedWikipediaContent;

        $paramTweet = [
            "status" => $answer,
            "in_reply_to_status_id" => $tweetId
        ];

        var_dump('Replying to tweet '.$paramTweet['in_reply_to_status_id']);
        Twitter::forApiV1()->postTweet($paramTweet);
    }

    protected function truncateText($string, $your_desired_width)
    {
        $parts = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
        $parts_count = count($parts);

        $length = 0;
        $last_part = 0;
        for (; $last_part < $parts_count; ++$last_part) {
            $length += strlen($parts[$last_part]);
            if ($length > $your_desired_width) {
                break;
            }
        }

        return implode(array_slice($parts, 0, $last_part));
    }
}
