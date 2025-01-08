<?php

namespace App\Filament\Pages;

use Filament\Forms;
use App\Models\Setting;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\HtmlString;
use Filament\Notifications\Notification;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.pages.settings';

    public ?array $twitterData = [];
    public ?array $facebookData = [];

    public function mount(): void
    {
        $this->twitterData = Setting::where('option_name', 'like', 'twitter_%')->pluck('option_value', 'option_name')->toArray();
        $this->twitterForm->fill($this->twitterData);

        $this->facebookData = Setting::where('option_name', 'like', 'facebook_%')->pluck('option_value', 'option_name')->toArray();
        $this->facebookForm->fill($this->facebookData);
    }

    protected function getForms(): array
    {
        return [
            'twitterForm',
            'facebookForm',
        ];
    }

    public function twitterForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Twitter API Credentials')
                    ->description(new HtmlString('Visit the <a href="https://developer.x.com/en/portal/dashboard" target="_blank" style="color:#3b82f6">Twitter Developer Portal</a> to create a new app and get your API credentials.'))
                    ->schema([
                        Forms\Components\TextInput::make('twitter_account_id')
                            ->label('Account ID')
                            ->placeholder('Add an account ID e.g 1183058513662227734')
                            ->required(),
                        Forms\Components\TextInput::make('twitter_api_key')
                            ->label('API Key')
                            ->placeholder('Add an Consumer API key e.g 1a2b3c4d5e6f7g8h9i0j1k2l3m4n5o6p7q8r9s0t1u2v3w4x5y6z')
                            ->required(),
                        Forms\Components\TextInput::make('twitter_api_secret_key')
                            ->label('API Secret Key')
                            ->placeholder('Add an Consumer API secret key e.g 1a2b3c4d5e6f7g8h9i0j1k2l3m4n5o6p7q8r9s0t1u2v3w4x5y6z')
                            ->required(),
                        Forms\Components\TextInput::make('twitter_access_token')
                            ->label('Access Token')
                            ->placeholder('Add an access token e.g 1183058513662227734-1a2b3c4d5e6f7g8h9i0j1k2l3m4n5o6p7q8r9s0t1u2v3w4x5y6z')
                            ->required(),
                        Forms\Components\TextInput::make('twitter_access_token_secret')
                            ->label('Access Token Secret')
                            ->placeholder('Add an access token secret e.g 1a2b3c4d5e6f7g8h9i0j1k2l3m4n5o6p7q8r9s0t1u2v3w4x5y6z')
                            ->required(),
                        // Enable auto-posting for Twitter
                        Forms\Components\Toggle::make('twitter_autopost')
                            ->label('Enable Auto-post')
                            ->hint('Enable or disable auto-post to Twitter.')
                            ->default(false),
                    ]),
            ])
            ->statePath('twitterData');
    }

    public function facebookForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Facebook API Credentials')
                    ->description(new HtmlString('Visit the <a href="https://developers.facebook.com/apps" target="_blank" style="color:#3b82f6">Facebook for Developers</a> to create a new app and get your API credentials.'))
                    ->schema([
                        Forms\Components\TextInput::make('facebook_app_id')
                            ->label('App ID')
                            ->placeholder('Add an app ID e.g 2428070484192111')
                            ->required(),
                        Forms\Components\TextInput::make('facebook_page_id')
                            ->label('Page ID')
                            ->placeholder('Add a page ID e.g 289905724212122')
                            ->required(),
                        Forms\Components\TextInput::make('facebook_app_secret')
                            ->label('App Secret')
                            ->placeholder('Add an app secret e.g 96b8b62b106c13ea890r958c82e2ettb4')
                            ->required(),
                        Forms\Components\Select::make('facebook_default_graph_version')
                            ->label('Default Graph Version')
                            ->options([
                                'v2.0' => 'v2.0',
                                'v1.0' => 'v1.0',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('facebook_access_token')
                            ->label('Access Token')
                            ->placeholder('Add an access token e.g EAAigUTazLBIBOxBqOFhPxoyyminhYZBLvevcwaKgmyhn3XYFQ6bRoOD6066yMmK4UJOTUY86YuyFiCsaPnWaZAGBffpypbqCjhDBZA0xZAcIdbMgA7lnIZCj0QVfuIqRLMZB0IcdmKy3ea2mUITypv5r3RZCr0LAJFEralVqCJiPKLycCX6iTZBWilAmqq46IT3k8mIYQgRTE1p7U4qNTwcnKXkkqdZBJn9ehtQZDZD')
                            ->hint(new HtmlString('You can generate an access token from the <a href="https://developers.facebook.com/tools/explorer" target="_blank" style="color:#3b82f6">Graph API Explorer</a>.')),
                        Forms\Components\Toggle::make('facebook_autopost')
                            ->label('Enable Auto-post')
                            ->hint('Enable or disable auto-post to Facebook.')
                            ->default(false),
                    ]),
            ])
            ->statePath('facebookData');
    }

    public function getTwitterActions(): array
    {
        return [
            Action::make('save')
                ->submit('saveTwitter')
        ];
    }

    public function getFacebookActions(): array
    {
        return [
            Action::make('save')
                ->submit('saveFacebook')
        ];
    }

    public function saveTwitter(): void
    {
        $data = $this->twitterForm->getState();

        // Save the data to the database
        foreach ($data as $key => $value) {
            // Save the data to the database
            Setting::updateOrCreate(['option_name' => $key], ['option_value' => $value]);
        }

        Notification::make()
            ->title('Twitter API Credentials')
            ->body('Your Twitter API credentials has been saved successfully.')
            ->success()
            ->send();
    }

    public function saveFacebook(): void
    {
        $data = $this->facebookForm->getState();

        // Save the data to the database
        foreach ($data as $key => $value) {
            // Save the data to the database
            Setting::updateOrCreate(['option_name' => $key], ['option_value' => $value]);
        }

        Notification::make()
            ->title('Facebook API Credentials')
            ->body('Your Facebook API credentials has been saved successfully.')
            ->success()
            ->send();
    }
}
