<?php

namespace App\Filament\Widgets;

use App\Models\Tickets;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;


class WaitingTickets extends BaseWidget
{
    protected static ?int $sort=3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Tickets::query()->whereStatus('waiting')
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn(string $state): string => match($state){
                    'waiting' =>'gray',
                    'process' =>'info',
                    'close' =>'danger',
                })
            ])
           
            ->actions([
                Action::make('process')
                    ->button()
                    ->color('info')
                    ->requiresConfirmation()
                    ->extraAttributes(['class' => 'w-20'])
                    ->action(function (Tickets $ticket) {
                        Tickets::find($ticket->id)->update([
                            'status' => 'process'
                        ]);
                        Notification::make()->success()->title('Ttickets Approved')->body('Tickets is on process now')->icon('heroicon-o-check')->send();
                    })
                    ->hidden(fn(Tickets $ticket) => $ticket->status !== 'waiting'),

                // Close Button
                Action::make('close')
                    ->button()
                    ->color('danger')
                    ->requiresConfirmation()
                    ->extraAttributes(['class' => 'w-20'])
                    ->action(function (Tickets $ticket) {
                        $ticket->update(['status' => 'close']);
                        Notification::make()
                            ->success()
                            ->title('Ticket Closed')
                            ->body('The ticket has been closed.')
                            ->send();
                    })
                    ->hidden(fn(Tickets $ticket) => $ticket->status !== 'process'),
                ]);
    }
}
