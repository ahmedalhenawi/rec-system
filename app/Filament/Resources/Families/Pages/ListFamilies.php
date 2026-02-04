<?php

namespace App\Filament\Resources\Families\Pages;

use App\Filament\Imports\FamilyImporter;
use App\Filament\Resources\Families\FamilyResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListFamilies extends ListRecords
{
    protected static string $resource = FamilyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(FamilyImporter::class)
                ->label('استيراد عائلات')
                ->icon('heroicon-o-arrow-up-tray')
                ->visible(fn () => auth()->user()->hasRole('super_admin')),
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('family.all'))
                ->icon('heroicon-m-list-bullet'),

            'north' => Tab::make(__('family.governorates.north'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('governorate', 'north'))
                ->icon('heroicon-m-map-pin')
                ->badge(\App\Models\Family::where('governorate', 'north')->count()),

            'middle' => Tab::make(__('family.governorates.middle'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('governorate', 'middle'))
                ->icon('heroicon-m-map-pin')
                ->badge(\App\Models\Family::where('governorate', 'middle')->count()),

            'south' => Tab::make(__('family.governorates.south'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('governorate', 'south'))
                ->icon('heroicon-m-map-pin')
                ->badge(\App\Models\Family::where('governorate', 'south')->count()),
        ];
    }


}
