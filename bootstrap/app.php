<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'tenant' => \Spatie\Multitenancy\Http\Middleware\NeedsTenant::class,
        ]);

        // 💡 الطريقة الرسمية والمستقرة: بنقول للارافيل لما يجي يطرد يوزر مش مسجل دخول (Guest)
        // شيك على الـ Request لو جاي للـ API أو طالب JSON، ارمي 401 فوراً وامنع الـ Redirect للويب
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                abort(response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated. Please provide a valid token.'
                ], 401));
            }
            // الافتراضي لصفحات الويب العادية لو احتجتها مستقبلاً
            return '/login'; 
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // للأمان الإضافي، بنجبر أي إيرور تاني يحصل جوة الـ api إنه يرجع JSON دايماً
        $exceptions->shouldRenderJsonWhen(function ($request, $e) {
            if ($request->is('api/*')) {
                return true;
            }
            return $request->expectsJson();
        });
    })->create();
