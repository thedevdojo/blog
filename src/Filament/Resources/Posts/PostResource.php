<?php

namespace Devdojo\Blog\Filament\Resources\Posts;

use BackedEnum;
use Devdojo\Blog\Filament\Resources\Posts\Pages\CreatePost;
use Devdojo\Blog\Filament\Resources\Posts\Pages\EditPost;
use Devdojo\Blog\Filament\Resources\Posts\Pages\ListPosts;
use Devdojo\Blog\Models\Category;
use Devdojo\Blog\Models\Post;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $slug = 'posts';

    protected static BackedEnum|string|null $navigationIcon = 'phosphor-pencil-line-duotone';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        $userModel = config('devdojo.blog.settings.user_model') ?? config('auth.providers.users.model');

        return $schema
            ->components([
                TextInput::make('title')
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                    ->required()
                    ->maxLength(191),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(191),
                RichEditor::make('body')
                    ->required()
                    ->fileAttachmentsDisk(config('filament.default_filesystem_disk'))
                    ->fileAttachmentsDirectory('attachments')
                    ->fileAttachmentsVisibility('public')
                    ->columnSpanFull(),
                Textarea::make('excerpt')
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->image()
                    ->disk(config('filament.default_filesystem_disk'))
                    ->directory('posts'),
                TextInput::make('seo_title')
                    ->maxLength(191),
                Select::make('author_id')
                    ->label('Author')
                    ->options(
                        $userModel::all()
                            ->mapWithKeys(fn ($user) => [
                                $user->id => $user->name
                                    ?? $user->username
                                    ?? $user->email,
                            ])
                            ->toArray()
                    )
                    ->searchable()
                    ->required(),
                Select::make('category_id')
                    ->label('Category')
                    ->options(Category::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Textarea::make('meta_description')
                    ->columnSpanFull(),
                Textarea::make('meta_keywords')
                    ->columnSpanFull(),
                Select::make('status')
                    ->required()
                    ->options([
                        'DRAFT' => 'Draft',
                        'PUBLISHED' => 'Published',
                        'PENDING' => 'Pending',
                    ]),
                Toggle::make('featured')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable(),
                ImageColumn::make('image'),
                TextColumn::make('status'),
                IconColumn::make('featured')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}
