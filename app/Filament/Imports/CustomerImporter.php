<?php

namespace App\Filament\Imports;

use App\Models\Customer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class CustomerImporter extends Importer
{
    protected static ?string $model = Customer::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('house_unit_no')
                ->rules(['max:255']),
            ImportColumn::make('telephone_no')
                ->rules(['max:255']),
            ImportColumn::make('address'),
            ImportColumn::make('email')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255']),
            ImportColumn::make('city_district')
                ->rules(['max:255']),
            ImportColumn::make('ic_mykad')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('postal_code')
                ->rules(['max:255']),
            ImportColumn::make('date_of_birth')
                ->rules(['date']),
            ImportColumn::make('gender'),
            ImportColumn::make('country')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('notes_comments'),
            ImportColumn::make('attachment'),
            ImportColumn::make('team_id')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
        ];
    }

    public function resolveRecord(): ?Customer
    {
        // return Customer::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Customer();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your customer import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
