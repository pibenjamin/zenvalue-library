<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class AddBookActionWidget extends Widget
{
    protected static ?int $sort = -2;

    protected static bool $isLazy = false;

    /**
     * @var view-string
     */
    protected static string $view = 'filament.widgets.add-book-action';
}
