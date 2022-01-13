<?php

namespace App\Console\Commands;

use App\Jobs\ProccessAnswerMention;
use App\Models\Tweets;
use Atymic\Twitter\Facade\Twitter;
use Illuminate\Console\Command;

class ListenMentions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listen:mentions';

    private $paramsResponse =
        [
            "tweet.fields" => "attachments,author_id,context_annotations,conversation_id,created_at,entities,geo,id,in_reply_to_user_id,lang," .
                "public_metrics,possibly_sensitive,referenced_tweets,reply_settings,source,text,withheld"
            ,
            "user.fields" =>
                "created_at,description,entities,id,location,name,pinned_tweet_id,profile_image_url,protected,public_metrics,url,username," .
                "verified,withheld"

        ];

    private $termToSearchPattern = '/"(?<term>[a-zA-z0-9]+)"/';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        Twitter::getStream(function ($tweet) {

            /** Decode tweet JSON to array */
            $tweetArray = json_decode(utf8_decode($tweet), true);

            /** Verify if it is a hearthbeat to keep alive the stream */
            if (empty($tweetArray)) {
                $this->info("hearthbeat");
                return;
            }

            /** Extract the tweet info */
            $tweetText = $tweetArray['data']['text'];
            $twitterUserId = $tweetArray['data']['author_id'];

            /** Proccess tweet text with the predefined regex pattern to obtain the term to search*/
            preg_match($this->termToSearchPattern, $tweetText, $termMatch);
            $termToSearch = $termMatch['term'] ?? null;

            /** Verify if there is any term to search */
            if (!empty($termToSearch)) {
                $this->info("Term to search: " . $termToSearch);

                $tweetInstance = Tweets::create([
                    'twitter_user_id' => $twitterUserId,
                    'term_to_search' => $termToSearch,
                    'data' => $tweet
                ]);

                ProccessAnswerMention::dispatch($tweetInstance)->onQueue('answer-mention');
            }
            else {
                $this->info("No term to search in this tweet");
            }

        },
            $this->paramsResponse
        );
    }
}
