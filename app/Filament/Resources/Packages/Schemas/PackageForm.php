<?php

namespace App\Filament\Resources\Packages\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات الطرد الأساسية')
                    ->description('أدخل تفاصيل الطرد والكميات المستهدفة هنا.')
                    ->schema([
                        Grid::make(1) // تقسيم الحقول على 3 أعمدة
                        ->schema([
                            TextInput::make('package_code')
                                ->label('رقم الطرد')
                                ->placeholder('سيتم توليده تلقائياً')
                                ->disabled() // يمنع التعديل اليدوي
                                ->dehydrated(false) ,

                            TextInput::make('name')
                                ->label('اسم الطرد')
                                ->required(),

                            Select::make('type')
                                ->label('نوع الطرد')
                                ->options([
                                    'غذائي' => 'غذائي',
                                    'صحي' => 'صحي',
                                    'كسوة' => 'كسوة',
                                    'مستلزمات ايواء' => 'مستلزمات ايواء',
                                    'نقدي' => 'نقدي',
                                ])
                                ->required(),
                        ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('unit')
                                    ->label('الوحدة')
                                    ->required(),

                                TextInput::make('target_quantity')
                                    ->label('الكمية المستهدفة')
                                    ->numeric()
                                    ->suffix('وحدة')
                                    ->required(),

                                Select::make('status')
                                    ->label('حالة الطرد')
                                    ->options([
                                        'active' => 'نشط (مفتوح للتوزيع)',
                                        'closed' => 'مغلق (منتهي)',
                                    ])
                                    ->default('active')
                                    ->required(),
                            ]),

                        Textarea::make('notes')
                            ->label('ملاحظات إضافية')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->icon('heroicon-o-cube'),
            ]);
    }
}
