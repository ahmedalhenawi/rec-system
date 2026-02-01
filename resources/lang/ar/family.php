<?php

return [
    // العناوين والأقسام
    'basic_info_section'     => 'بيانات العائلة الأساسية',
    'basic_info_desc'        => 'المعلومات التعريفية ومكان السكن الأصلي',
    'displacement_section'   => 'حالة النزوح',
    'displacement_section_desc' => 'تفعيل هذا الخيار لإدخال بيانات مكان النزوح الحالي',
    'economic_section'       => 'الوضع الاقتصادي والبيانات الإدارية',
    'addition_details'       => 'تفاصيل التسجيل',

    // الحقول الأساسية
    'family_code'            => 'كود الأسرة',
    'auto_generated'         => 'تلقائي', // تمت الإضافة
    'social_status'          => 'الحالة الاجتماعية',
    'full_address'           => 'العنوان الكامل',
    'phone'                  => 'رقم التواصل',
    'governorate'            => 'المحافظة',

    // النزوح
    'is_displaced'           => 'هل الأسرة نازحة؟',
    'displacement_type'      => 'نوع النزوح',
    'displacement_center'    => 'مركز الإيواء',
    'displacement_address'   => ' (عنوان الايواء)عنوان النزوح',
    'inside'                 => 'داخل المحافظة',
    'outside'                => 'خارج المحافظة',

    // الاقتصادي والتسجيل
    'income_range'           => 'فئة الدخل',
    'income_source'          => 'مصدر الدخل',
    'data_source'            => 'مصدر البيانات',
    'source_name'            => 'اسم المصدر',
    'addition_reason'        => 'سبب الإضافة',
    'reason_name'            => 'اسم السبب',
    'notes'                  => 'ملاحظات التسجيل',

    // القوائم والمصفوفات
    'governorates' => [
        'south'  => 'محافظة الجنوب',
        'middle' => 'محافظة الوسطى',
        'north'  => 'محافظة الشمال',
    ],

    'all' => 'الكل',

    'social_statuses' => [
        'married'  => 'متزوج/ـة',
        'widow'    => 'أرمل/ـة',
        'divorced' => 'مطلق/ـة',
        'single'   =>  'غير متزوج/ـة',
    ],

    'income_ranges' => [
        'lte_500'      => '500 شيكل وأقل',
        '501_1000'     => 'من 501 إلى 1000 شيكل',
        '1001_1500'    => 'من 1001 إلى 1500 شيكل',
        '1501_2000'    => 'من 1501 إلى 2000 شيكل',
        'gte_2000'     => '2000 شيكل فأكثر',
    ],

    'displacement_types' => [
        'inside'  => 'نزوح داخل المحافظة',
        'outside' => 'نزوح خارج المحافظة',
    ],


    'males_count' => 'عدد الذكور',

    'females_count' => 'عدد الاناث',

    'total_members' => 'عدد الأفراد',



    // عام
    'persons'       => 'أفراد العائلة',
    'collapse_all'  => 'طي الكل',
    'expand_all'    => 'توسيع الكل',
    'created_at'    => 'تاريخ الإضافة',

    'validation' => [
        'phone_required' => 'يرجى إدخال رقم التواصل.',
        'phone_regex'    => 'رقم الهاتف يجب أن يحتوي على أرقام فقط.',
        'phone_length'   => 'رقم التواصل يجب أن يكون 10 خانات بالضبط.',
        ],
];
