<?php

namespace App\Filament\Resources\EstimateResource\Pages;

use Filament\Actions;
use App\Models\Estimate;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\EstimateResource;

class CreateEstimate extends CreateRecord
{
    protected static string $resource = EstimateResource::class;

    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();
        return $resource::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = new ($this->getModel())($data);
        $record->estimate_number = Estimate::where('team_id', Filament::getTenant()->id)->orderBy('id', 'desc')->first()?->id + 1;
      
      
        if (
            static::getResource()::isScopedToTenant() &&
            ($tenant = Filament::getTenant())
        ) {
            return $this->associateRecordWithTenant($record, $tenant);
        }
   
        $record->save();

        return $record;
    }

    protected function associateRecordWithTenant(Model $record, Model $tenant): Model
    {
        $relationship = static::getResource()::getTenantRelationship($tenant);
        
        $temp = $record->toArray() ;
     
        unset($record->item_estimate);
        $temp2 = $relationship->save($record);
        $temp2->items()->sync(array_column($temp['item_estimate'], 'id') );
        return $temp2;
    }


}
