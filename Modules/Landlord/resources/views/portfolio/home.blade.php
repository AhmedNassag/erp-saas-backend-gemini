@extends('landlord::components.layouts.master')

@section('title', 'NexaERP — The Future of Business Management')

@section('meta')
<meta name="description" content="NexaERP is the all-in-one ERP SaaS platform for modern businesses. Manage HR, Inventory, POS, CRM, Payroll and Accounting in one place.">
@endsection

@section('styles')
<style>
    .hero-glow {
        background: radial-gradient(ellipse 80% 60% at 50% -10%, rgba(59,130,246,0.25) 0%, transparent 70%);
    }
    .dashboard-mockup {
        background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
        border: 1px solid rgba(59,130,246,0.3);
        box-shadow: 0 40px 80px rgba(0,0,0,0.6), 0 0 60px rgba(59,130,246,0.15);
    }
    .stat-card { background: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(6,182,212,0.05)); }
    .feature-card:hover { transform: translateY(-6px); border-color: rgba(59,130,246,0.5); }
    .feature-card { transition: all 0.3s ease; }
    @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
    .float-anim { animation: float 4s ease-in-out infinite; }
    @keyframes countUp { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
    .counter-anim { animation: countUp 0.6s ease forwards; }
    .step-line::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 100%;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, #3B82F6, transparent);
        transform: translateY(-50%);
    }
</style>
@endsection

@section('content')

<!-- ===== HERO SECTION ===== -->
<section class="relative min-h-screen flex items-center overflow-hidden gradient-bg hero-glow pt-20">
    <!-- Background decorations -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-electric-blue/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-cyan-brand/5 rounded-full blur-3xl"></div>
        <div class="absolute top-10 right-10 w-2 h-2 bg-electric-blue rounded-full opacity-60"></div>
        <div class="absolute top-1/3 left-10 w-1 h-1 bg-cyan-brand rounded-full opacity-40"></div>
        <div class="absolute bottom-20 left-1/3 w-1.5 h-1.5 bg-electric-blue rounded-full opacity-50"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 w-full">
        <div class="grid lg:grid-cols-2 gap-16 items-center">

            <!-- Left: Text Content -->
            <div data-aos="fade-right">
                <div class="inline-flex items-center gap-2 glass rounded-full px-4 py-2 mb-6 border border-electric-blue/20">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    <span class="text-slate-300 text-xs font-medium">Trusted by 500+ companies worldwide</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black leading-tight mb-6">
                    <span class="text-white">Scale Your Business</span><br>
                    <span class="gradient-text">with NexaERP</span>
                </h1>

                <p class="text-slate-400 text-lg leading-relaxed mb-8 max-w-lg">
                    The all-in-one cloud ERP platform that unifies HR, Inventory, POS, CRM, Payroll, and Accounting — so you can focus on growth, not complexity.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 mb-10">
                    <a href="{{ route('landlord.pricing') }}"
                       class="btn-primary-gradient text-white font-semibold px-8 py-4 rounded-xl text-center shadow-xl flex items-center justify-center gap-2">
                        <i class="fa-solid fa-rocket"></i>
                        Get Started Free
                    </a>
                    <a href="#demo"
                       class="glass border border-slate-600 hover:border-electric-blue/50 text-white font-semibold px-8 py-4 rounded-xl text-center transition-all duration-300 flex items-center justify-center gap-2 hover:bg-white/5">
                        <i class="fa-solid fa-circle-play text-electric-blue"></i>
                        Watch Demo
                    </a>
                </div>

                <div class="flex items-center gap-6 text-sm text-slate-500">
                    <div class="flex items-center gap-2"><i class="fa-solid fa-check text-green-400"></i> No credit card required</div>
                    <div class="flex items-center gap-2"><i class="fa-solid fa-check text-green-400"></i> 14-day free trial</div>
                    <div class="flex items-center gap-2"><i class="fa-solid fa-check text-green-400"></i> Cancel anytime</div>
                </div>
            </div>

            <!-- Right: Dashboard Mockup -->
            <div data-aos="fade-left" data-aos-delay="200">
                <div class="float-anim relative">
                    <div class="dashboard-mockup rounded-2xl p-4 relative overflow-hidden">
                        <!-- Mockup Header -->
                        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-700/50">
                            <div class="w-3 h-3 rounded-full bg-red-500/70"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-500/70"></div>
                            <div class="w-3 h-3 rounded-full bg-green-500/70"></div>
                            <div class="ml-4 flex-1 h-5 bg-slate-700/50 rounded-md"></div>
                        </div>
                        <!-- Mockup Sidebar + Content -->
                        <div class="flex gap-3">
                            <div class="w-12 space-y-2">
                                <div class="h-8 bg-electric-blue/20 rounded-lg flex items-center justify-center"><i class="fa-solid fa-gauge text-electric-blue text-xs"></i></div>
                                <div class="h-8 bg-slate-700/30 rounded-lg"></div>
                                <div class="h-8 bg-slate-700/30 rounded-lg"></div>
                                <div class="h-8 bg-slate-700/30 rounded-lg"></div>
                                <div class="h-8 bg-slate-700/30 rounded-lg"></div>
                            </div>
                            <div class="flex-1 space-y-3">
                                <!-- Stats Row -->
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="bg-electric-blue/10 border border-electric-blue/20 rounded-lg p-2">
                                        <div class="text-xs text-slate-400 mb-1">Revenue</div>
                                        <div class="text-sm font-bold text-white">$84.2K</div>
                                        <div class="text-xs text-green-400">+12.5%</div>
                                    </div>
                                    <div class="bg-cyan-brand/10 border border-cyan-brand/20 rounded-lg p-2">
                                        <div class="text-xs text-slate-400 mb-1">Orders</div>
                                        <div class="text-sm font-bold text-white">1,284</div>
                                        <div class="text-xs text-green-400">+8.1%</div>
                                    </div>
                                    <div class="bg-purple-500/10 border border-purple-500/20 rounded-lg p-2">
                                        <div class="text-xs text-slate-400 mb-1">Users</div>
                                        <div class="text-sm font-bold text-white">342</div>
                                        <div class="text-xs text-green-400">+5.3%</div>
                                    </div>
                                </div>
                                <!-- Chart Placeholder -->
                                <div class="bg-slate-800/50 rounded-lg p-3 h-24 flex items-end gap-1">
                                    @foreach([40,65,45,80,55,90,70,85,60,95,75,88] as $h)
                                    <div class="flex-1 rounded-sm" style="height:{{ $h }}%; background: linear-gradient(to top, #3B82F6, #06B6D4); opacity: 0.7;"></div>
                                    @endforeach
                                </div>
                                <!-- Table Placeholder -->
                                <div class="space-y-1.5">
                                    @for($i=0;$i<3;$i++)
                                    <div class="flex items-center gap-2 bg-slate-800/30 rounded-lg px-2 py-1.5">
                                        <div class="w-5 h-5 rounded bg-electric-blue/20"></div>
                                        <div class="flex-1 h-2 bg-slate-700/50 rounded"></div>
                                        <div class="w-12 h-2 bg-slate-700/50 rounded"></div>
                                        <div class="w-8 h-4 bg-green-500/20 rounded text-green-400 text-xs flex items-center justify-center">✓</div>
                                    </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Floating badges -->
                    <div class="absolute -top-4 -right-4 glass rounded-xl px-3 py-2 border border-green-500/30 shadow-xl">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                            <span class="text-xs text-white font-medium">99.9% Uptime</span>
                        </div>
                    </div>
                    <div class="absolute -bottom-4 -left-4 glass rounded-xl px-3 py-2 border border-electric-blue/30 shadow-xl">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-electric-blue text-xs"></i>
                            <span class="text-xs text-white font-medium">Enterprise Security</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== TRUSTED BY ===== -->
