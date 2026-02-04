<?php

namespace App\Filament\Imports;

use App\Models\Family;
use App\Models\Person;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class FamilyImporter extends Importer
{
    protected static ?string $model = Family::class;

    public static function getColumns(): array
    {
        return [
            // الرقم التسلسلي لربط العائلات (مهم جداً)
            ImportColumn::make('number')
                ->label('رقم العائلة')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),

            // بيانات العائلة
            ImportColumn::make('social_status')->label('الحالة الاجتماعية'),
            ImportColumn::make('governorate')->label('المحافظة'),
            ImportColumn::make('full_address')->label('العنوان الكامل'),

            // النزوح
            ImportColumn::make('is_displaced')->label('هل يوجد نزوح؟')->boolean(),
            ImportColumn::make('displacement_type')->label('نوع النزوح'),
            ImportColumn::make('displacement_address')->label('عنوان النزوح'),
            ImportColumn::make('income_range')->label('نطاق الدخل'),

            // بيانات الفرد
            ImportColumn::make('full_name')->label('الاسم الكامل')->requiredMapping(),
            ImportColumn::make('national_id')->label('رقم الهوية')->requiredMapping(),
            ImportColumn::make('dob')->label('تاريخ الميلاد')->rules(['date']),
            ImportColumn::make('gender')->label('الجنس'),
            ImportColumn::make('relation')->label('صلة القرابة'),

            ImportColumn::make('is_working')->label('يعمل؟')->boolean(),
        ];
    }

    public function resolveRecord(): ?Family
    {
        $familyNumberInExcel = $this->data['number'];
        $importId = $this->import->id;
        $cacheKey = "import_{$importId}_family_{$familyNumberInExcel}";

        // 2. هل قمنا بإنشاء هذه العائلة في سطر سابق (خلال الدقائق الماضية)؟
        $existingFamilyId = Cache::get($cacheKey);

        if ($existingFamilyId) {
            // نعم! العائلة موجودة. نرجع الكائن الموجود بدلاً من إنشاء جديد
            // Filament سيفهم تلقائياً أن هذا تحديث (Update) وليس إنشاء (Insert)
            return Family::withoutGlobalScopes()->find($existingFamilyId);
        }

        // 3. لا، هذه أول مرة يمر علينا هذا الرقم في هذا الملف. ننشئ عائلة جديدة.
        return new Family([
            'user_id'            => $this->import->user_id,
            'social_status'      => $this->data['social_status'] ?? null,
            'phone'              => $this->data['phone'] ?? null, // تأكدنا من إضافتها
            'governorate'        => $this->data['governorate'] ?? null,
            'full_address'       => $this->data['full_address'] ?? null,
            'is_displaced'       => $this->data['is_displaced'] ?? false,
            'displacement_type'  => $this->data['displacement_type'] ?? null,
            'displacement_address'=> $this->data['displacement_address'] ?? null,
            'income_range'       => $this->data['income_range'] ?? null,
            'addition_source_id' => $this->data['addition_source_id'] ?? null,
            'addition_reason_id' => $this->data['addition_reason_id'] ?? null,
            'addition_notes'     => $this->data['addition_notes'] ?? null,
        ]);
    }

    protected function afterSave(): void
    {
        // التأكد من أن العائلة محفوظة
        if (!$this->record->exists) {
            return;
        }

        // تخزين ID العائلة في الكاش
        $cacheKey = "import_{$this->import->id}_family_{$this->data['number']}";
        Cache::put($cacheKey, $this->record->id, now()->addMinutes(10));

        // إنشاء أو تحديث بيانات الشخص
        Person::updateOrCreate(
            ['national_id' => $this->data['national_id']],
            [
                'family_id'  => $this->record->id,
                'full_name'  => $this->data['full_name'],
                'dob'        => $this->data['dob'] ?? null,
                'gender'     => $this->data['gender'] ?? null,
                'relation'   => $this->data['relation'] ?? null,
                'is_working' => $this->data['is_working'] ?? false,
            ]
        );
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'تمت معالجة ملف الاستيراد.';
        $body .= ' تم استيراد ' . number_format($import->successful_rows) . ' سجل بنجاح.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' وفشل استيراد ' . number_format($failedRowsCount) . ' سجل.';
        }

        return $body;
    }

    // أضف هذه الدالة داخل كلاس FamilyImporter
    public function saveRecord(): void
    {
        $record = $this->record;

        // تنظيف الأعمدة الزائدة الخاصة بالأفراد لكي لا يحدث كراش
        $columnsToRemove = ['number', 'full_name', 'national_id', 'dob', 'gender', 'relation', 'is_working'];
        foreach ($columnsToRemove as $column) {
            unset($record->$column);
        }

        // الحفظ
        $record->save();

        // 4. اللحظة الحاسمة: تخزين ID العائلة في الكاش فوراً بعد الحفظ
        // لكي يجده السطر التالي في resolveRecord ويرتبط به
        $familyNumberInExcel = $this->data['number'];
        $cacheKey = "import_{$this->import->id}_family_{$familyNumberInExcel}";

        // نخزن الـ ID لمدة قصيرة (مثلاً 30 دقيقة تكفي لانتهاء الملف)
        Cache::put($cacheKey, $record->id, now()->addMinutes(30));
    }

}






