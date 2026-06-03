<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8"><title>إدارة الباقات</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-dark text-white">
        <div class="container py-5">
            <h2 class="mb-4">📦 إدارة الباقات (Packages)</h2>
            <a href="{{ route('landlord.admin.dashboard') }}" class="btn btn-secondary mb-3">← العودة للوحة التحكم</a>
            <table class="table table-dark table-striped">
                <thead><tr><th>#</th><th>الاسم</th><th>السعر</th><th>المستخدمين</th><th>الحالة</th></tr></thead>
                <tbody>
                    @foreach($packages as $p)
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td>{{ $p->name }}</td>
                            <td>{{ $p->price }}</td>
                            <td>{{ $p->limit_users }}</td>
                            <td>{{ $p->is_active ? 'نشط' : 'غير نشط' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </body>
</html>
