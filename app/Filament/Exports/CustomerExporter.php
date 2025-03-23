<?php

namespace App\Filament\Exports;

use App\Models\Customer;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CustomerExporter extends Exporter
{
    protected static ?string $model = Customer::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name'),
            ExportColumn::make('house_unit_no'),
            ExportColumn::make('telephone_no'),
            ExportColumn::make('address'),
            ExportColumn::make('email'),
            ExportColumn::make('city_district'),
            ExportColumn::make('ic_mykad'),
            ExportColumn::make('postal_code'),
            ExportColumn::make('date_of_birth'),
            ExportColumn::make('gender'),
            ExportColumn::make('country'),
            ExportColumn::make('notes_comments'),
            ExportColumn::make('attachment'),
            ExportColumn::make('team_id'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your customer export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