<section class="py-14 bg-slate-900/50 border-y border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-center text-slate-500 text-sm font-medium uppercase tracking-widest mb-8" data-aos="fade-up">Trusted by industry leaders</p>
        <div class="flex flex-wrap items-center justify-center gap-8 lg:gap-16" data-aos="fade-up" data-aos-delay="100">
            @foreach(['TechCorp', 'GlobalTrade', 'RetailMax', 'FinanceHub', 'LogiPro', 'BuildCo'] as $company)
            <div class="text-slate-600 hover:text-slate-400 transition-colors duration-300 font-bold text-lg tracking-tight cursor-default">
                {{ $company }}
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ===== FEATURES ===== -->
<section id="features" class="py-24 bg-deep-blue">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <span class="text-electric-blue text-sm font-semibold uppercase tracking-widest">Everything You Need</span>
            <h2 class="text-3xl sm:text-4xl font-black text-white mt-3 mb-4">Powerful Features for <span class="gradient-text">Modern Business</span></h2>
            <p class="text-slate-400 max-w-2xl mx-auto">From HR to accounting, NexaERP covers every aspect of your business operations in one unified platform.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
            $features = [
                ['icon'=>'fa-users','color'=>'blue','title'=>'HR Management','desc'=>'Streamline hiring, onboarding, attendance, leaves, and performance reviews with intelligent automation.'],
                ['icon'=>'fa-boxes-stacked','color'=>'cyan','title'=>'Inventory Control','desc'=>'Real-time stock tracking, multi-warehouse management, automated reorder points, and barcode scanning.'],
                ['icon'=>'fa-cash-register','color'=>'purple','title'=>'POS System','desc'=>'Lightning-fast point of sale with offline mode, multi-payment support, and real-time sales analytics.'],
                ['icon'=>'fa-handshake','color'=>'green','title'=>'CRM','desc'=>'Manage leads, deals, and customer relationships with a visual pipeline and automated follow-ups.'],
                ['icon'=>'fa-money-bill-wave','color'=>'yellow','title'=>'Payroll','desc'=>'Automated salary calculations, tax compliance, payslip generation, and direct bank transfers.'],
                ['icon'=>'fa-chart-line','color'=>'red','title'=>'Accounting','desc'=>'Full double-entry bookkeeping, financial reports, invoicing, and multi-currency support.'],
            ];
            $colors = ['blue'=>'text-blue-400 bg-blue-500/10 border-blue-500/20','cyan'=>'text-cyan-400 bg-cyan-500/10 border-cyan-500/20','purple'=>'text-purple-400 bg-purple-500/10 border-purple-500/20','green'=>'text-green-400 bg-green-500/10 border-green-500/20','yellow'=>'text-yellow-400 bg-yellow-500/10 border-yellow-500/20','red'=>'text-red-400 bg-red-500/10 border-red-500/20'];
            @endphp

            @foreach($features as $i => $feature)
            <div class="feature-card glass rounded-2xl p-6 border border-slate-700/50 cursor-default"
                 data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                <div class="w-12 h-12 rounded-xl border {{ $colors[$feature['color']] }} flex items-center justify-center mb-5">
                    <i class="fa-solid {{ $feature['icon'] }} text-lg"></i>
                </div>
                <h3 class="text-white font-bold text-lg mb-2">{{ $feature['title'] }}</h3>
                <p class="text-slate-400 text-sm leading-relaxed">{{ $feature['desc'] }}</p>
                <div class="mt-4 flex items-center gap-1 text-electric-blue text-sm font-medium hover:gap-2 transition-all cursor-pointer">
                    Learn more <i class="fa-solid fa-arrow-right text-xs"></i>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ===== HOW IT WORKS ===== -->
