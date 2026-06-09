<?php

namespace Devdojo\Blog\Filament\Resources\Posts\Pages;

use Devdojo\Blog\Filament\Resources\Posts\PostResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
