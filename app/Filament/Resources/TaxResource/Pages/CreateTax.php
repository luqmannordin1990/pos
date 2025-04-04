<?php

namespace App\Filament\Resources\TaxResource\Pages;

use App\Filament\Resources\TaxResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTax extends CreateRecord
{
    protected static string $resource = TaxResource::class;

    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();
        return $resource::getUrl('index');
    }
}
