<?php

namespace App\Filament\Resources\LoanResource\Pages;

use App\Filament\Resources\LoanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Book;
class CreateLoan extends CreateRecord
{
    protected static string $resource = LoanResource::class;



    protected function afterCreate(): void
    {
        $this->record->book->status = Book::STATUS_BORROWED;
        $this->record->book->save();
    }
}
