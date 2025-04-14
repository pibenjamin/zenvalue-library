<?php

namespace App\Filament\Resources\TrainingResource\Widgets;

use App\Models\Loan;
use App\Models\User;
use App\Models\Link;
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

class LinksTrainingsWidget extends BaseWidget
{
    //use HasWidgetShield;

    public ?Model $record                       = null;
    protected int|string|array $columnSpan      = 'full';
    protected static ?bool $collapsible         = true;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Link::query()
                    ->join('training_links', 'links.id', '=', 'training_links.link_id')
                    ->where('training_links.training_id', $this->record->id)
            )
            ->heading('Les liens vers des ressources en ligne')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Lien')
                    ->url(fn (Link $record) => $record->url)
                    ->openUrlInNewTab()
                    ->sortable()
                    ->toggleable()
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
            'refresh-widget-links-trainings' => '$refresh',
        ];
    }
}
