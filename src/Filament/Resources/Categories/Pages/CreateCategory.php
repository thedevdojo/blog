<?php

namespace Devdojo\Blog\Filament\Resources\Categories\Pages;

use Devdojo\Blog\Filament\Resources\Categories\CategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
}
