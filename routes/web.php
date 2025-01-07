<?php

use App\Models\Post;
use Facebook\Facebook;
use Noweh\TwitterApi\Client;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $fb = app(Facebook::class);
    try {
        $response = $fb->get('/me', env('FACEBOOK_ACCESS_TOKEN'));

        // Decode the response body and return it as JSON
        $graphNode = $response->getGraphNode();
        return response()->json($graphNode->asArray());
    } catch (\Facebook\Exceptions\FacebookResponseException $e) {
        return response()->json(['error' => 'Graph returned an error: ' . $e->getMessage()], 400);
    } catch (\Facebook\Exceptions\FacebookSDKException $e) {
        return response()->json(['error' => 'Facebook SDK returned an error: ' . $e->getMessage()], 500);
    }
});

