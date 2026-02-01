<?php
namespace App\Filament\Imports;

use App\Models\Delivery;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class DeliveryImporter extends Importer
{
    protected static ?string $model = Delivery::class;

    public static function getColumns(): array
    {
        return [
            // ربط عمود الهوية مع التحقق من الصحة
            ImportColumn::make('national_id')
                ->requiredMapping()
                ->label('رقم الهوية')
                ->rules(['required', 'numeric']),

            // ربط عمود الاسم
            ImportColumn::make('beneficiary_name')
                ->requiredMapping()
                ->label('اسم المستفيد'),

            // ربط كود الاستلام (الـ 3 أرقام)
            ImportColumn::make('receipt_code')
                ->requiredMapping()
                ->label('كود الاستلام')
                ->rules(['required', 'max:3']),
        ];
    }

    public function resolveRecord(): ?Delivery
    {
        // هنا المنطق القوي:
        // 1. نبحث إذا كان الشخص استلم هذا الطرد مسبقاً لمنع التكرار
        // 2. نربط الـ package_id القادم من الصفحة تلقائياً

        return new Delivery([
            'package_id' => $this->options['package_id'], // سيتم تمريره من الأكشن
            'received_at' => now(), // توثيق وقت الاستلام اللحظي
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'تمت عملية استيراد المستفيدين بنجاح وفحص ' . number_format($import->successful_rows) . ' سجل.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' هناك ' . number_format($failedRowsCount) . ' سجل فشل استيرادهم (غالباً بسبب التكرار).';
        }

        return $body;
    }

    public static function getJobChunkSize(): int
    {
        return 100;
    }
}