<section id="how-it-works" class="py-24 bg-slate-900/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <span class="text-electric-blue text-sm font-semibold uppercase tracking-widest">Simple Process</span>
            <h2 class="text-3xl sm:text-4xl font-black text-white mt-3 mb-4">Get Started in <span class="gradient-text">3 Easy Steps</span></h2>
            <p class="text-slate-400 max-w-xl mx-auto">From sign-up to fully operational in minutes — no IT team required.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 relative">
            @php
            $steps = [
                ['num'=>'01','icon'=>'fa-user-plus','title'=>'Create Your Account','desc'=>'Sign up, choose your plan, and set up your company profile in under 5 minutes.'],
                ['num'=>'02','icon'=>'fa-sliders','title'=>'Configure Your Modules','desc'=>'Enable the modules you need — HR, Inventory, POS, CRM — and customize settings to fit your workflow.'],
                ['num'=>'03','icon'=>'fa-rocket','title'=>'Launch & Scale','desc'=>'Invite your team, import your data, and start managing your business smarter from day one.'],
            ];
            @endphp

            @foreach($steps as $i => $step)
            <div class="relative text-center" data-aos="fade-up" data-aos-delay="{{ $i * 150 }}">
                @if($i < 2)
                <div class="hidden md:block absolute top-10 left-[60%] right-0 h-px bg-gradient-to-r from-electric-blue/50 to-transparent z-0"></div>
                @endif
                <div class="relative z-10">
                    <div class="w-20 h-20 rounded-2xl btn-primary-gradient flex items-center justify-center mx-auto mb-6 shadow-xl shadow-electric-blue/20">
                        <i class="fa-solid {{ $step['icon'] }} text-2xl text-white"></i>
                    </div>
                    <div class="absolute -top-2 -right-2 w-8 h-8 rounded-full bg-slate-900 border-2 border-electric-blue flex items-center justify-center mx-auto">
                        <span class="text-electric-blue text-xs font-black">{{ $step['num'] }}</span>
                    </div>
                </div>
                <h3 class="text-white font-bold text-xl mb-3">{{ $step['title'] }}</h3>
                <p class="text-slate-400 text-sm leading-relaxed max-w-xs mx-auto">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ===== STATS ===== -->
