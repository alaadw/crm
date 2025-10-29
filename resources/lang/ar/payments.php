<?php

return [
    // المدفوعات
    'payments' => 'المدفوعات',
    'payment' => 'دفعة',
    'payment_history' => 'تاريخ المدفوعات',
    'payment_date' => 'تاريخ الدفع',
    'payment_method' => 'طريقة الدفع',
    'currency_code' => 'العملة',
    'amount' => 'المبلغ',
    'amount_jod' => 'المبلغ (دينار)',
    'status' => 'الحالة',
    'notes' => 'ملاحظات',
    'add_payment' => 'إضافة دفعة',
    'maximum_amount' => 'الحد الأقصى للمبلغ',
    'completed' => 'مكتمل',
    'pending' => 'معلق',
    'cancelled' => 'ملغي',
    
    // طرق الدفع
    'cash' => 'نقدي',
    'bank_transfer' => 'تحويل بنكي',
    'credit_card' => 'بطاقة ائتمان',
    'check' => 'شيك',
    'zaincash' => 'زين كاش',
    'other' => 'أخرى',
    
    // الرسائل
    'payment_added_successfully' => 'تم إضافة الدفعة بنجاح',
    'enrollment_not_found_for_student' => 'التسجيل المحدد لا ينتمي لهذا الطالب',
    'enrollment_not_active' => 'هذا التسجيل غير نشط',
    'amount_exceeds_due_amount' => 'مبلغ الدفعة لا يمكن أن يتجاوز المبلغ المستحق :due',
    'amount_exceeds_due_amount_js' => 'مبلغ الدفعة لا يمكن أن يتجاوز المبلغ المستحق',
    
    // رسائل التحقق
    'enrollment_required' => 'يرجى اختيار التسجيل.',
    'enrollment_invalid' => 'يرجى اختيار تسجيل صالح.',
    'amount_required' => 'مبلغ الدفعة مطلوب.',
    'amount_numeric' => 'مبلغ الدفعة يجب أن يكون رقم.',
    'amount_min' => 'مبلغ الدفعة يجب أن يكون على الأقل 0.01.',
    'payment_method_required' => 'يرجى اختيار طريقة الدفع.',
    'payment_method_invalid' => 'يرجى اختيار طريقة دفع صالحة.',
    'payment_date_required' => 'تاريخ الدفع مطلوب.',
    'payment_date_invalid' => 'يرجى إدخال تاريخ دفع صالح.',
    'notes_max' => 'يجب أن لا تزيد الملاحظات عن 1000 حرف.',
    
    // أسماء الحقول
    'enrollment' => 'التسجيل',

    // لوحة المدفوعات والرسوم البيانية
    'statistics' => 'إحصائيات المدفوعات',
    'chart_type' => 'نوع الرسم البياني',
    'by_time' => 'حسب الوقت',
    'by_method' => 'حسب الطريقة',
    'period' => 'الفترة',
    'day' => 'يومي',
    'week' => 'أسبوعي',
    'month' => 'شهري',
    'sales_rep' => 'مندوب المبيعات',
    'received_by' => 'مستلم الدفعة',
    'class' => 'الشعبة',
    'total_amount' => 'إجمالي التحصيل',
    'total_by_period' => 'الإجمالي حسب الفترة',
    'total_by_method' => 'الإجمالي حسب الطريقة',
    'week_label' => 'الأسبوع :week',
    'print_now' => 'طباعة الآن',
    'export_excel_filename' => 'تصدير_المدفوعات_:date.xlsx',
    'export_pdf_filename' => 'تصدير_المدفوعات_:date.pdf',
];