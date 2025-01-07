<?php

namespace App\Providers;

use Facebook\Facebook;
use App\Models\Setting;
use Illuminate\Support\ServiceProvider;

class FacebookServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Facebook::class, function ($app) {
            $facebookData = Setting::where('option_name', 'like', 'facebook_%')->pluck('option_value', 'option_name')->toArray();

            return new Facebook([
                'app_id' => $facebookData['facebook_app_id'] ?? env('FACEBOOK_APP_ID'),
                'app_secret' => $facebookData['facebook_app_secret'] ?? env('FACEBOOK_APP_SECRET'),
                'default_graph_version' => 'v21.0', //$facebookData['facebook_default_graph_version'] ?? env('FACEBOOK_DEFAULT_GRAPH_VERSION', 'v21.0'),
            ]);
        });

        $this->app->singleton('facebook.access_token', function () {
            // Retrieve the access token from the database or environment
            $facebookData = Setting::where('option_name', 'like', 'facebook_%')->pluck('option_value', 'option_name')->toArray();
            return $facebookData['facebook_access_token'] ?? env('FACEBOOK_ACCESS_TOKEN');
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