<section class="py-20 gradient-bg relative overflow-hidden">
    <div class="absolute inset-0 hero-glow pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
            @php
            $stats = [
                ['value'=>'500+','label'=>'Companies','icon'=>'fa-building','color'=>'text-blue-400'],
                ['value'=>'50K+','label'=>'Active Users','icon'=>'fa-users','color'=>'text-cyan-400'],
                ['value'=>'99.9%','label'=>'Uptime SLA','icon'=>'fa-server','color'=>'text-green-400'],
                ['value'=>'24/7','label'=>'Support','icon'=>'fa-headset','color'=>'text-purple-400'],
            ];
            @endphp

            @foreach($stats as $i => $stat)
            <div class="stat-card glass rounded-2xl p-6 text-center border border-slate-700/50" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid {{ $stat['icon'] }} {{ $stat['color'] }} text-xl"></i>
                </div>
                <div class="text-3xl sm:text-4xl font-black gradient-text mb-1">{{ $stat['value'] }}</div>
                <div class="text-slate-400 text-sm font-medium">{{ $stat['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ===== TESTIMONIALS ===== -->
<section id="testimonials" class="py-24 bg-deep-blue">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <span class="text-electric-blue text-sm font-semibold uppercase tracking-widest">What Clients Say</span>
            <h2 class="text-3xl sm:text-4xl font-black text-white mt-3 mb-4">Loved by <span class="gradient-text">Businesses Worldwide</span></h2>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            @php
            $testimonials = [
                ['name'=>'Sarah Johnson','role'=>'CEO, TechCorp','quote'=>'NexaERP transformed how we manage our 200-person team. The HR and payroll modules alone saved us 20 hours per week. Absolutely game-changing.','rating'=>5,'initials'=>'SJ','color'=>'from-blue-500 to-cyan-500'],
                ['name'=>'Michael Chen','role'=>'Operations Director, GlobalTrade','quote'=>'The inventory management is incredibly powerful. Real-time tracking across 5 warehouses, automated reorders — it just works. Our stockouts dropped by 90%.','rating'=>5,'initials'=>'MC','color'=>'from-purple-500 to-pink-500'],
                ['name'=>'Aisha Al-Rashid','role'=>'CFO, RetailMax','quote'=>'The accounting module gives us financial clarity we never had before. Multi-currency, automated reconciliation, and beautiful reports. Worth every penny.','rating'=>5,'initials'=>'AA','color'=>'from-green-500 to-teal-500'],
            ];
            @endphp

            @foreach($testimonials as $i => $t)
            <div class="glass rounded-2xl p-6 border border-slate-700/50 hover:border-electric-blue/30 transition-all duration-300 flex flex-col"
                 data-aos="fade-up" data-aos-delay="{{ $i * 120 }}">
                <div class="flex items-center gap-1 mb-4">
                    @for($s=0;$s<$t['rating'];$s++)
                    <i class="fa-solid fa-star text-yellow-400 text-sm"></i>
                    @endfor
                </div>
                <p class="text-slate-300 text-sm leading-relaxed flex-1 mb-6">"{{ $t['quote'] }}"</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $t['color'] }} flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                        {{ $t['initials'] }}
                    </div>
                    <div>
                        <div class="text-white font-semibold text-sm">{{ $t['name'] }}</div>
                        <div class="text-slate-500 text-xs">{{ $t['role'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ===== CTA BANNER ===== -->
<section class="py-20 relative overflow-hidden">
    <div class="absolute inset-0" style="background: linear-gradient(135deg, #1E3A5F 0%, #0F172A 40%, #1a1040 100%);"></div>
    <div class="absolute inset-0 hero-glow pointer-events-none"></div>
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center" data-aos="fade-up">
        <div class="inline-flex items-center gap-2 glass rounded-full px-4 py-2 mb-6 border border-electric-blue/20">
            <i class="fa-solid fa-bolt text-electric-blue text-xs"></i>
            <span class="text-slate-300 text-xs font-medium">Limited time: 20% off annual plans</span>
        </div>
        <h2 class="text-3xl sm:text-5xl font-black text-white mb-6">
            Ready to Transform<br><span class="gradient-text">Your Business?</span>
        </h2>
        <p class="text-slate-400 text-lg mb-10 max-w-2xl mx-auto">
            Join 500+ companies already using NexaERP to streamline operations and accelerate growth. Start your free trial today — no credit card required.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('landlord.pricing') }}"
               class="btn-primary-gradient text-white font-bold px-10 py-4 rounded-xl shadow-2xl shadow-electric-blue/30 flex items-center justify-center gap-2 text-lg">
                <i class="fa-solid fa-rocket"></i>
                Start Free Trial
            </a>
            <a href="{{ route('landlord.contact') }}"
               class="glass border border-slate-600 hover:border-electric-blue/50 text-white font-semibold px-10 py-4 rounded-xl transition-all duration-300 flex items-center justify-center gap-2 text-lg hover:bg-white/5">
                <i class="fa-solid fa-calendar text-electric-blue"></i>
                Book a Demo
            </a>
        </div>
    </div>
</section>

@endsection
