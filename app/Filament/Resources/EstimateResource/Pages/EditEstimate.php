<?php

namespace App\Filament\Resources\EstimateResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\EstimateResource;

class EditEstimate extends EditRecord
{
    protected static string $resource = EstimateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();
        return $resource::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['item_estimate'] = $this->getRecord()->items->map(
            function ($item) {
                $item->amount = number_format($item->pivot->quantity * $item->price, 2, '.', '');
                return $item ;
            }
        )->toArray();
        
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $syncData = [];
        foreach ($data['item_estimate'] as $item) {
            $syncData[$item['id']] = [
                'quantity' => $item['pivot']['quantity']
            ];
        }
        // dd($syncData);
        $record->items()->sync($syncData);
        unset($data['item_estimate']);
        $record->update($data);

        return $record;
    }
}
