<?php

namespace App\Filament\Resources;

use Z3d0X\FilamentLogger\Resources\ActivityResource;

class ModActivityResource extends ActivityResource
{
 
    protected static ?string $navigationGroup = 'Maintenance';
    protected static ?int $navigationSort = 10;

    public static function getNavigationGroup(): ?string
    {
        return 'Maintenance';
    }
}
