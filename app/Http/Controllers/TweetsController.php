<?php

namespace App\Http\Controllers;

use App\Models\Tweets;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TweetsController extends Controller
{
    /**
     * Store a new tweet in the database.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request) : void
    {
        // Validate the request...
    }
}
