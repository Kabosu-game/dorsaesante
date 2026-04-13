<?php

namespace App\Filament\Widgets;

use App\Models\EmergencyAlert;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class EmergencyAlertsTable extends BaseWidget
{
    protected static ?string $heading = 'Alertes d\'urgence récentes';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                EmergencyAlert::query()
                    ->with(['user:id,name'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Patient')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'medical'  => 'danger',
                        'accident' => 'warning',
                        'fire'     => 'danger',
                        default    => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'sent'       => 'danger',
                        'received'   => 'warning',
                        'dispatched' => 'info',
                        'resolved'   => 'success',
                        default      => 'gray',
                    }),
                Tables\Columns\TextColumn::make('address')->label('Adresse')->limit(30),
                Tables\Columns\TextColumn::make('created_at')->label('Date')->dateTime('d/m/Y H:i')->sortable(),
            ]);
    }
}
