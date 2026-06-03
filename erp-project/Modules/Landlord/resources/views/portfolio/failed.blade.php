@extends('landlord::components.layouts.master')

@section('title', 'Payment Failed — NexaERP')

@section('content')

<section class="relative min-h-screen flex items-center justify-center gradient-bg overflow-hidden pt-20">
    <div class="absolute inset-0 hero-glow pointer-events-none"></div>

    <div class="relative max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center py-16">

        <div class="mb-8">
            <div class="w-28 h-28 rounded-full bg-red-500/10 border-2 border-red-500/30 flex items-center justify-center mx-auto">
                <div class="w-20 h-20 rounded-full bg-red-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-xmark text-red-400 text-4xl"></i>
                </div>
            </div>
        </div>

        <div class="inline-flex items-center gap-2 glass rounded-full px-4 py-2 mb-4 border border-red-500/20">
            <i class="fa-solid fa-triangle-exclamation text-red-400 text-xs"></i>
            <span class="text-slate-300 text-xs font-medium">Payment Failed</span>
        </div>

        <h1 class="text-4xl sm:text-5xl font-black text-white mb-4">Something went wrong!</h1>

        <p class="text-slate-400 text-lg max-w-md mx-auto mb-2">
            {{ $message ?? 'Your payment could not be processed.' }}
        </p>
        <p class="text-slate-500 text-sm mb-8">
            Please try again or contact support if the issue persists.
        </p>

        <div class="flex items-center justify-center gap-4">
            <a href="{{ url()->previous() ?? route('landlord.pricing') }}"
               class="btn-primary-gradient text-white font-bold px-8 py-3.5 rounded-xl inline-flex items-center gap-2 shadow-xl">
                <i class="fa-solid fa-arrow-left"></i>
                Try Again
            </a>
            <a href="{{ route('landlord.contact') }}"
               class="glass border border-slate-600 hover:border-electric-blue/50 text-white font-semibold px-8 py-3.5 rounded-xl inline-flex items-center gap-2 transition-all">
                <i class="fa-solid fa-headset"></i>
                Contact Support
            </a>
        </div>
    </div>
</section>

@endsection
