<?php

namespace App\Filament\Resources\Packages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PackageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // القسم الأول: المعلومات الأساسية
            Section::make('البيانات التعريفية')
                ->description('المعلومات العامة والرمزية للطرد')
                ->icon('heroicon-o-identification')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('package_code')
                            ->label('كود الطرد')
                            ->copyable()
                            ->weight('bold')
                            ->color('primary')
                            ->icon('heroicon-m-qr-code'),

                        TextEntry::make('name')
                            ->label('اسم الطرد')
                            ->size('lg')
                            ->weight('bold'),

                        TextEntry::make('type')
                            ->label('نوع الطرد')
                            ->badge()
                            ->color('info'),
                    ]),
                ]),

            // القسم الثاني: تفاصيل الكميات والحالة
            Section::make('تفاصيل التوزيع')
                ->icon('heroicon-o-truck')
                ->schema([
                    Grid::make(4)->schema([
                        TextEntry::make('unit')
                            ->label('الوحدة المستعملة')
                            ->icon('heroicon-m-beaker'),

                        TextEntry::make('target_quantity')
                            ->label('الكمية المستهدفة')
                            ->numeric()
                            ->weight('bold')
                            ->suffix(' قطعة/طرد'),

                        TextEntry::make('status')
                            ->label('حالة الطرد')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'closed' => 'danger',
                                default => 'gray',
                            }),

                        // إضافة ذكية: عرض عدد الذين استلموا فعلياً في واجهة العرض
                        TextEntry::make('deliveries_count')
                            ->label('تم التوزيع لـ')
                            ->state(fn ($record) => $record->deliveries()->count() . ' مستفيد')
                            ->weight('bold')
                            ->color('success'),
                    ]),
                ]),

            // القسم الثالث: التوقيتات والملاحظات
            Grid::make(2)->schema([
                Section::make('التواريخ')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('تاريخ الإنشاء')
                            ->dateTime('Y-m-d H:i')
                            ->icon('heroicon-o-calendar'),
                        TextEntry::make('updated_at')
                            ->label('آخر تحديث')
                            ->dateTime('Y-m-d H:i')
                            ->since(), // يعرض التوقيت بصيغة "منذ..."
                    ]),

                Section::make('ملاحظات إضافية')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('notes')
                            ->label('')
                            ->placeholder('لا توجد ملاحظات مسجلة.')
                            ->markdown(), // يدعم تنسيق النصوص إذا وجدت
                    ]),
            ]),
        ]);
    }
}
