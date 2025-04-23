<?php

namespace App\Filament\Resources\TrainingResource\Widgets;

use App\Models\Loan;
use App\Models\User;
use App\Models\Book;
use App\Models\Training;
use App\Models\Tag;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class BookTrainingsWidget extends BaseWidget
{
    //use HasWidgetShield;

    public ?Model $record                       = null;
    protected int|string|array $columnSpan      = 'full';
    protected static ?bool $collapsible         = true;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Book::query()
                    ->join('training_books', 'books.id', '=', 'training_books.book_id')
                    ->where('training_books.training_id', $this->record->id)
                    //->where('books.status', Book::STATUS_ON_SHELF)
            )
            ->heading('Les livres liés et/ou permettant d\'approfondir cette formation')
            ->description('Cette bibliographie peut contenir des livres qui ne sont pas encore dans notre catalogue. Si vous souhaitez les acquérir, veuillez nous le signaler.')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Livre')
                    ->url(function (Book $record) {
                        if (auth()->user()->hasRole('super_admin')) {
                            return route('filament.admin.resources.book-admins.edit', $record->book_id);
                        }
                        return route('filament.admin.resources.books.view', $record->book_id);
                    })
                    ->openUrlInNewTab()
                    ->sortable()
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\ImageColumn::make('cover_url')
                    ->label('Couverture')
                    ->height(50)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('authors.name')
                    ->label('Auteurs')
                    ->badge()
                    ->color('gray')
                    ->wrap()
                    ->state(function ($record) {
                        return Book::find($record->book_id)->authors->pluck('name');
                    })
                    ->verticallyAlignStart()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('tags.title')
                    ->label('Mots-clés')
                    ->state(function ($record) {
                        return Book::find($record->book_id)->tags->pluck('title');
                    })
                    ->wrap()
                    ->badge()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->sortable()
                    ->badge()
                    ->state(function (Book $record): string {
                        return $record->getStatusLabel();
                    })
                    ->color(fn (Book $record): string => $record->getStatusColor()),

            ])
            ->poll('3s')
            ->filters([                   
            ]);
    }

    protected function getTablePollingInterval(): ?string
    {
        return null;
    }

    public function getListeners(): array
    {
        return [
            'refresh-widget-book-trainings' => '$refresh',
        ];
    }
}