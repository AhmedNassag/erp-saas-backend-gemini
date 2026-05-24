@extends('landlord::components.layouts.master')

@section('title', 'Welcome to NexaERP — Your ERP is Ready!')

@section('meta')
<meta name="description" content="Your NexaERP account has been created successfully. Login to your dashboard and start managing your business.">
@endsection

@section('styles')
<style>
    @keyframes checkDraw {
        0% { stroke-dashoffset: 100; opacity: 0; }
        50% { opacity: 1; }
        100% { stroke-dashoffset: 0; opacity: 1; }
    }
    @keyframes circlePulse {
        0% { transform: scale(0.8); opacity: 0; }
        60% { transform: scale(1.05); opacity: 1; }
        100% { transform: scale(1); opacity: 1; }
    }
    @keyframes fadeSlideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes shimmer {
        0% { background-position: -200% center; }
        100% { background-position: 200% center; }
    }
    .success-circle {
        animation: circlePulse 0.6s ease-out forwards;
    }
    .check-path {
        stroke-dasharray: 100;
        stroke-dashoffset: 100;
        animation: checkDraw 0.8s ease-out 0.4s forwards;
    }
    .fade-slide-1 { animation: fadeSlideUp 0.6s ease-out 0.8s both; }
    .fade-slide-2 { animation: fadeSlideUp 0.6s ease-out 1.0s both; }
    .fade-slide-3 { animation: fadeSlideUp 0.6s ease-out 1.2s both; }
    .fade-slide-4 { animation: fadeSlideUp 0.6s ease-out 1.4s both; }
    .fade-slide-5 { animation: fadeSlideUp 0.6s ease-out 1.6s both; }
    .shimmer-text {
        background: linear-gradient(90deg, #3B82F6, #06B6D4, #3B82F6);
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: shimmer 3s linear infinite;
    }
    .url-box {
        background: rgba(59,130,246,0.08);
        border: 1px solid rgba(59,130,246,0.3);
    }
    .next-card:hover { transform: translateY(-4px); border-color: rgba(59,130,246,0.4); }
    .next-card { transition: all 0.3s ease; }
    .confetti-dot {
        position: absolute;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        animation: fadeSlideUp 1s ease-out both;
    }
</style>
@endsection

@section('content')

<!-- ===== SUCCESS HERO ===== -->
<section class="relative min-h-screen flex items-center justify-center gradient-bg overflow-hidden pt-20">
    <div class="absolute inset-0 hero-glow pointer-events-none"></div>

    <!-- Decorative dots -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="confetti-dot bg-electric-blue/40 top-1/4 left-1/4" style="animation-delay:0.2s"></div>
        <div class="confetti-dot bg-cyan-brand/40 top-1/3 right-1/3" style="animation-delay:0.4s"></div>
        <div class="confetti-dot bg-green-400/40 bottom-1/3 left-1/3" style="animation-delay:0.6s"></div>
        <div class="confetti-dot bg-purple-400/40 top-1/2 right-1/4" style="animation-delay:0.3s"></div>
        <div class="absolute top-20 left-20 w-64 h-64 bg-electric-blue/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-20 w-48 h-48 bg-green-500/5 rounded-full blur-3xl"></div>
    </div>

    <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center py-16">

        <!-- Animated Checkmark -->
        <div class="flex justify-center mb-8">
            <div class="relative">
                <div class="w-28 h-28 rounded-full bg-green-500/10 border-2 border-green-500/30 flex items-center justify-center success-circle">
                    <div class="w-20 h-20 rounded-full bg-green-500/20 flex items-center justify-center">
                        <svg class="w-10 h-10" viewBox="0 0 50 50" fill="none">
                            <polyline class="check-path" points="10,25 22,37 40,15"
                                      stroke="#22C55E" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                <!-- Pulse rings -->
                <div class="absolute inset-0 rounded-full border-2 border-green-500/20 animate-ping"></div>
            </div>
        </div>

        <!-- Congratulations Text -->
        <div class="fade-slide-1">
            <div class="inline-flex items-center gap-2 glass rounded-full px-4 py-2 mb-4 border border-green-500/20">
                <i class="fa-solid fa-party-horn text-yellow-400 text-xs"></i>
                <span class="text-slate-300 text-xs font-medium">Account Created Successfully</span>
            </div>
            <h1 class="text-4xl sm:text-5xl font-black text-white mb-4">
                🎉 Congratulations!
            </h1>
            <h2 class="text-2xl sm:text-3xl font-bold mb-4">
                <span class="shimmer-text">Your ERP is Ready!</span>
            </h2>
            <p class="text-slate-400 text-lg max-w-xl mx-auto">
                Welcome to NexaERP! Your dedicated business management platform has been provisioned and is ready to use.
            </p>
        </div>

        <!-- Login URL Box -->
        <div class="fade-slide-2 mt-8" x-data="{ copied: false }">
            <p class="text-slate-400 text-sm mb-3">Your unique login URL:</p>
            <div class="url-box rounded-2xl p-4 flex items-center gap-3 max-w-lg mx-auto">
                <div class="flex-1 text-left">
                    <p class="text-slate-500 text-xs mb-0.5">ERP Dashboard URL</p>
                    <p class="gradient-text font-bold text-lg break-all" id="loginUrl">
                        {{ session('login_url', 'http://yourcompany.erp.test:8000') }}
                    </p>
                </div>
                <button
                    @click="
                        navigator.clipboard.writeText('{{ session('login_url', 'http://yourcompany.erp.test:8000') }}');
                        copied = true;
                        setTimeout(() => copied = false, 2000);
                    "
                    class="flex-shrink-0 w-10 h-10 rounded-xl transition-all duration-300 flex items-center justify-center"
                    :class="copied ? 'bg-green-500/20 border border-green-500/30' : 'glass border border-slate-600 hover:border-electric-blue/50'"
                    :title="copied ? 'Copied!' : 'Copy URL'">
                    <i :class="copied ? 'fa-solid fa-check text-green-400' : 'fa-solid fa-copy text-slate-400 hover:text-white'" class="text-sm transition-all"></i>
                </button>
            </div>
            <p x-show="copied" x-transition class="text-green-400 text-xs mt-2 flex items-center justify-center gap-1">
                <i class="fa-solid fa-check"></i> URL copied to clipboard!
            </p>

            @if(session('email'))
            <p class="text-slate-500 text-sm mt-3">
                Login with: <span class="text-slate-300 font-medium">{{ session('email') }}</span>
            </p>
            @endif
        </div>

        <!-- Go to Dashboard Button -->
        <div class="fade-slide-3 mt-6">
            <a href="{{ session('login_url', '#') }}"
               target="_blank"
               class="btn-primary-gradient text-white font-bold px-10 py-4 rounded-xl inline-flex items-center gap-3 shadow-2xl shadow-electric-blue/30 text-lg hover:scale-105 transition-transform duration-300">
                <i class="fa-solid fa-gauge-high"></i>
                Go to My Dashboard
                <i class="fa-solid fa-arrow-up-right-from-square text-sm opacity-70"></i>
            </a>
        </div>

        <!-- Next Steps Cards -->
        <div class="fade-slide-4 mt-14">
            <p class="text-slate-400 text-sm font-medium uppercase tracking-widest mb-6">What to do next</p>
            <div class="grid sm:grid-cols-3 gap-4">
                @php
                $nextSteps = [
                    ['icon'=>'fa-gauge','color'=>'text-blue-400 bg-blue-500/10 border-blue-500/20','title'=>'Login to Dashboard','desc'=>'Access your ERP dashboard and explore all available modules.','action'=>'Login Now','href'=>session('login_url','#')],
                    ['icon'=>'fa-users-gear','color'=>'text-purple-400 bg-purple-500/10 border-purple-500/20','title'=>'Setup Your Team','desc'=>'Invite team members, assign roles, and configure permissions.','action'=>'Add Users','href'=>session('login_url','#')],
                    ['icon'=>'fa-compass','color'=>'text-cyan-400 bg-cyan-500/10 border-cyan-500/20','title'=>'Explore Features','desc'=>'Discover HR, Inventory, POS, CRM, and all other modules.','action'=>'Explore','href'=>session('login_url','#')],
                ];
                @endphp

                @foreach($nextSteps as $step)
                <div class="next-card glass rounded-2xl p-5 border border-slate-700/50 text-left">
                    <div class="w-10 h-10 rounded-xl border {{ $step['color'] }} flex items-center justify-center mb-4">
                        <i class="fa-solid {{ $step['icon'] }} text-sm"></i>
                    </div>
                    <h3 class="text-white font-bold text-sm mb-2">{{ $step['title'] }}</h3>
                    <p class="text-slate-400 text-xs leading-relaxed mb-4">{{ $step['desc'] }}</p>
                    <a href="{{ $step['href'] }}" target="_blank"
                       class="text-electric-blue text-xs font-semibold hover:underline flex items-center gap-1">
                        {{ $step['action'] }} <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Support Note -->
        <div class="fade-slide-5 mt-10">
            <div class="glass rounded-2xl p-5 border border-slate-700/50 max-w-lg mx-auto">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-electric-blue/10 border border-electric-blue/20 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-headset text-electric-blue text-sm"></i>
                    </div>
                    <div class="text-left">
                        <p class="text-white font-semibold text-sm">Need help getting started?</p>
                        <p class="text-slate-400 text-xs">Our support team is available 24/7. <a href="{{ route('landlord.contact') }}" class="text-electric-blue hover:underline">Contact us</a> or check our <a href="#" class="text-electric-blue hover:underline">documentation</a>.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="fade-slide-5 mt-6">
            <a href="{{ route('landlord.home') }}" class="text-slate-500 hover:text-slate-300 text-sm transition-colors flex items-center justify-center gap-2">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to NexaERP Home
            </a>
        </div>

    </div>
</section>

@endsection

@section('scripts')
<script>
    // Auto-redirect countdown (optional UX enhancement)
    // Uncomment to enable auto-redirect after 30 seconds
    /*
    let countdown = 30;
    const loginUrl = '{{ session('login_url', '#') }}';
    const timer = setInterval(() => {
        countdown--;
        const el = document.getElementById('countdown');
        if (el) el.textContent = countdown;
        if (countdown <= 0) {
            clearInterval(timer);
            window.location.href = loginUrl;
        }
    }, 1000);
    */
</script>
@endsection
