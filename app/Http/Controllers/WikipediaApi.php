<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class WikipediaApi extends Controller
{
    private $baseURL =  "https://es.wikipedia.org/w/api.php";
    private $baseParams = [
        "action" => "query",
        "list" => "search",
        "srprop" => "snippet",
        "format" => "json",
        "origin" => "*",
        "utf8" => ""
    ];

    public function searchPageSnippet(string $termToSearch):array {
        $client = new Client();

        $this->baseParams['srsearch'] = $termToSearch;
        $uri = $this->baseURL."?".http_build_query($this->baseParams);

        $response = $client->request('GET', $uri, []);
        $responseBody = (string) $response->getBody();
        $responseBodyDecoded = json_decode($responseBody, true);

        if(!empty($responseBodyDecoded))
            return $responseBodyDecoded;
        else
            return [];

    }
}
