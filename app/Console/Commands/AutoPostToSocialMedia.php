<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\User;
use App\Models\Setting;
use Noweh\TwitterApi\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class AutoPostToSocialMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically post scheduled content to social media.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $timezone = env('APP_TIMEZONE') ?? config('app.timezone');
        $scheduledPosts = Post::where('published_at', '<=', now($timezone))
            ->where(function ($query) {
                $query->where('is_posted_to_twitter', false)
                    ->orWhere('is_posted_to_facebook', false);
            })
            ->get();

        $twitterAutoPost = Setting::where('option_name', 'twitter_autopost')->first();
        $facebookAutoPost = Setting::where('option_name', 'facebook_autopost')->first();
        $users = User::all();
        $isAnyPostSuccessful = false;

        try {
            foreach ($scheduledPosts as $post) {
                $tags = $post->tags->pluck('name')->map(fn($tag) => "#$tag")->implode(' ');
                $siteUrl = 'https://tiptopacademy.org/';
                $description = "{$post->description}\n\n{$siteUrl}\n\n{$tags}";

                // Twitter Posting
                if (!$post->is_posted_to_twitter && ($twitterAutoPost->option_value ?? env('TWITTER_AUTO_POST'))) {
                    try {
                        $twitterClient = app(Client::class);
                        $tweetData = ['text' => $description];

                        if (!empty($post->image)) {
                            $fileUrl = url("storage/$post->image");
                            $fileData = base64_encode(file_get_contents($fileUrl));
                            $mediaInfo = $twitterClient->uploadMedia()->upload($fileData);
                            $tweetData['media'] = [
                                'media_ids' => [(string) $mediaInfo["media_id"]],
                            ];
                        }

                        $response = $twitterClient->tweet()->create()->performRequest($tweetData);
                        Log::info("Twitter response: " . json_encode($response));
                        $post->is_posted_to_twitter = true;
                    } catch (\Exception $e) {
                        Log::error("Error posting Post ID {$post->id} to Twitter: " . $e->getMessage());
                    }
                }

                // Facebook Posting
                if (!$post->is_posted_to_facebook && ($facebookAutoPost->option_value ?? env('FACEBOOK_AUTO_POST'))) {
                    $accessToken = app('facebook.access_token');
                    $facebook = app(\Facebook\Facebook::class);
                    $pageID = Setting::where('option_name', 'facebook_page_id')->first()->option_value ?? env('FACEBOOK_PAGE_ID');

                    try {
                        $data = ['message' => $description];

                        if (!empty($post->image)) {
                            $fileUrl = url("storage/$post->image");
                            $data['source'] = $facebook->fileToUpload($fileUrl);
                            $response = $facebook->post("/$pageID/photos", $data, $accessToken);
                        } else {
                            $response = $facebook->post("/$pageID/feed", $data, $accessToken);
                        }

                        Log::info("Facebook response: " . json_encode($response));
                        $post->is_posted_to_facebook = true;
                    } catch (\Exception $e) {
                        Log::error("Error posting Post ID {$post->id} to Facebook: " . $e->getMessage());
                    }
                }

                // Save the updated post status
                $post->save();

                // Flag successful posts
                if ($post->is_posted_to_twitter || $post->is_posted_to_facebook) {
                    $isAnyPostSuccessful = true;
                    Log::info("Post ID: {$post->id} status updated for Twitter/Facebook.");
                }

                if ($post->is_posted_to_twitter && $post->is_posted_to_facebook) {
                    $post->is_posted = true;
                    $post->save();
                    Log::info("Post ID: {$post->id} status updated for both Twitter and Facebook.");
                }
            }

            if ($isAnyPostSuccessful) {
                foreach ($users as $user) {
                    Notification::make()
                        ->title('Scheduled posts successfully posted')
                        ->body('Some or all scheduled posts were successfully posted to social media.')
                        ->success()
                        ->sendToDatabase($user);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error posting to social media: ' . $e->getMessage());

            foreach ($users as $user) {
                Notification::make()
                    ->title('Error posting to social media')
                    ->body('An error occurred while posting to social media. Please check the logs.')
                    ->danger()
                    ->sendToDatabase($user);
            }
        }
    }
}
