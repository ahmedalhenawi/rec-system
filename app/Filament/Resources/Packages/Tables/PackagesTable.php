<?php

namespace App\Filament\Resources\Packages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('package_code')
                    ->label('رقم الطرد')
                    ->copyable() // ميزة نسخ الرقم بضغطة واحدة
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),

                TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->color('info'),

                TextColumn::make('target_quantity')
                    ->label('المستهدف')
                    ->numeric()
                    ->alignCenter(),

                TextColumn::make('deliveries_count')
                    ->counts('deliveries')
                    ->label('المنجز')
                    ->weight('bold')
                    ->color(fn ($record) => $record->deliveries_count >= $record->target_quantity ? 'success' : 'warning')
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'closed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'نشط',
                        'closed' => 'مغلق',
                        default => $state,
                    }),
            ])
            ->recordActions([
                ViewAction::make()->label('عرض'),
                EditAction::make()->label('تعديل'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
