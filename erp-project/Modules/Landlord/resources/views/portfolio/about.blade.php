@extends('landlord::components.layouts.master')

@section('title', 'About NexaERP — Our Story & Mission')

@section('meta')
<meta name="description" content="Learn about NexaERP's mission to revolutionize business management with cutting-edge ERP solutions for modern enterprises.">
@endsection

@section('styles')
<style>
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -1px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, #3B82F6, #06B6D4);
    }
    .value-card:hover { transform: translateY(-4px); }
    .value-card { transition: all 0.3s ease; }
    .team-card:hover .team-overlay { opacity: 1; }
    .team-overlay { transition: opacity 0.3s ease; }
</style>
@endsection

@section('content')

<!-- ===== PAGE HERO ===== -->
<section class="relative pt-32 pb-20 gradient-bg overflow-hidden">
    <div class="absolute inset-0 hero-glow pointer-events-none"></div>
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 right-20 w-64 h-64 bg-electric-blue/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 left-20 w-48 h-48 bg-cyan-brand/5 rounded-full blur-3xl"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div data-aos="fade-up">
            <div class="inline-flex items-center gap-2 glass rounded-full px-4 py-2 mb-6 border border-electric-blue/20">
                <i class="fa-solid fa-building text-electric-blue text-xs"></i>
                <span class="text-slate-300 text-xs font-medium">Our Story</span>
            </div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white mb-6">
                About <span class="gradient-text">NexaERP</span>
            </h1>
            <p class="text-slate-400 text-lg max-w-2xl mx-auto mb-8">
                We're on a mission to make enterprise-grade business management accessible to every company, regardless of size.
            </p>
            <!-- Breadcrumb -->
            <nav class="flex items-center justify-center gap-2 text-sm text-slate-500">
                <a href="{{ route('landlord.home') }}" class="hover:text-white transition-colors">Home</a>
                <i class="fa-solid fa-chevron-right text-xs"></i>
                <span class="text-slate-300">About</span>
            </nav>
        </div>
    </div>
</section>

<!-- ===== MISSION & VISION ===== -->
<section class="py-24 bg-slate-900/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-8">

            <div class="glass rounded-2xl p-8 border border-slate-700/50 hover:border-electric-blue/30 transition-all duration-300" data-aos="fade-right">
                <div class="w-14 h-14 rounded-2xl bg-electric-blue/10 border border-electric-blue/20 flex items-center justify-center mb-6">
                    <i class="fa-solid fa-bullseye text-electric-blue text-2xl"></i>
                </div>
                <h3 class="text-2xl font-black text-white mb-4">Our Mission</h3>
                <p class="text-slate-400 leading-relaxed mb-4">
                    To democratize enterprise resource planning by delivering powerful, intuitive, and affordable cloud-based ERP solutions that empower businesses of all sizes to operate at their full potential.
                </p>
                <p class="text-slate-400 leading-relaxed">
                    We believe every business deserves the tools that Fortune 500 companies use — without the complexity or the price tag.
                </p>
            </div>

            <div class="glass rounded-2xl p-8 border border-slate-700/50 hover:border-cyan-brand/30 transition-all duration-300" data-aos="fade-left">
                <div class="w-14 h-14 rounded-2xl bg-cyan-brand/10 border border-cyan-brand/20 flex items-center justify-center mb-6">
                    <i class="fa-solid fa-eye text-cyan-brand text-2xl"></i>
                </div>
                <h3 class="text-2xl font-black text-white mb-4">Our Vision</h3>
                <p class="text-slate-400 leading-relaxed mb-4">
                    To become the world's most trusted ERP platform — a single source of truth for every business operation, from the first employee to the ten-thousandth.
                </p>
                <p class="text-slate-400 leading-relaxed">
                    We envision a future where business intelligence is instant, decisions are data-driven, and growth is limitless.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ===== OUR STORY TIMELINE ===== -->
