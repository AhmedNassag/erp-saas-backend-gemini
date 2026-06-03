<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8"><title>إدارة الشركات</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-dark text-white">
        <div class="container py-5">
            <h2 class="mb-4">👥 إدارة الشركات (Tenants)</h2>
            <a href="{{ route('landlord.admin.dashboard') }}" class="btn btn-secondary mb-3">← العودة للوحة التحكم</a>
            <table class="table table-dark table-striped">
                <thead><tr><th>#</th><th>الاسم</th><th>الدومين</th><th>قاعدة البيانات</th><th>الحالة</th></tr></thead>
                <tbody>
                    @foreach($tenants as $t)
                        <tr>
                            <td>{{ $t->id }}</td>
                            <td>{{ $t->name }}</td>
                            <td>{{ $t->domain }}</td>
                            <td>{{ $t->database }}</td>
                            <td>{{ $t->status ?? 'active' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </body>
</html>
