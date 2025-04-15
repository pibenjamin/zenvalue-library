<?php

namespace App\Filament\Resources\TrainingResource\Widgets;

use App\Models\Doc;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;

class DocsTrainingsWidget extends BaseWidget
{
    //use HasWidgetShield;

    public ?Model $record                       = null;
    protected int|string|array $columnSpan      = 'full';
    protected static ?bool $collapsible         = true;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Doc::query()
                    ->join('training_docs', 'docs.id', '=', 'training_docs.doc_id')
                    ->where('training_docs.training_id', $this->record->id)
            )
            ->heading('Les documents liés')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Document')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('authors.name')
                    ->label('Auteurs')
                    ->badge()
                    ->openUrlInNewTab()
                    ->wrap()
                    ->tooltip(fn (Doc $record) => $record->authors->pluck('name')->implode(' - '))
            ])
            ->poll('3s')
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Télécharger')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->disableLabel()
                    ->tooltip('Télécharger ce livre')
                    ->button()
                    ->color('primary')
                    ->action(function (Doc $record) {
                        if (!Storage::disk('public')->exists($record->path)) {
                            Notification::make()
                                ->title('Erreur')
                                ->body('Le fichier n\'existe pas')
                                ->danger()
                                ->send();
                            return;
                        }

                        Notification::make()
                            ->title('Téléchargement effectué')
                            ->success()
                            ->send();

                        return Storage::disk('public')->download($record->path);
                    }),
            ])
            ->filters([
                //
            ]);
    }

    protected function getTablePollingInterval(): ?string
    {
        return null;
    }

    public function getListeners(): array
    {
        return [
            'refresh-widget-docs-trainings' => '$refresh',
        ];
    }
}
