<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Post;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PostResource\Pages;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Split::make([
                    Forms\Components\Section::make('Post content')->schema([
                        Forms\Components\Textarea::make('description')
                            ->maxLength(255) // Individual max length, but dynamically validated
                            ->placeholder('Add a description')
                            ->hint('Max 180 characters, total combined length with other fields must not exceed 275')
                            ->reactive(),
                        Forms\Components\FileUpload::make('image')
                            ->image(),
                        Forms\Components\TextInput::make('site_url')
                            ->maxLength(255) // Individual max length, but dynamically validated
                            ->placeholder('Add a site URL e.g https://example.com')
                            ->default(env('APP_URL'))
                            ->reactive(),
                        Forms\Components\SpatieTagsInput::make('tags')
                            ->placeholder('Add a tag')
                            ->nestedRecursiveRules([
                                'min:3',
                                'max:50',
                            ])
                            ->reactive()
                            ->rule(function ($get) {
                                return function ($attribute, $value, $fail) use ($get) {
                                    $description = $get('description') ?? '';
                                    $siteUrl = $get('site_url') ?? '';
                                    $image = $get('image') ?? '';
                                    $tags = is_array($value) ? implode(',', $value) : '';

                                    $descriptionLength = strlen($description);
                                    $siteUrlLength = strlen($siteUrl);
                                    $tagsLength = strlen($tags);

                                    $totalLength = $descriptionLength + $siteUrlLength + $tagsLength;

                                    if ($totalLength > 275) {
                                        $fail(
                                            "The total length of description, site URL, and tags must not exceed 275 characters. Current total: {$totalLength}."
                                        );
                                    }

                                    if (empty($description) && empty($image)) {
                                        $fail(
                                            "Either post description or image is required."
                                        );
                                    }
                                };
                            }),
                    ]),
                    Forms\Components\Section::make([
                        Forms\Components\DateTimePicker::make('published_at')
                            ->required()
                            ->seconds(false)
                            ->default(now()),
                    ])->grow(false),
                ])->from('md')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        // Only render the tooltip if the column content exceeds the length limit.
                        return $state;
                    }),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('site_url')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\BooleanColumn::make('is_posted'),
                Tables\Columns\BooleanColumn::make('is_posted_to_twitter'),
                Tables\Columns\BooleanColumn::make('is_posted_to_facebook'),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime('F j, Y g:i A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(''),
                Tables\Actions\DeleteAction::make()->label(''),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->modifyQueryUsing(function (Builder $query) {
                return $query->latest();
            });
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
