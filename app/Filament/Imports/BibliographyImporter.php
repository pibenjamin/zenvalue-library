<?php

namespace App\Filament\Imports;

use App\Models\Bibliography;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class BibliographyImporter extends Importer
{
    protected static ?string $model = Bibliography::class;

    public static function getColumns(): array
    {
        return [
            return [
                ImportColumn::make('Titre')
                    ->requiredMapping()
                    ->rules(['required', 'max:255']),
                ImportColumn::make('Auteurs')
                    ->requiredMapping()
                    ->rules(['required', 'max:32']),
                ImportColumn::make('Langue')
                    ->requiredMapping()
                    ->rules(['required', 'max:32']),
            ];
        ];
    }

    public function resolveRecord(): ?Bibliography
    {
        // return Bibliography::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Bibliography();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your bibliography import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
