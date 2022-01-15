<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tweets extends Model
{
    use HasFactory;

    protected $fillable = ['tweet_id', 'twitter_user_id', 'term_to_search', 'data'];
}
