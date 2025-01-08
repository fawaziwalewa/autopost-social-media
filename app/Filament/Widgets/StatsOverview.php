<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = -5;

    protected function getStats(): array
    {
        $totalPosts = Post::count();
        $timezone = env('APP_TIMEZONE') ?? config('app.timezone');
        $newPostsThisMonth = Post::whereMonth('created_at', now($timezone)->month)->count();
        $publishedPostsTwitter = Post::where('is_posted_to_twitter', true)->count();
        $scheduledPostsTwitter = Post::where('is_posted_to_twitter', false)->count();
        $publishedPostsFacebook = Post::where('is_posted_to_facebook', true)->count();
        $scheduledPostsFacebook = Post::where('is_posted_to_facebook', false)->count();


        return [
            Stat::make('Total Posts', $totalPosts)
                ->description("{$newPostsThisMonth} new posts this month")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Published Posts to Twitter', $publishedPostsTwitter)
                ->description('Posts published to Twitter')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('dark')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Scheduled Posts to Twitter', $scheduledPostsTwitter)
                ->description('Posts scheduled to Twitter')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Published Posts to Facebook', $publishedPostsFacebook)
                ->description('Posts published to Facebook')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('info')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Scheduled Posts to Facebook', $scheduledPostsFacebook)
                ->description('Posts scheduled to Facebook')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
        ];
    }
}
