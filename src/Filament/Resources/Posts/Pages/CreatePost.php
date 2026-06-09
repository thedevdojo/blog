<?php

namespace Devdojo\Blog\Filament\Resources\Posts\Pages;

use Devdojo\Blog\Filament\Resources\Posts\PostResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;
}
