<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\User;
class EmployeesOverview extends Widget
{
    protected static string $view = 'filament.widgets.employees-overview';
    protected int|string|array $columnSpan = 'full';


    public function getUsers()
    {
        return User::query()
            ->orderBy('email', 'asc')
            ->where('email', '!=', config('app.admin_email'))
            ->get();
    }

    protected function getViewData(): array
    {
        return [
            'users' => $this->getUsers(),
        ];
    }

}
