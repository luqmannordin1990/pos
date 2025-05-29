<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use Filament\Actions;
use App\Models\Invoice;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\InvoiceResource;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();
        return $resource::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = new ($this->getModel())($data);
        $record->invoice_number = Invoice::where('team_id', Filament::getTenant()->id)->orderBy('id', 'desc')->first()?->id + 1;
      
      
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
     
        unset($record->item_invoice);
        $temp2 = $relationship->save($record);

        $syncData = [];
        foreach ($temp['item_invoice'] as $item) {
            $syncData[$item['id']] = [
                'quantity' => $item['pivot']['quantity']
            ];
        }

       
        $temp2->items()->sync($syncData);
        return $temp2;
    }

}
