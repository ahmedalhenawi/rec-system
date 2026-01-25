<?php

namespace App\Filament\Resources\Families\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Support\Carbon;

class FamiliesTable
{
    public static function configure(Table $table): Table
    {
        $textColumn = TextColumn::make('breadwinner.dob') // استخدام العلاقة مباشرة
        ->label(__('person.dob'))
            ->date();

        return $table
            ->columns([
                TextColumn::make('family_code')
                    ->label(__('family.family_code'))
                    ->copyable()
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('أضيف بواسطة')
                    ->visible(fn () => auth()->user()->hasRole('super_admin')) // يظهر فقط للسوبر أدمن
                    ->badge()
                    ->color('gray'),

                // --- بيانات رب الأسرة ---


                // 2. عمود اسم رب الأسرة
                TextColumn::make('breadwinner.full_name')
                    ->label(__('person.head_name'))
                    ->searchable(query: function ($query, string $search) {
                        return $query->whereHas('breadwinner', function ($q) use ($search) {
                            $q->where('full_name', 'like', "%{$search}%");
                        });
                    }),

                 // 3. عمود رقم الهوية
                TextColumn::make('breadwinner.national_id')
                    ->label(__('person.national_id'))
                    ->searchable(),

                  // 4. عمود حالة العمل
                IconColumn::make('breadwinner.is_working')
                    ->label(__('person.is_working'))
                    ->boolean(),
                $textColumn,


                // --- بيانات العائلة الأساسية ---
                TextColumn::make('social_status')
                    ->label(__('family.social_status'))
                    ->formatStateUsing(fn ($state) => __('family.social_statuses.' . $state))
                    ->searchable(),

                TextColumn::make('phone')
                    ->label(__('family.phone'))
                    ->copyable()
                    ->searchable(),

                TextColumn::make('governorate')
                    ->label(__('family.governorate'))
                    ->formatStateUsing(fn ($state) => __('family.governorates.' . $state))
                    ->searchable(),

                TextColumn::make('full_address')
                    ->label(__('family.full_address'))
                    ->searchable(),

                IconColumn::make('is_displaced')
                    ->label(__('family.is_displaced'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('displacement_type')
                    ->label(__('family.displacement_type'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn ($state) => __('family.displacement_types.' . $state)),

                TextColumn::make('males_count')
                    ->label(__('family.males_count'))
                    ->counts([
                        'persons as males_count' => fn ($query) => $query->where('gender', 'male'),
                    ])
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('females_count')
                    ->label(__('family.females_count'))
                    ->counts([
                        'persons as females_count' => fn ($query) => $query->where('gender', 'female'),
                    ])
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('persons_count')
                    ->label(__('family.total_members'))
                    ->counts('persons')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),


                TextColumn::make('income_range')
                    ->label(__('family.income_range'))
                    ->formatStateUsing(fn ($state) => __('family.income_ranges.' . $state))
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('family.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // 1. مجموعة الحالة الصحية والعمل
                Filter::make('health_and_work')
                    ->form([
                        Section::make('الوضع الصحي والعمل')
                            ->description('تصفية العائلات التي لديها حالات إعاقة، أمراض مزمنة، أو حسب حالة عمل رب الأسرة')
                            ->schema([
                                Select::make('has_disabilities')
                                    ->label('وجود إعاقة')
                                    ->options(['1' => 'يوجد أفراد ذوي إعاقة', '0' => 'لا يوجد ذوي إعاقة'])
                                    ->placeholder('الكل'),
                                Select::make('has_chronic_diseases')
                                    ->label('مرض مزمن')
                                    ->options(['1' => 'يوجد أفراد لديهم أمراض', '0' => 'لا يوجد أمراض'])
                                    ->placeholder('الكل'),
                                Select::make('head_is_working')
                                    ->label('رب الأسرة يعمل')
                                    ->options(['1' => 'نعم (يعمل)', '0' => 'لا (عاطل عن العمل)'])
                                    ->placeholder('الكل'),
                            ])->columns(3)->collapsible(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['has_disabilities'] !== null && $data['has_disabilities'] !== '', function ($q) use ($data) {
                                return $data['has_disabilities'] === '1'
                                    ? $q->whereHas('persons', fn($sq) => $sq->has('disabilities'))
                                    : $q->whereDoesntHave('persons', fn($sq) => $sq->has('disabilities'));
                            })
                            ->when($data['has_chronic_diseases'] !== null && $data['has_chronic_diseases'] !== '', function ($q) use ($data) {
                                return $data['has_chronic_diseases'] === '1'
                                    ? $q->whereHas('persons', fn($sq) => $sq->has('chronicDiseases'))
                                    : $q->whereDoesntHave('persons', fn($sq) => $sq->has('chronicDiseases'));
                            })
                            ->when($data['head_is_working'] !== null && $data['head_is_working'] !== '', function ($q) use ($data) {
                                return $q->whereHas('persons', fn($sq) => $sq->where('relation', 'head')
                                    ->where('is_working', $data['head_is_working'] === '1'));
                            });
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        // التحقق باستخدام !== '' لضمان ظهور الـ Indicator حتى لو كانت القيمة '0'
                        if (($data['has_disabilities'] ?? '') !== '') {
                            $indicators[] = ($data['has_disabilities'] === '1' ? 'عائلات بها إعاقة' : 'بدون إعاقة');
                        }
                        if (($data['has_chronic_diseases'] ?? '') !== '') {
                            $indicators[] = ($data['has_chronic_diseases'] === '1' ? 'عائلات بها مرض مزمن' : 'بدون أمراض مزمنة');
                        }
                        if (($data['head_is_working'] ?? '') !== '') {
                            $indicators[] = ($data['head_is_working'] === '1' ? 'رب الأسرة يعمل' : 'رب الأسرة لا يعمل');
                        }
                        return $indicators;
                    })
                    ->columnSpanFull(),

                // 2. مجموعة بيانات رب الأسرة والبحث عن الأفراد
                Filter::make('head_details')
                    ->form([
                        Section::make('بيانات الهوية والأسماء')
                            ->schema([
                                TextInput::make('head_name')->label(__('person.head_name')),
                                TextInput::make('national_id')->label('هوية رب الأسرة'),
                                TextInput::make('member_national_id')->label('هوية أحد أفراد العائلة'),
                                Fieldset::make('تاريخ ميلاد رب الأسرة')
                                    ->schema([
                                        DatePicker::make('dob_from')->label('من'),
                                        DatePicker::make('dob_until')->label('إلى'),
                                    ])->columns(2)->columnSpan(1),
                            ])->columns(3)
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->whereHas('persons', function (Builder $q) use ($data) {
                                $q->where('relation', 'head')
                                    ->when($data['head_name'], fn($sq, $name) => $sq->where('full_name', 'like', "%{$name}%"))
                                    ->when($data['national_id'], fn($sq, $id) => $sq->where('national_id', 'like', "%{$id}%"))
                                    ->when($data['dob_from'], fn($sq, $date) => $sq->whereDate('dob', '>=', $date))
                                    ->when($data['dob_until'], fn($sq, $date) => $sq->whereDate('dob', '<=', $date));
                            })
                            ->when($data['member_national_id'], function ($q, $id) {
                                $q->whereHas('persons', fn($sq) => $sq->where('national_id', 'like', "%{$id}%"));
                            });
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['head_name']) $indicators[] = 'رب الأسرة: ' . $data['head_name'];
                        if ($data['national_id']) $indicators[] = 'هوية الرب: ' . $data['national_id'];
                        if ($data['member_national_id']) $indicators[] = 'هوية فرد: ' . $data['member_national_id'];
                        if ($data['dob_from'] || $data['dob_until']) {
                            $indicators[] = 'ميلاد الرب: ' . ($data['dob_from'] ?? '...') . ' إلى ' . ($data['dob_until'] ?? '...');
                        }
                        return $indicators;
                    })
                    ->columnSpanFull(),

                // 3. مجموعة الحالة الاجتماعية والسكن والدخل
                Filter::make('social_economic')
                    ->form([
                        Section::make('الحالة الاجتماعية والسكن')
                            ->schema([
                                Select::make('social_status')
                                    ->label(__('family.social_status'))
                                    ->options(collect(__('family.social_statuses'))->mapWithKeys(fn($label, $key) => [$key => $label])->toArray())
                                    ->placeholder('الكل'),
                                Select::make('income_range')
                                    ->label(__('family.income_range'))
                                    ->options(collect(__('family.income_ranges'))->mapWithKeys(fn($label, $key) => [$key => $label])->toArray())
                                    ->placeholder('الكل'),
                                Select::make('is_displaced')
                                    ->label('حالة النزوح')
                                    ->options(['1' => 'نازح', '0' => 'مقيم (غير نازح)'])
                                    ->placeholder('الكل'),
                            ])->columns(3)->collapsible(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['social_status'], fn($q, $status) => $q->where('social_status', $status))
                            ->when($data['income_range'], fn($q, $range) => $q->where('income_range', $range))
                            ->when($data['is_displaced'] !== null && $data['is_displaced'] !== '', fn($q, $val) => $q->where('is_displaced', $data['is_displaced'] === '1'));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['social_status']) {
                            $label = collect(__('family.social_statuses'))->get($data['social_status']);
                            $indicators[] = 'الحالة: ' . ($label ?? $data['social_status']);
                        }
                        if ($data['income_range']) $indicators[] = 'الدخل: ' . $data['income_range'];
                        if (($data['is_displaced'] ?? '') !== '') {
                            $indicators[] = ($data['is_displaced'] === '1' ? 'نازح' : 'مقيم');
                        }
                        return $indicators;
                    })
                    ->columnSpanFull(),

                // 4. مجموعة الإحصائيات والأعمار
                Filter::make('family_stats_group')
                    ->form([
                        Section::make('أعداد الأفراد والأعمار')
                            ->schema([
                                Fieldset::make('أفراد العائلة')
                                    ->schema([
                                        TextInput::make('min_persons')->numeric()->label('الحد الأدنى')->placeholder('Min'),
                                        TextInput::make('max_persons')->numeric()->label('الحد الأقصى')->placeholder('Max'),
                                    ])->columns(2)->columnSpan(1),

                                Fieldset::make('أعمار الأطفال')
                                    ->schema([
                                        TextInput::make('min_age')->numeric()->label('من عمر')->placeholder('Min age'),
                                        TextInput::make('max_age')->numeric()->placeholder('Max age')->label('إلى عمر'),
                                    ])->columns(2)->columnSpan(1),

                                Fieldset::make('تاريخ تسجيل العائلة')
                                    ->schema([
                                        DatePicker::make('created_from')->label('من تاريخ')->placeholder('Created from'),
                                        DatePicker::make('created_until')->label('إلى تاريخ')->placeholder('Created until'),
                                    ])->columns(2)->columnSpan(1),
                            ])->columns(3)
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['min_persons'], fn($q, $val) => $q->has('persons', '>=', $val))
                            ->when($data['max_persons'], fn($q, $val) => $q->has('persons', '<=', $val))
                            ->when($data['created_from'], fn($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn($q, $date) => $q->whereDate('created_at', '<=', $date))
                            ->when($data['min_age'] || $data['max_age'], function ($q) use ($data) {
                                return $q->whereHas('persons', function ($sq) use ($data) {
                                    $sq->when($data['min_age'], fn($ssq, $age) => $ssq->whereYear('dob', '<=', now()->year - $age))
                                        ->when($data['max_age'], fn($ssq, $age) => $ssq->whereDate('dob', '>', now()->subYears($age + 1)));
                                });
                            });
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        // Indicator لأعداد الأفراد
                        if ($data['min_persons'] || $data['max_persons']) {
                            $indicators[] = 'أفراد العائلة: ' . ($data['min_persons'] ?? '0') . ' - ' . ($data['max_persons'] ?? '∞');
                        }
                        // Indicator للأعمار
                        if ($data['min_age'] || $data['max_age']) {
                            $indicators[] = 'أعمار الأطفال: ' . ($data['min_age'] ?? '0') . ' - ' . ($data['max_age'] ?? '∞');
                        }
                        // Indicator لتاريخ التسجيل
                        if ($data['created_from'] || $data['created_until']) {
                            $indicators[] = 'التسجيل: ' . ($data['created_from'] ?? 'البداية') . ' - ' . ($data['created_until'] ?? 'اليوم');
                        }
                        return $indicators;
                    })
                    ->columnSpanFull(),
            ])
            ->filtersFormColumns(1)
            ->filtersFormWidth('5xl')

            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->recordAction(ViewAction::class)
            // Disable the default record URL to prevent redirection to the edit page,
            // allowing the recordAction (View Modal) to trigger instead.
            ->recordUrl(null)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);

    }
}

