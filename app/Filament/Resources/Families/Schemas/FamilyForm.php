<?php

namespace App\Filament\Resources\Families\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class FamilyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // --- 1. القسم الأول: البيانات الأساسية للأسرة ---
                Section::make(__('family.basic_info_section'))
                    ->description(__('family.basic_info_desc'))
                    ->icon('heroicon-o-home')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('family_code')
                                ->label(__('family.family_code'))
                                ->default(__('family.auto_generated'))
                                ->disabled()
                                ->dehydrated(false),

                            Select::make('governorate')
                                ->label(__('family.governorate'))
                                ->options(__('family.governorates'))
                                ->required()
                                ->columnSpan(2),
                        ]),

                        Grid::make(2)->schema([
                            Select::make('social_status')
                                ->label(__('family.social_status'))
                                ->options(__('family.social_statuses'))
                                ->required()
                                ->native(false),

                            TextInput::make('phone')
                                ->label(__('family.phone'))
                                ->tel()
                                ->required()
                                ->regex('/^[0-9]+$/')
                                ->length(10)
                                ->placeholder('05xxxxxxxx')
                                ->prefixIcon('heroicon-m-phone')
                                ->validationMessages([
                                    'required' => __('family.validation.phone_required'),
                                    'regex'    => __('family.validation.phone_regex'),
                                    'size'   => __('family.validation.phone_length'),
                                ]),
                        ]),

                        Textarea::make('full_address')
                            ->label(__('family.full_address'))
                            ->required()
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                // --- 2. القسم الثاني: بيانات النزوح ---
                Section::make(__('family.displacement_section'))
                    ->description(__('family.displacement_section_desc'))
                    ->schema([
                        Toggle::make('is_displaced')
                            ->label(__('family.is_displaced'))
                            ->required()
                            ->onColor('danger')
                            ->live(),

                        Grid::make(2)
                            ->visible(fn (Get $get) => $get('is_displaced'))
                            ->schema([
                                Select::make('displacement_type')
                                    ->label(__('family.displacement_type'))
                                    ->options(__('family.displacement_types')),

                                Select::make('displacement_center_id')
                                    ->label(__('family.displacement_center'))
                                    ->relationship('displacementCenter', 'name')
                                    ->searchable()
                                    ->preload(),

                                Textarea::make('displacement_address')
                                    ->label(__('family.displacement_address'))
                                    ->columnSpanFull(),
                            ]),
                    ]),

                // --- 3. القسم الثالث: الوضع الاقتصادي والبيانات الإدارية ---
                Section::make(__('family.economic_section'))
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('income_range')
                                ->label(__('family.income_range'))
                                ->options(__('family.income_ranges'))
                                ->required(),

                            Select::make('income_source_id')
                                ->label(__('family.income_source'))
                                ->relationship('incomeSource', 'name')
                                ->required(),
                        ]),

                        // بيانات التسجيل
                        Section::make(__('family.addition_details'))
                            ->schema([
                                Grid::make(2)->schema([
                                    Select::make('addition_source_id')
                                        ->label(__('family.data_source'))
                                        ->relationship('additionSource', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->createOptionForm([
                                            TextInput::make('name')
                                                ->label(__('family.source_name'))
                                                ->required(),
                                        ])
                                        ->createOptionAction(
                                            fn ($action) => $action
                                                ->visible(fn () => auth()->user()?->hasRole('super_admin'))
                                        )
                                        ->required(),

                                    Select::make('addition_reason_id')
                                        ->label(__('family.addition_reason'))
                                        ->relationship('additionReason', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->createOptionForm([
                                            TextInput::make('name')
                                                ->label(__('family.reason_name'))
                                                ->required(),
                                        ])
                                        ->createOptionAction(
                                            fn ($action) => $action
                                                ->visible(fn () => auth()->user()?->hasRole('super_admin'))
                                        )
                                        ->required(),
                                ]),
                                Textarea::make('addition_notes')
                                    ->label(__('family.notes'))
                                    ->columnSpanFull(),
                            ])->compact(),
                    ])->collapsible(),

                // --- 4. القسم الرابع: أفراد العائلة ---
                Section::make(__('family.persons'))
                    ->description(__('person.persons_desc'))
                    ->icon('heroicon-o-users')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('persons')
                            ->hiddenLabel()
                            ->relationship()
                            ->collapseAllAction(fn ($action) => $action->label(__('family.collapse_all')))
                            ->expandAllAction(fn ($action) => $action->label(__('family.expand_all')))
                            ->schema([
                                // الصف 1: البيانات الشخصية
                                Grid::make(3)->schema([
                                    TextInput::make('full_name')
                                        ->label(__('person.full_name'))
                                        ->required()
                                        ->rule(function () {
                                            return function (string $attribute, $value, \Closure $fail) {
                                                $words = preg_split('/\s+/', trim($value), -1, PREG_SPLIT_NO_EMPTY);
                                                $count = count($words);

                                                if ($count < 4) {
                                                    $fail(__('person.validation.name_min_words'));
                                                }

                                                if ($count > 8) {
                                                    $fail(__('person.validation.name_max_words'));
                                                }
                                            };
                                        }),

                                    TextInput::make('national_id')
                                        ->label(__('person.national_id'))
                                        ->required()
                                        ->numeric() // وجود هذه القاعدة يحول التفتيش إلى "أرقام"
                                        ->length(9)
                                        ->unique(ignoreRecord: true)
                                        ->validationMessages([
                                            'required' => __('person.validation.id_required'),
                                            'numeric'  => __('person.validation.id_numeric'),
                                            'digits'   => __('person.validation.id_size'),
                                            'unique'   => __('person.validation.id_unique'),
                                        ]),

                                    Select::make('relation')
                                        ->label(__('person.relation'))
                                        ->options(__('person.relations'))
                                        ->required(),
                                ]),

                                // الصف 2: البيانات الديموغرافية
                                Grid::make(3)->schema([
                                    DatePicker::make('dob')
                                        ->label(__('person.dob'))
                                        ->required()
                                        ->native(false),

                                    Select::make('gender')
                                        ->label(__('person.gender'))
                                        ->options([
                                            'male' => __('person.male'),
                                            'female' => __('person.female'),
                                        ])
                                        ->required(),

                                    Toggle::make('is_working')
                                        ->label(__('person.is_working'))
                                        ->inline(false),
                                ]),

                                // الصف 3: أزرار التحكم
                                Grid::make(2)->schema([
                                    Toggle::make('has_health_condition')
                                        ->label(__('person.has_health_condition'))
                                        ->live()
                                        ->dehydrated(false)
                                        ->onColor('warning')
                                        ->afterStateHydrated(fn (Toggle $component, Get $get) =>
                                        $component->state(filled($get('chronicDiseases')) || filled($get('disabilities')))
                                        ),

                                    Toggle::make('has_education_record')
                                        ->label(__('person.has_education_record'))
                                        ->live()
                                        ->dehydrated(false)
                                        ->onColor('info')
                                        ->afterStateHydrated(fn (Toggle $component, Get $get) =>
                                        $component->state(filled($get('education')))
                                        ),
                                ]),

                                // الصف 4: الأقسام المخفية
                                Grid::make(2)->schema([
                                    // قسم الصحة
                                    Section::make(__('person.health_status'))
                                        ->icon('heroicon-o-heart')
                                        ->visible(fn (Get $get) =>
                                            $get('has_health_condition') ||
                                            filled($get('chronicDiseases')) ||
                                            filled($get('disabilities'))
                                        )
                                        ->schema([
                                            Repeater::make('chronicDiseases')
                                                ->label(__('person.chronic_diseases'))
                                                ->relationship('chronicDiseases')
                                                ->schema([
                                                    Select::make('disease_name')
                                                        ->label(__('person.disease_name'))
                                                        ->options(__('person.chronic_diseases_list'))
                                                        ->required()
                                                        ->searchable()
                                                        ->distinct()
                                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                                ])
                                                ->addActionLabel(__('person.add_disease')),
                                            Repeater::make('disabilities')
                                                ->label(__('person.disabilities'))
                                                ->relationship('disabilities')
                                                ->schema([
                                                    Select::make('disability_type')
                                                        ->label(__('person.disability_type'))
                                                        ->options(__('person.disability_types'))
                                                        ->required(),

                                                    Select::make('severity')
                                                        ->label(__('person.severity'))
                                                        ->options(__('person.severity_levels')),
                                                ])
                                                ->columns(2)
                                                ->addActionLabel(__('person.add_disability')),
                                        ]),

                                    // قسم التعليم
                                    Section::make(__('person.education_info'))
                                        ->icon('heroicon-o-academic-cap')
                                        ->visible(fn (Get $get) =>
                                            $get('has_education_record') ||
                                            filled($get('education'))
                                        )
                                        ->schema([
                                            Repeater::make('education')
                                                ->hiddenLabel()
                                                ->relationship('education')
                                                ->schema([
                                                    Select::make('education_level')
                                                        ->label(__('person.education_level'))
                                                        ->options(__('person.education_levels'))
                                                        ->required(),

                                                    TextInput::make('initiative_name')
                                                        ->label(__('person.initiative_name')),
                                                ])->addActionLabel(__('person.add_education')),
                                        ]),
                                ]),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['full_name'] ?? null)
                            ->collapsed()
                            ->cloneable()
                            ->columnSpanFull()
                    ]),
            ]);
    }
}
