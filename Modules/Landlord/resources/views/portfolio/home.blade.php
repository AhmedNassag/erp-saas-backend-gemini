<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8"><title>بورتفوليو الشركة المركزية</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="#">🚀 Cloud ERP SaaS</a>
                <div class="navbar-nav me-auto">
                    <a class="nav-link active" href="{{ route('landlord.home') }}">الرئيسية</a>
                    <a class="nav-link" href="{{ route('landlord.about') }}">عن الشركة</a>
                    <a class="nav-link" href="{{ route('landlord.pricing') }}">الباقات والأسعار</a>
                    <a class="nav-link" href="{{ route('landlord.contact') }}">تواصل معنا</a>
                </div>
            </div>
        </nav>
        <div class="container text-center py-5 my-5">
            <h1 class="display-3 fw-bold text-primary">أهلاً بك في منصة المستقبل للـ ERP</h1>
            <p class="lead text-muted">نحن نقدم حلول سحابية متكاملة لإدارة شركتك ومخازنك بذكاء وسرعة.</p>
            <a href="{{ route('landlord.pricing') }}" class="btn btn-primary btn-lg mt-3">تصفح خطط الأسعار وابدأ الآن 💳</a>
        </div>
    </body>
</html>