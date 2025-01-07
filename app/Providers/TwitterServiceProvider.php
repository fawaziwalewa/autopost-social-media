<?php

namespace App\Providers;

use App\Models\Setting;
use Noweh\TwitterApi\Client;
use Illuminate\Support\ServiceProvider;

class TwitterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Client::class, function () {
            $twitterData = Setting::where('option_name', 'like', 'twitter_%')->pluck('option_value', 'option_name')->toArray();

            return new Client([
                'account_id' => $twitterData['twitter_account_id'] ?? env('TWITTER_ACCOUNT_ID'),
                'consumer_key' => $twitterData['twitter_api_key'] ?? env('TWITTER_API_KEY'),
                'consumer_secret' => $twitterData['twitter_api_secret_key'] ?? env('TWITTER_API_KEY_SECRET'),
                'access_token' => $twitterData['twitter_access_token'] ?? env('TWITTER_ACCESS_TOKEN'),
                'access_token_secret' => $twitterData['twitter_access_token_secret'] ?? env('TWITTER_ACCESS_TOKEN_SECRET'),
                'bearer_token' => $twitterData['twitter_bearer_token'] ?? env('TWITTER_BEARER_TOKEN'),
            ]);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