<section class="py-24 bg-deep-blue">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <span class="text-electric-blue text-sm font-semibold uppercase tracking-widest">Our Journey</span>
            <h2 class="text-3xl sm:text-4xl font-black text-white mt-3 mb-4">The <span class="gradient-text">NexaERP Story</span></h2>
            <p class="text-slate-400 max-w-xl mx-auto">From a small startup to a platform trusted by hundreds of companies worldwide.</p>
        </div>

        <div class="relative pl-8 border-l-2 border-slate-700 space-y-12">
            @php
            $milestones = [
                ['year'=>'2020','title'=>'The Beginning','desc'=>'NexaERP was founded by a team of enterprise software veterans frustrated with the complexity and cost of existing ERP solutions. We set out to build something better.','icon'=>'fa-seedling','color'=>'text-green-400 bg-green-500/10 border-green-500/30'],
                ['year'=>'2021','title'=>'First 50 Clients','desc'=>'After 12 months of intense development, we launched our MVP with HR and Inventory modules. Within 6 months, 50 companies had signed up and our NPS score was 72.','icon'=>'fa-users','color'=>'text-blue-400 bg-blue-500/10 border-blue-500/30'],
                ['year'=>'2022','title'=>'Full Platform Launch','desc'=>'We launched POS, CRM, Payroll, and Accounting modules, completing our full ERP suite. Series A funding of $8M allowed us to scale our engineering team to 40 people.','icon'=>'fa-rocket','color'=>'text-purple-400 bg-purple-500/10 border-purple-500/30'],
                ['year'=>'2024','title'=>'500+ Companies & Growing','desc'=>'Today, NexaERP powers 500+ companies across 30 countries with 50,000+ active users. We\'re expanding into AI-powered analytics and predictive business intelligence.','icon'=>'fa-trophy','color'=>'text-yellow-400 bg-yellow-500/10 border-yellow-500/30'],
            ];
            @endphp

            @foreach($milestones as $i => $m)
            <div class="relative" data-aos="fade-right" data-aos-delay="{{ $i * 100 }}">
                <!-- Dot on timeline -->
                <div class="absolute -left-[2.85rem] top-1 w-6 h-6 rounded-full btn-primary-gradient border-2 border-deep-blue flex items-center justify-center">
                    <div class="w-2 h-2 bg-white rounded-full"></div>
                </div>

                <div class="glass rounded-2xl p-6 border border-slate-700/50 hover:border-electric-blue/30 transition-all duration-300">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl border {{ $m['color'] }} flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid {{ $m['icon'] }} text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="gradient-text font-black text-xl">{{ $m['year'] }}</span>
                                <h3 class="text-white font-bold text-lg">{{ $m['title'] }}</h3>
                            </div>
                            <p class="text-slate-400 text-sm leading-relaxed">{{ $m['desc'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ===== TEAM ===== -->
<section class="py-24 bg-slate-900/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <span class="text-electric-blue text-sm font-semibold uppercase tracking-widest">The People</span>
            <h2 class="text-3xl sm:text-4xl font-black text-white mt-3 mb-4">Meet Our <span class="gradient-text">Leadership Team</span></h2>
            <p class="text-slate-400 max-w-xl mx-auto">Experienced leaders passionate about building the future of business management.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
            $team = [
                ['name'=>'Alex Morgan','role'=>'CEO & Co-Founder','initials'=>'AM','color'=>'from-blue-500 to-cyan-500','bio'=>'15 years in enterprise software. Former VP at SAP.'],
                ['name'=>'Priya Sharma','role'=>'CTO & Co-Founder','initials'=>'PS','color'=>'from-purple-500 to-pink-500','bio'=>'Ex-Google engineer. Built systems at scale for 100M+ users.'],
                ['name'=>'James Wilson','role'=>'Head of Product','initials'=>'JW','color'=>'from-green-500 to-teal-500','bio'=>'Product leader with 10+ years in B2B SaaS.'],
                ['name'=>'Layla Hassan','role'=>'Head of Customer Success','initials'=>'LH','color'=>'from-orange-500 to-red-500','bio'=>'Passionate about helping businesses achieve their goals.'],
            ];
            @endphp

            @foreach($team as $i => $member)
            <div class="team-card glass rounded-2xl overflow-hidden border border-slate-700/50 hover:border-electric-blue/30 transition-all duration-300 group"
                 data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                <!-- Avatar -->
                <div class="relative h-48 bg-gradient-to-br {{ $member['color'] }} flex items-center justify-center">
                    <span class="text-5xl font-black text-white/90">{{ $member['initials'] }}</span>
                    <!-- Overlay -->
                    <div class="team-overlay absolute inset-0 bg-deep-blue/80 opacity-0 flex items-center justify-center gap-3">
                        <a href="#" class="w-9 h-9 glass rounded-lg flex items-center justify-center text-white hover:text-electric-blue transition-colors">
                            <i class="fa-brands fa-linkedin-in text-sm"></i>
                        </a>
                        <a href="#" class="w-9 h-9 glass rounded-lg flex items-center justify-center text-white hover:text-electric-blue transition-colors">
                            <i class="fa-brands fa-twitter text-sm"></i>
                        </a>
                        <a href="#" class="w-9 h-9 glass rounded-lg flex items-center justify-center text-white hover:text-electric-blue transition-colors">
                            <i class="fa-solid fa-envelope text-sm"></i>
                        </a>
                    </div>
                </div>
                <!-- Info -->
                <div class="p-5">
                    <h3 class="text-white font-bold text-lg mb-1">{{ $member['name'] }}</h3>
                    <p class="gradient-text text-sm font-semibold mb-3">{{ $member['role'] }}</p>
                    <p class="text-slate-400 text-xs leading-relaxed">{{ $member['bio'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ===== VALUES ===== -->
<section class="py-24 bg-deep-blue">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <span class="text-electric-blue text-sm font-semibold uppercase tracking-widest">What Drives Us</span>
            <h2 class="text-3xl sm:text-4xl font-black text-white mt-3 mb-4">Our Core <span class="gradient-text">Values</span></h2>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
            $values = [
                ['icon'=>'fa-heart','color'=>'text-red-400 bg-red-500/10 border-red-500/20','title'=>'Customer First','desc'=>'Every decision we make starts with one question: how does this help our customers succeed?'],
                ['icon'=>'fa-lightbulb','color'=>'text-yellow-400 bg-yellow-500/10 border-yellow-500/20','title'=>'Innovation','desc'=>'We constantly push boundaries, embrace new technologies, and challenge the status quo.'],
                ['icon'=>'fa-shield-halved','color'=>'text-blue-400 bg-blue-500/10 border-blue-500/20','title'=>'Trust & Security','desc'=>'We treat your data with the highest level of care. Enterprise-grade security is non-negotiable.'],
                ['icon'=>'fa-handshake','color'=>'text-green-400 bg-green-500/10 border-green-500/20','title'=>'Transparency','desc'=>'Honest pricing, clear communication, and no hidden surprises. We say what we mean.'],
                ['icon'=>'fa-bolt','color'=>'text-cyan-400 bg-cyan-500/10 border-cyan-500/20','title'=>'Speed & Reliability','desc'=>'99.9% uptime SLA. We know your business never stops, so neither do we.'],
                ['icon'=>'fa-globe','color'=>'text-purple-400 bg-purple-500/10 border-purple-500/20','title'=>'Global Mindset','desc'=>'Built for businesses worldwide with multi-language, multi-currency, and multi-timezone support.'],
            ];
            @endphp

            @foreach($values as $i => $value)
            <div class="value-card glass rounded-2xl p-6 border border-slate-700/50 hover:border-electric-blue/30 cursor-default"
                 data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                <div class="w-12 h-12 rounded-xl border {{ $value['color'] }} flex items-center justify-center mb-4">
                    <i class="fa-solid {{ $value['icon'] }} text-lg"></i>
                </div>
                <h3 class="text-white font-bold text-lg mb-2">{{ $value['title'] }}</h3>
                <p class="text-slate-400 text-sm leading-relaxed">{{ $value['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ===== JOIN US CTA ===== -->
<section class="py-20 bg-slate-900/40">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center" data-aos="fade-up">
        <div class="glass rounded-3xl p-12 border border-electric-blue/20 relative overflow-hidden">
            <div class="absolute inset-0 hero-glow pointer-events-none"></div>
            <div class="relative">
                <div class="w-16 h-16 rounded-2xl btn-primary-gradient flex items-center justify-center mx-auto mb-6 shadow-xl shadow-electric-blue/30">
                    <i class="fa-solid fa-users text-white text-2xl"></i>
                </div>
                <h2 class="text-3xl sm:text-4xl font-black text-white mb-4">
                    Join the <span class="gradient-text">NexaERP Family</span>
                </h2>
                <p class="text-slate-400 text-lg mb-8 max-w-xl mx-auto">
                    Whether you're a 5-person startup or a 500-person enterprise, NexaERP scales with you. Start your journey today.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('landlord.pricing') }}"
                       class="btn-primary-gradient text-white font-bold px-8 py-4 rounded-xl shadow-xl flex items-center justify-center gap-2">
                        <i class="fa-solid fa-rocket"></i>
                        Start Free Trial
                    </a>
                    <a href="{{ route('landlord.contact') }}"
                       class="glass border border-slate-600 hover:border-electric-blue/50 text-white font-semibold px-8 py-4 rounded-xl transition-all duration-300 flex items-center justify-center gap-2 hover:bg-white/5">
                        <i class="fa-solid fa-message text-electric-blue"></i>
                        Talk to Sales
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
