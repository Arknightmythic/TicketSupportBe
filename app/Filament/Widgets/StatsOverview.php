<?php

namespace App\Filament\Widgets;

use App\Models\Tickets;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {

        $newtickets = Tickets::whereMonth('created_at', Carbon::now()->month)->whereYear('created_at',Carbon::now()->year)->count();
        $waitingtickets = Tickets::whereStatus('waiting')->count();
        $closetickets = Tickets::whereStatus('close')->count();
        return [
            Stat::make('New Ticket of the month', $newtickets),
            Stat::make('Total tickets still waiting', $waitingtickets),
            Stat::make('Total tickets was closed', $closetickets),
        ];
    }
}
