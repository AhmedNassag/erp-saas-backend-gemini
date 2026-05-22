<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8"><title>الباقات والأسعار</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container py-5">
            <h2 class="text-center mb-5 fw-bold">💰 خطط الأسعار والاشتراكات السنوية</h2>
            <div class="row justify-content-center">
                @foreach($packages as $package)
                <div class="col-md-4">
                    <div class="card shadow-sm text-center p-4">
                        <h3 class="text-primary">{{ $package->name }}</h3>
                        <h2 class="my-3">{{ $package->price }}$ <small class="text-muted">/ سنوياً</small></h2>
                        <p class="text-muted">المستخدمين المتاحين للشركة: {{ $package->limit_users }} موظف</p>
                        <a href="{{ route('landlord.subscribe.form', $package->id) }}" class="btn btn-success btn-lg w-100 mt-3">اشترك الآن 🚀</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </body>
</html>