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
        $publishedPosts = Post::where('is_posted', true)->count();
        $scheduledPosts = Post::where('is_posted', false)->count();
        $newPostsThisMonth = Post::whereMonth('created_at', now($timezone)->month)->count();

        return [
            Stat::make('Total Posts', $totalPosts)
                ->description("{$newPostsThisMonth} new posts this month")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Published Posts', $publishedPosts)
                ->description('Posts published so far')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Scheduled Posts', $scheduledPosts)
                ->description('Posts awaiting publication')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
        ];
    }
}
