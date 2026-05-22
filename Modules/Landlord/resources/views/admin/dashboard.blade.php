<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8"><title>لوحة التحكم المركزية لـ الـ SaaS</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-dark text-white">
        <div class="container py-5">
            <h2 class="mb-4">🏰 لوحة التحكم المركزية للمنصة (Super Admin Central Panel)</h2>
            <div class="row g-4 text-center text-dark">
                <div class="col-md-4">
                    <div class="card bg-warning p-4">
                        <h3>👥 الشركات المشتركة</h3>
                        <p class="display-5 fw-bold">{{ $tenantsCount }} شركات</p>
                        <a href="{{ route('landlord.admin.tenants') }}" class="btn btn-sm btn-outline-dark">إدارة الشركات</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white p-4">
                        <h3>📦 باقات الخدمة</h3>
                        <p class="display-5 fw-bold">{{ $packagesCount }} باقات</p>
                        <a href="{{ route('landlord.admin.packages') }}" class="btn btn-sm btn-outline-light">تعديل الأسعار</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info p-4">
                        <h3>💳 إجمالي المدفوعات</h3>
                        <p class="display-5 fw-bold">مؤمنة بالـ Webhook</p>
                        <a href="#" class="btn btn-sm btn-outline-dark">سجل الفواتير</a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>