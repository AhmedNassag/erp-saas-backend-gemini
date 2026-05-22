<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8"><title>تم الاشتراك بنجاح</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light py-5 text-center">
        <div class="container my-5">
            <h1 class="text-success fw-bold">🎉 مبروك! تم سداد الاشتراك وتهيئة نظامك بنجاح!</h1>
            <p class="lead text-muted my-3">تم إرسال تفاصيل الدخول إلى بريدك الإلكتروني: <strong>{{ session('email') }}</strong></p>
            <div class="alert alert-info d-inline-block p-4 mt-3">
                <h5>رابط الدخول المباشر للـ ERP الخاص بك هو:</h5>
                <a href="{{ session('login_url') }}" target="_blank" class="fw-bold fs-4 text-decoration-none">{{ session('login_url') }}</a>
            </div>
        </div>
    </body>
</html>