<?php

namespace App\Filament\Resources\BookAdminResource\Pages;

use App\Filament\Resources\BookAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Actions\Action;
use Illuminate\Support\Collection;
use App\Models\Tag;
use Illuminate\Support\Str;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Book;

class ListBookAdmins extends ListRecords

{
    protected static string $resource = BookAdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Ajouter un livre')
                ->icon('heroicon-o-plus'),
        ];
    }
    public static function bulkAddTagsAction(): Action
    {
        return Action::make('bulkAddTagsAction')
            ->label('Ajouter un mot-clé')
            ->modalDescription('Ajouter un ou plusieurs mot-clés à tous les livres sélectionnés')
            ->icon('heroicon-o-tag')
            ->form([
                Forms\Components\Select::make('add_tags')
                    ->label('Mots-clés')
                    ->multiple()
                    ->relationship('tags', 'title')
                    ->dehydrated()  // pour récupérer les données dans la fonction action à partir d"une relation
                    ->preload()
                    ->action(function (array $data, $livewire): void {

                        $ids            = $livewire->getSelectedTableRecords()->pluck('id')->toArray();
                        dd($ids);
                    }),

            ])
            ->action(function (array $data, $livewire): void {

                $ids            = $livewire->getSelectedTableRecords()->pluck('id')->toArray();
                dd($ids);

                /*
                foreach ($data['add_tags'] as $tag) {

                    if(!$Tag = Tag::where('title', $tag)->first()) 
                    {
                        $Tag = Tag::create([
                            'title' => $tag,
                            'slug' => Str::slug($tag),
                        ]);
                    }

                    foreach ($livewire->getSelectedTableRecords() as $record) {

                        dd($record->title);
                       
                    }
                
                }   
                */


                //                dd($data, $arguments, $livewire->getFormData());
            });
    }


    public static function vvvvbulkAddTagsAction(): Action
    {
        return Action::make('bulkAddTagsAction')
            ->label('Ajouter un mot-clé')
            ->modalDescription('Ajouter un ou plusieurs mot-clés à tous les livres sélectionnés')
            ->icon('heroicon-o-tag')
            ->form([
                Forms\Components\Select::make('add_tags')
                    ->label('Mots-clés')
                    ->multiple()
                    ->relationship('tags', 'title')
                    ->dehydrated()  // pour récupérer les données dans la fonction action à partir d"une relation
                    ->preload(),
            ])
            ->action(function ($livewire): void {


                dd($livewire->getSelectedTableRecords());

                foreach ($data['add_tags'] as $tag) {

                    if(!$Tag = Tag::where('title', $tag)->first()) 
                    {
                        $Tag = Tag::create([
                            'title' => $tag,
                            'slug' => Str::slug($tag),
                        ]);
                    }

                    foreach ($livewire->getSelectedTableRecords() as $record) {

                        dd($ $record->tags()->attach($Tag));
                       
                    }
                
                }        


                

                //                dd($data, $arguments, $livewire->getFormData());
            });
    }

    public function getTabs(): array
    {
        return [
            __('Livres sur étagère') => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Book::STATUS_ON_SHELF)->orderBy('created_at', 'desc'))
                ->badge(fn () => Book::where('status', Book::STATUS_ON_SHELF)->where('missing', false)->count()),
            __('Livres déposés au bureau') => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Book::STATUS_DROP_OFF)->orderBy('created_at', 'desc'))
                ->badge(fn () => Book::where('status', Book::STATUS_DROP_OFF)->count()),
            __('Livres à qualifier') => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Book::STATUS_TO_QUALIFY)->orderBy('created_at', 'desc'))
                ->badge(fn () => Book::where('status', Book::STATUS_TO_QUALIFY)->count()),
            __('Livres qualifiés') => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Book::STATUS_QUALIFIED)->orderBy('created_at', 'desc'))
                ->badge(fn () => Book::where('status', Book::STATUS_QUALIFIED)->count()),
            __('Livres rejetés') => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Book::STATUS_REJECTED)->orderBy('created_at', 'desc'))
                ->badge(fn () => Book::where('status', Book::STATUS_REJECTED)->count()),
            __('Livres à acquérir') => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Book::STATUS_AQUISITION_REQUEST)->orderBy('created_at', 'desc'))
                ->badge(fn () => Book::where('status', Book::STATUS_AQUISITION_REQUEST)->count()),
            __('Tous les livres') => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->orderBy('created_at', 'desc'))
                ->badge(fn () => Book::count()),
        ];
    }



}
