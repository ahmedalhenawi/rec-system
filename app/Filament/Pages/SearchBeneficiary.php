<?php

namespace App\Filament\Pages;

use App\Models\Delivery;
use App\Models\Package;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Gate;

class SearchBeneficiary extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?string $navigationLabel = 'البحث عن مستفيد';
    protected static ?string $title = 'البحث عن مستفيد';
    protected string $view = 'filament.pages.search-beneficiary';

    public ?string $search_id = '';

    // تحسين: إعادة ضبط الجدول عند تغير الرقم
    public function updatedSearchId()
    {
        $this->resetTable();
    }

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make()
                    ->schema([
                        TextInput::make('search_id')
                            ->label('رقم الهوية (9 أرقام)')
                            ->placeholder('أدخل أو الصق رقم الهوية...')
                            // تم استخدام live() مع debounce لضمان التقاط الرقم عند اللصق
                            ->live(onBlur: false)
                            ->debounce(300)
                            ->numeric()
                            // حذفنا length(9) من هنا لأنها أحياناً تعطل الـ Validation أثناء اللصق
                            // وسنعتمد على maxlength والمنطق في الـ query
                            ->extraInputAttributes(['maxlength' => 9])
                            ->autofocus()
                            ->columnSpanFull()
                            ->suffixAction(
                                \Filament\Actions\Action::make('clear')
                                    ->icon('heroicon-m-x-mark')
                                    ->color('danger')
                                    ->action(function() {
                                        $this->search_id = '';
                                        $this->resetTable();
                                    })
                            ),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Delivery::query()
                    ->when(
                    // المنطق: البحث فقط إذا كان الرقم 9 أرقام
                        strlen($this->search_id) === 9,
                        fn ($q) => $q->where('national_id', $this->search_id),
                        fn ($q) => $q->whereRaw('1 = 0')
                    )
            )
            ->columns([
                TextColumn::make('package.name')
                    ->label('اسم الطرد')
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('package.type')
                    ->label('النوع')
                    ->badge(),

                TextColumn::make('beneficiary_name')
                    ->label('الاسم'),

                TextColumn::make('receipt_code')
                    ->label('كود الاستلام')
                    ->badge()
                    ->color('success'),

                TextColumn::make('received_at')
                    ->label('تاريخ الاستلام')
                    ->dateTime('Y-m-d H:i')
                    ->description(fn (Delivery $record): string => $record->received_at->diffForHumans()),
            ])
            ->emptyStateHeading(fn() =>
            strlen($this->search_id) < 9 ? 'يرجى إكمال رقم الهوية (9 أرقام)' : 'لم يستلم هذا المواطن أي طرود'
            )
            ->emptyStateIcon(fn() =>
            strlen($this->search_id) < 9 ? 'heroicon-o-pencil-square' : 'heroicon-o-x-circle'
            );
    }

    public static function canAccess(): bool
    {
        // هنا نسمح بالدخول فقط إذا كان المستخدم يملك صلاحية viewAny الخاصة بمودل Delivery
        return Gate::allows('viewAny', Package::class);
    }
}
