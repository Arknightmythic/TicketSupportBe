<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketsResource\Pages;
use App\Filament\Resources\TicketsResource\RelationManagers;
use App\Models\Tickets;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketsResource extends Resource
{
    protected static ?string $model = Tickets::class;

    public static function canCreate(): bool
    {
        return false;
    }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                    ->color(fn(string $state): string => match ($state) {
                        'waiting' => 'gray',
                        'process' => 'info',
                        'close' => 'danger',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'waiting' => 'Waiting',
                        'process' => 'Process',
                        'Close' => 'Closed',
                    ])
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTickets::route('/create'),
            'edit' => Pages\EditTickets::route('/{record}/edit'),
            'view' => Pages\ViewTickets::route('/{record}'),
        ];
    }
}
