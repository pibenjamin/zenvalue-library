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
                    ->preload(),

            ])
            ->action(function (array $data, $livewire): void {

                dd($data, $livewire->getSelectedTableRecords());

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
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Book::STATUS_ON_SHELF))
                ->badge(fn () => Book::where('status', Book::STATUS_ON_SHELF)->count()),
            __('Livres à qualifier') => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Book::STATUS_CONTRIBUTION_TO_QUALIFY))
                ->badge(fn () => Book::where('status', Book::STATUS_CONTRIBUTION_TO_QUALIFY)->count()),
            __('Livres qualifiés') => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Book::STATUS_CONTRIBUTION_QUALIFIED))
                ->badge(fn () => Book::where('status', Book::STATUS_CONTRIBUTION_QUALIFIED)->count()),
            __('Livres rejetés') => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Book::STATUS_CONTRIBUTION_REJECTED))
                ->badge(fn () => Book::where('status', Book::STATUS_CONTRIBUTION_REJECTED)->count()),
        ];
    }



}
