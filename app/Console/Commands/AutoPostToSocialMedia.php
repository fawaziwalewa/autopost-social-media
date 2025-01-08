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
            ->where('is_posted', false)
            ->get();

        $twitterAutoPost = Setting::where('option_name', 'twitter_auto_post')->first();
        $facebookAutoPost = Setting::where('option_name', 'facebook_auto_post')->first();
        $users = User::all();
        $isAnyPostSuccessful = false;

        try {
            foreach ($scheduledPosts as $post) {
                $isTwitterPosted = false;
                $isFacebookPosted = false;

                // Format description with tags
                $tags = $post->tags->pluck('name')->map(function ($tag) {
                    return "#$tag"; // Prepend each tag with '#'
                })->implode(' ');

                $siteUrl = env('APP_URL');
                $description = "{$post->description}\n\n{$siteUrl}\n\n{$tags}";

                // Post to Twitter
                if (($twitterAutoPost && $twitterAutoPost->option_value) || env('TWITTER_AUTO_POST')) {
                    try {
                        $twitterClient = app(Client::class);
                        $tweetData = ['text' => $description];

                        if (!empty($post->image)) {
                            $fileUrl = url("storage/$post->image");
                            $fileData = base64_encode(file_get_contents($fileUrl));
                            $mediaInfo = $twitterClient->uploadMedia()->upload($fileData);

                            $tweetData['media'] = [
                                'media_ids' => [
                                    (string) $mediaInfo["media_id"],
                                ],
                            ];
                        }

                        $twitterClient->tweet()->create()->performRequest($tweetData);
                        $isTwitterPosted = true;
                    } catch (\Exception $e) {
                        Log::error("Error posting Post ID {$post->id} to Twitter: " . $e->getMessage());
                    }
                }


                // Post to Facebook
                if (($facebookAutoPost && $facebookAutoPost->option_value) || env('FACEBOOK_AUTO_POST')) {
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

                        Log::info("Facebook response: " . json_encode($response->getGraphNode()));

                        $isFacebookPosted = !empty($response->getGraphNode()['id']);
                    } catch (\Exception $e) {
                        Log::error("Error posting Post ID {$post->id} to Facebook: " . $e->getMessage());
                    }
                }

                // Mark post as posted only if successfully posted to both platforms
                if ($isTwitterPosted && $isFacebookPosted) {
                    $post->is_posted = true;
                    $post->save();

                    $isAnyPostSuccessful = true;

                    $this->info("Post ID: {$post->id} successfully posted to Twitter and Facebook.");
                    Log::info("Post ID: {$post->id} successfully posted to Twitter and Facebook.");
                }
            }

            if ($isAnyPostSuccessful) {
                foreach ($users as $user) {
                    Notification::make()
                        ->title('Scheduled posts successfully posted to social media')
                        ->body('All scheduled posts have been successfully posted to Twitter and Facebook.')
                        ->success()
                        ->sendToDatabase($user);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error posting to social media: ' . $e->getMessage());

            foreach ($users as $user) {
                Notification::make()
                    ->title('Error posting to social media')
                    ->body('An error occurred while posting to social media. Please check the logs for more information.')
                    ->danger()
                    ->sendToDatabase($user);
            }
        }
    }

}
