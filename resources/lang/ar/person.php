<?php

return [
    // العناوين
    'persons_desc'       => 'قائمة بجميع أفراد الأسرة، يمكنك إضافة تفاصيل صحية أو تعليمية لكل فرد.',
    'full_name'          => 'الاسم الرباعي',
    'national_id'        => 'رقم الهوية',
    'dob'                => 'تاريخ الميلاد',
    'gender'             => 'الجنس',
    'relation'           => 'صلة القرابة',
    'is_working'         => 'هل يعمل؟',
    'head_name'          => "اسم رب الأسرة",
    // أزرار التحكم في الظهور
    'has_health_condition' => 'إضافة بيانات صحية (أمراض/إعاقات)؟',
    'has_education_record' => 'إضافة بيانات تعليمية؟',

    // القيم
    'male'   => 'ذكر',
    'female' => 'أنثى',

    'relations' => [
        'head'     => 'رب الأسرة',
        'wife_or_husband'     => 'زوج/ـة',
        'child'      => 'ابن/ـة',
        // 'daughter' => 'ابنة',
        // 'father'   => 'أب',
        // 'mother'   => 'أم',
        // 'brother'  => 'أخ',
        // 'sister'   => 'أخت',
    ],

    // الأقسام الفرعية
    'health_status'      => 'الوضع الصحي',
    'education_info'     => 'التعليم والمبادرات',

    // الأمراض والإعاقات
    'chronic_diseases'   => 'الأمراض المزمنة',
    'disease_name'       => 'اسم المرض',
    'add_disease'        => 'إضافة مرض',

    'disabilities'       => 'الإعاقات',
    'disability_type'    => 'نوع الإعاقة',
    'severity'           => 'درجة الإعاقة',
    'add_disability'     => 'إضافة إعاقة',

    'disability_types' => [
        'movement' => 'حركية',
        'visual'   => 'بصرية',
        'hearing'  => 'سمعية',
        'mental'   => 'ذهنية',
        'speech'   => 'نطقية',
    ],

    'severity_levels' => [
        'mild'     => 'طفيفة',
        'moderate' => 'متوسطة',
        'severe'   => 'شديدة',
    ],

    // التعليم
    'education_records'  => 'المؤهلات العلمية',
    'education_level'    => 'المرحلة الدراسية',
    'initiative_name'    => 'اسم المبادرة (إن وجد)',
    'add_education'      => 'إضافة مؤهل',
    'add_person'         => 'إضافة فرد جديد',

    'education_levels' => [
        'illiterate'  => 'أمي',
        'primary'     => 'ابتدائي',
        'preparatory' => 'إعدادي',
        'secondary'   => 'ثانوي',
        'diploma'     => 'دبلوم',
        'bachelor'    => 'بكالوريوس',
        'master'      => 'ماجستير',
        'phd'         => 'دكتوراه',
    ],

    // رسائل التحقق
    'validation' => [
        'name_min_words' => 'الاسم يجب أن يكون رباعياً على الأقل (4 كلمات).',
        'name_max_words' => 'الاسم طويل جداً، الحد الأقصى هو 8 كلمات.',
        'id_required' => 'يرجى إدخال رقم الهوية.',
        'id_numeric'  => 'رقم الهوية يجب أن يتكون من أرقام فقط.',
        'id_size'     => 'رقم الهوية يجب أن يكون 9 خانات بالضبط.',
        'id_unique'   => 'رقم الهوية هذا مسجل مسبقاً لشخص آخر.',

    ],

    'chronic_diseases_list' => [
        'diabetes' => 'السكري',
        'hypertension' => 'ضغط الدم',
        'heart_disease' => 'أمراض القلب',
        'asthma' => 'الربو',
        'cancer' => 'السرطان',
        'kidney_disease' => 'أمراض الكلى',
        'other' => 'أخرى',
    ],
];

