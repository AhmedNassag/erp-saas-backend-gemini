@extends('landlord::components.layouts.master')

@section('title', 'NexaERP Pricing — Simple, Transparent Plans')

@section('meta')
<meta name="description" content="Choose the NexaERP plan that fits your business. Starter, Professional, and Enterprise plans with transparent pricing and no hidden fees.">
@endsection

@section('styles')
<style>
    .pricing-card { transition: all 0.3s ease; }
    .pricing-card:hover { transform: translateY(-6px); }
    .popular-card {
        background: linear-gradient(135deg, rgba(59,130,246,0.15), rgba(6,182,212,0.08));
        border-color: rgba(59,130,246,0.5) !important;
        box-shadow: 0 0 40px rgba(59,130,246,0.15);
    }
    .toggle-bg { background: rgba(255,255,255,0.08); }
    .toggle-active { background: linear-gradient(135deg, #3B82F6, #06B6D4); }
    .check-icon { color: #3B82F6; }
    .cross-icon { color: #475569; }
    .table-row:hover { background: rgba(59,130,246,0.05); }
</style>
@endsection

@section('content')

<!-- ===== PAGE HERO ===== -->
<section class="relative pt-32 pb-20 gradient-bg overflow-hidden">
    <div class="absolute inset-0 hero-glow pointer-events-none"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div data-aos="fade-up">
            <div class="inline-flex items-center gap-2 glass rounded-full px-4 py-2 mb-6 border border-electric-blue/20">
                <i class="fa-solid fa-tag text-electric-blue text-xs"></i>
                <span class="text-slate-300 text-xs font-medium">Simple, transparent pricing</span>
            </div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white mb-6">
                Plans for Every <span class="gradient-text">Business Size</span>
            </h1>
            <p class="text-slate-400 text-lg max-w-2xl mx-auto mb-8">
                No hidden fees. No long-term contracts. Start free, scale as you grow.
            </p>
            <nav class="flex items-center justify-center gap-2 text-sm text-slate-500">
                <a href="{{ route('landlord.home') }}" class="hover:text-white transition-colors">Home</a>
                <i class="fa-solid fa-chevron-right text-xs"></i>
                <span class="text-slate-300">Pricing</span>
            </nav>
        </div>
    </div>
</section>

<!-- ===== BILLING TOGGLE + PRICING CARDS ===== -->
<section class="py-20 bg-slate-900/40" x-data="{ annual: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Toggle -->
        <div class="flex items-center justify-center gap-4 mb-14" data-aos="fade-up">
            <span :class="!annual ? 'text-white font-semibold' : 'text-slate-500'" class="text-sm transition-colors">Monthly</span>
            <button @click="annual = !annual"
                    class="relative w-14 h-7 rounded-full toggle-bg border border-slate-600 transition-all duration-300 focus:outline-none"
                    :class="annual ? 'border-electric-blue/50' : ''">
                <div class="absolute top-0.5 left-0.5 w-6 h-6 rounded-full transition-all duration-300 shadow-md"
                     :class="annual ? 'translate-x-7 toggle-active' : 'bg-slate-400'"></div>
            </button>
            <span :class="annual ? 'text-white font-semibold' : 'text-slate-500'" class="text-sm transition-colors">
                Annual
                <span class="ml-1.5 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-green-500/20 text-green-400 border border-green-500/30">
                    Save 20%
                </span>
            </span>
        </div>

        @php
        // Static fallback plans — controller passes $packages from DB
        $staticPlans = [
            [
                'id' => 1,
                'name' => 'Starter',
                'monthly_price' => 29,
                'annual_price' => 23,
                'description' => 'Perfect for small businesses getting started with ERP.',
                'color' => 'blue',
                'icon' => 'fa-seedling',
                'popular' => false,
                'features' => ['Up to 10 Users','HR Management','Basic Inventory','POS System','Email Support','1 Warehouse','5GB Storage','Monthly Reports'],
                'missing' => ['CRM Module','Payroll','Advanced Accounting','API Access','Custom Integrations'],
            ],
            [
                'id' => 2,
                'name' => 'Professional',
                'monthly_price' => 79,
                'annual_price' => 63,
                'description' => 'For growing businesses that need the full ERP suite.',
                'color' => 'cyan',
                'icon' => 'fa-rocket',
                'popular' => true,
                'features' => ['Up to 50 Users','Full HR Suite','Advanced Inventory','POS + Multi-Register','CRM Module','Payroll','Full Accounting','Priority Support','5 Warehouses','50GB Storage','Advanced Reports','API Access'],
                'missing' => ['Custom Integrations','Dedicated Manager'],
            ],
            [
                'id' => 3,
                'name' => 'Enterprise',
                'monthly_price' => 199,
                'annual_price' => 159,
                'description' => 'For large organizations requiring maximum power and support.',
                'color' => 'purple',
                'icon' => 'fa-crown',
                'popular' => false,
                'features' => ['Unlimited Users','Full HR Suite','Advanced Inventory','POS + Multi-Register','CRM Module','Payroll','Full Accounting','24/7 Phone Support','Unlimited Warehouses','500GB Storage','Custom Reports','API Access','Custom Integrations','Dedicated Account Manager','SLA Guarantee','White-label Option'],
                'missing' => [],
            ],
        ];
        $plans = isset($packages) && $packages->count() > 0 ? $packages : collect($staticPlans);
        @endphp

        <!-- Pricing Cards -->
        <div class="grid md:grid-cols-3 gap-6 items-start">
            @foreach($staticPlans as $i => $plan)
            <div class="pricing-card glass rounded-2xl border {{ $plan['popular'] ? 'popular-card' : 'border-slate-700/50' }} overflow-hidden relative"
                 data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">

                @if($plan['popular'])
                <div class="absolute top-0 left-0 right-0 h-1 btn-primary-gradient"></div>
                <div class="absolute top-4 right-4">
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-electric-blue text-white shadow-lg">
                        <i class="fa-solid fa-star text-xs"></i>
                        Most Popular
                    </span>
                </div>
                @endif

                <div class="p-7">
                    <!-- Plan Header -->
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl btn-primary-gradient flex items-center justify-center">
                            <i class="fa-solid {{ $plan['icon'] }} text-white text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-black text-xl">{{ $plan['name'] }}</h3>
                        </div>
                    </div>

                    <p class="text-slate-400 text-sm mb-6">{{ $plan['description'] }}</p>

                    <!-- Price -->
                    <div class="mb-6">
                        <div class="flex items-end gap-1">
                            <span class="text-slate-400 text-lg">$</span>
                            <span x-text="annual ? '{{ $plan['annual_price'] }}' : '{{ $plan['monthly_price'] }}'"
                                  class="text-5xl font-black text-white leading-none"></span>
                            <span class="text-slate-400 text-sm mb-1">/mo</span>
                        </div>
                        <p x-show="annual" class="text-green-400 text-xs mt-1 font-medium">
                            <i class="fa-solid fa-check"></i>
                            Billed annually — save ${{ ($plan['monthly_price'] - $plan['annual_price']) * 12 }}/year
                        </p>
                        <p x-show="!annual" class="text-slate-500 text-xs mt-1">Billed monthly</p>
                    </div>

                    <!-- CTA -->
                    <a href="{{ route('landlord.subscribe.form', $plan['id']) }}"
                       class="{{ $plan['popular'] ? 'btn-primary-gradient shadow-xl shadow-electric-blue/20' : 'glass border border-slate-600 hover:border-electric-blue/50 hover:bg-white/5' }} block text-white font-bold py-3.5 rounded-xl text-center transition-all duration-300 mb-7">
                        Get Started
                    </a>

                    <!-- Features -->
                    <div class="space-y-2.5">
                        @foreach($plan['features'] as $feature)
                        <div class="flex items-center gap-3">
                            <div class="w-5 h-5 rounded-full bg-electric-blue/10 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-check check-icon text-xs"></i>
                            </div>
                            <span class="text-slate-300 text-sm">{{ $feature }}</span>
                        </div>
                        @endforeach
                        @foreach($plan['missing'] as $missing)
                        <div class="flex items-center gap-3 opacity-40">
                            <div class="w-5 h-5 rounded-full bg-slate-700/50 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-xmark cross-icon text-xs"></i>
                            </div>
                            <span class="text-slate-500 text-sm line-through">{{ $missing }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Money-back guarantee -->
        <div class="text-center mt-10" data-aos="fade-up">
            <div class="inline-flex items-center gap-3 glass rounded-full px-6 py-3 border border-green-500/20">
                <i class="fa-solid fa-shield-halved text-green-400"></i>
                <span class="text-slate-300 text-sm">30-day money-back guarantee — no questions asked</span>
            </div>
        </div>
    </div>
</section>

<!-- ===== FEATURE COMPARISON TABLE ===== -->
<section class="py-20 bg-deep-blue">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="text-electric-blue text-sm font-semibold uppercase tracking-widest">Compare Plans</span>
            <h2 class="text-3xl sm:text-4xl font-black text-white mt-3 mb-4">Full Feature <span class="gradient-text">Comparison</span></h2>
        </div>

        <div class="glass rounded-2xl border border-slate-700/50 overflow-hidden" data-aos="fade-up" data-aos-delay="100">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-700/50">
                            <th class="text-left px-6 py-4 text-slate-400 font-semibold text-sm w-1/2">Feature</th>
                            <th class="text-center px-4 py-4 text-white font-bold text-sm">Starter</th>
                            <th class="text-center px-4 py-4 text-electric-blue font-bold text-sm">Professional</th>
                            <th class="text-center px-4 py-4 text-white font-bold text-sm">Enterprise</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $comparison = [
                            ['feature'=>'Users','starter'=>'10','pro'=>'50','enterprise'=>'Unlimited'],
                            ['feature'=>'Warehouses','starter'=>'1','pro'=>'5','enterprise'=>'Unlimited'],
                            ['feature'=>'Storage','starter'=>'5 GB','pro'=>'50 GB','enterprise'=>'500 GB'],
                            ['feature'=>'HR Management','starter'=>true,'pro'=>true,'enterprise'=>true],
                            ['feature'=>'Inventory Control','starter'=>true,'pro'=>true,'enterprise'=>true],
                            ['feature'=>'POS System','starter'=>true,'pro'=>true,'enterprise'=>true],
                            ['feature'=>'CRM Module','starter'=>false,'pro'=>true,'enterprise'=>true],
                            ['feature'=>'Payroll','starter'=>false,'pro'=>true,'enterprise'=>true],
                            ['feature'=>'Full Accounting','starter'=>false,'pro'=>true,'enterprise'=>true],
                            ['feature'=>'API Access','starter'=>false,'pro'=>true,'enterprise'=>true],
                            ['feature'=>'Custom Integrations','starter'=>false,'pro'=>false,'enterprise'=>true],
                            ['feature'=>'White-label Option','starter'=>false,'pro'=>false,'enterprise'=>true],
                            ['feature'=>'Dedicated Account Manager','starter'=>false,'pro'=>false,'enterprise'=>true],
                            ['feature'=>'Support','starter'=>'Email','pro'=>'Priority','enterprise'=>'24/7 Phone'],
                            ['feature'=>'SLA Uptime','starter'=>'99.5%','pro'=>'99.9%','enterprise'=>'99.99%'],
                        ];
                        @endphp

                        @foreach($comparison as $i => $row)
                        <tr class="table-row border-b border-slate-700/30 last:border-0 transition-colors">
                            <td class="px-6 py-3.5 text-slate-300 text-sm font-medium">{{ $row['feature'] }}</td>
                            @foreach(['starter','pro','enterprise'] as $col)
                            <td class="text-center px-4 py-3.5">
                                @if(is_bool($row[$col]))
                                    @if($row[$col])
                                    <i class="fa-solid fa-check text-electric-blue"></i>
                                    @else
                                    <i class="fa-solid fa-minus text-slate-700"></i>
                                    @endif
                                @else
                                <span class="{{ $col === 'pro' ? 'text-electric-blue font-semibold' : 'text-slate-300' }} text-sm">{{ $row[$col] }}</span>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- ===== PRICING FAQ ===== -->
<section class="py-20 bg-slate-900/40">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="text-electric-blue text-sm font-semibold uppercase tracking-widest">Pricing FAQ</span>
            <h2 class="text-3xl font-black text-white mt-3 mb-4">Common <span class="gradient-text">Questions</span></h2>
        </div>

        <div class="space-y-3" x-data="{ open: null }">
            @php
            $pricingFaqs = [
                ['q'=>'Can I change my plan later?','a'=>'Yes, you can upgrade or downgrade your plan at any time. Upgrades take effect immediately and you\'ll be charged the prorated difference. Downgrades take effect at the next billing cycle.'],
                ['q'=>'What payment methods do you accept?','a'=>'We accept InstaPay, mobile wallets (Vodafone Cash, Etisalat Flous, Orange Money, WE Pay), and all major credit/debit cards (Visa, Mastercard). All payments are processed securely via PayMob.'],
                ['q'=>'Is there a setup fee?','a'=>'No setup fees, ever. The price you see is the price you pay. Your account is provisioned instantly after signup with no additional charges.'],
                ['q'=>'What happens when my trial ends?','a'=>'At the end of your 14-day trial, you\'ll be prompted to choose a plan. If you don\'t upgrade, your account will be paused (not deleted) and you can reactivate anytime within 30 days.'],
            ];
            @endphp

            @foreach($pricingFaqs as $i => $faq)
            <div class="glass rounded-xl border border-slate-700/50 overflow-hidden" data-aos="fade-up" data-aos-delay="{{ $i * 60 }}">
                <button @click="open === {{ $i }} ? open = null : open = {{ $i }}"
                        class="w-full flex items-center justify-between px-6 py-4 text-left hover:bg-white/3 transition-colors">
                    <span class="text-white font-semibold text-sm pr-4">{{ $faq['q'] }}</span>
                    <div class="w-7 h-7 rounded-lg bg-electric-blue/10 flex items-center justify-center flex-shrink-0 transition-transform duration-300"
                         :class="open === {{ $i }} ? 'rotate-45 bg-electric-blue/20' : ''">
                        <i class="fa-solid fa-plus text-electric-blue text-xs"></i>
                    </div>
                </button>
                <div x-show="open === {{ $i }}"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="px-6 pb-5">
                    <p class="text-slate-400 text-sm leading-relaxed">{{ $faq['a'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-10" data-aos="fade-up">
            <p class="text-slate-400 text-sm mb-4">Still have questions?</p>
            <a href="{{ route('landlord.contact') }}"
               class="btn-primary-gradient text-white font-semibold px-8 py-3 rounded-xl inline-flex items-center gap-2 shadow-lg">
                <i class="fa-solid fa-message"></i>
                Contact Sales
            </a>
        </div>
    </div>
</section>

@endsection
