<?php

namespace App\Filament\Resources\Packages\Pages;

use App\Filament\Resources\Packages\PackageResource;
use App\Filament\Resources\Packages\Schemas\PackageInfolist;
use App\Filament\Imports\DeliveryImporter;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Schemas\Schema;

class ViewPackage extends ViewRecord
{
    protected static string $resource = PackageResource::class;


    public function schema(Schema $schema): Schema
    {
        return PackageInfolist::configure($schema);
    }


    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('تعديل البيانات'),

            ImportAction::make()
                ->label('بدء استيراد المستفيدين')
                ->icon('heroicon-o-cloud-arrow-up')
                ->color('success')
                ->importer(DeliveryImporter::class)
                ->options(fn () => [
                    'package_id' => $this->record->getKey(),
                ]),
        ];
    }
}

