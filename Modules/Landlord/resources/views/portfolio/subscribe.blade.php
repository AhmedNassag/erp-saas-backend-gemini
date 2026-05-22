<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8"><title>استمارة الدفع والاشتراك</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light py-5">
        <div class="container">
            <div class="card col-md-6 mx-auto p-4 shadow">
                <h4 class="text-center text-success mb-4">💳 إدخال البيانات ومحاكاة عملية السداد لفئة: {{ $package->name }}</h4>
                <form action="{{ route('landlord.subscribe.checkout', $package->id) }}" method="POST">
                    @csrf
                    <div class="mb-3"><label>اسم شركتك التجارية</label><input type="text" name="company_name" class="form-control" required></div>
                    <div class="mb-3">
                        <label>الـ Subdomain المطلوب</label>
                        <div class="input-group" dir="ltr"><span class="input-group-text">.erp.test</span><input type="text" name="subdomain" class="form-control" required></div>
                    </div>
                    <div class="mb-3"><label>اسم مدير النظام للشركة</label><input type="text" name="admin_name" class="form-control" required></div>
                    <div class="mb-3"><label>البريد الإلكتروني للمدير</label><input type="email" name="admin_email" class="form-control" required></div>
                    <div class="mb-3"><label>باسورد المدير للـ ERP الجديد</label><input type="password" name="admin_password" class="form-control" required></div>
                    <button type="submit" class="btn btn-primary btn-lg w-100">تأكيد الدفع وإنشاء النظام فوراً 🚀</button>
                </form>
            </div>
        </div>
    </body>
</html>