@extends('landlord::components.layouts.master')

@section('title', 'Contact NexaERP — Get in Touch')

@section('meta')
<meta name="description" content="Contact NexaERP for sales inquiries, technical support, or partnership opportunities. We're here to help.">
@endsection

@section('styles')
<style>
    .form-input {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.1);
        color: white;
        transition: all 0.3s ease;
    }
    .form-input:focus {
        outline: none;
        border-color: #3B82F6;
        background: rgba(59,130,246,0.05);
        box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
    }
    .form-input::placeholder { color: rgba(148,163,184,0.6); }
    .form-input option { background: #1E293B; color: white; }
    .contact-card:hover { transform: translateY(-4px); border-color: rgba(59,130,246,0.4); }
    .contact-card { transition: all 0.3s ease; }
    .map-placeholder {
        background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
        border: 1px solid rgba(59,130,246,0.2);
    }
</style>
@endsection

@section('content')

<!-- ===== PAGE HERO ===== -->
<section class="relative pt-32 pb-20 gradient-bg overflow-hidden">
    <div class="absolute inset-0 hero-glow pointer-events-none"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div data-aos="fade-up">
            <div class="inline-flex items-center gap-2 glass rounded-full px-4 py-2 mb-6 border border-electric-blue/20">
                <i class="fa-solid fa-message text-electric-blue text-xs"></i>
                <span class="text-slate-300 text-xs font-medium">We'd love to hear from you</span>
            </div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white mb-6">
                Get in <span class="gradient-text">Touch</span>
            </h1>
            <p class="text-slate-400 text-lg max-w-2xl mx-auto mb-8">
                Have a question, need a demo, or ready to get started? Our team is here to help you every step of the way.
            </p>
            <nav class="flex items-center justify-center gap-2 text-sm text-slate-500">
                <a href="{{ route('landlord.home') }}" class="hover:text-white transition-colors">Home</a>
                <i class="fa-solid fa-chevron-right text-xs"></i>
                <span class="text-slate-300">Contact</span>
            </nav>
        </div>
    </div>
</section>

<!-- ===== CONTACT INFO CARDS ===== -->
<section class="py-16 bg-slate-900/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-6">
            @php
            $contacts = [
                ['icon'=>'fa-envelope','color'=>'text-blue-400 bg-blue-500/10 border-blue-500/20','title'=>'Email Us','value'=>'hello@nexaerp.com','sub'=>'We reply within 2 hours','href'=>'mailto:hello@nexaerp.com'],
                ['icon'=>'fa-phone','color'=>'text-green-400 bg-green-500/10 border-green-500/20','title'=>'Call Us','value'=>'+1 (234) 567-890','sub'=>'Mon–Fri, 9am–6pm EST','href'=>'tel:+1234567890'],
                ['icon'=>'fa-location-dot','color'=>'text-purple-400 bg-purple-500/10 border-purple-500/20','title'=>'Visit Us','value'=>'123 Tech Park, Silicon Valley','sub'=>'CA 94025, United States','href'=>'#map'],
            ];
            @endphp

            @foreach($contacts as $i => $c)
            <div class="contact-card glass rounded-2xl p-6 border border-slate-700/50 text-center"
                 data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                <div class="w-14 h-14 rounded-2xl border {{ $c['color'] }} flex items-center justify-center mx-auto mb-5">
                    <i class="fa-solid {{ $c['icon'] }} text-xl"></i>
                </div>
                <h3 class="text-white font-bold text-lg mb-2">{{ $c['title'] }}</h3>
                <a href="{{ $c['href'] }}" class="gradient-text font-semibold text-base hover:opacity-80 transition-opacity block mb-1">{{ $c['value'] }}</a>
                <p class="text-slate-500 text-sm">{{ $c['sub'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ===== CONTACT FORM + MAP ===== -->
<section class="py-16 bg-deep-blue">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-10">

            <!-- Contact Form -->
            <div data-aos="fade-right">
                <div class="glass rounded-2xl p-8 border border-slate-700/50">
                    <h2 class="text-2xl font-black text-white mb-2">Send Us a Message</h2>
                    <p class="text-slate-400 text-sm mb-8">Fill out the form and we'll get back to you within 24 hours.</p>

                    <form action="#" method="POST" x-data="{ submitting: false, submitted: false }" @submit.prevent="submitting = true; setTimeout(() => { submitting = false; submitted = true; }, 1500)">
                        @csrf
                        <div class="space-y-5">
                            <!-- Name + Email -->
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-slate-300 text-sm font-medium mb-2">Full Name <span class="text-red-400">*</span></label>
                                    <input type="text" name="name" placeholder="John Smith" required
                                           class="form-input w-full px-4 py-3 rounded-xl text-sm">
                                </div>
                                <div>
                                    <label class="block text-slate-300 text-sm font-medium mb-2">Email Address <span class="text-red-400">*</span></label>
                                    <input type="email" name="email" placeholder="john@company.com" required
                                           class="form-input w-full px-4 py-3 rounded-xl text-sm">
                                </div>
                            </div>

                            <!-- Company -->
                            <div>
                                <label class="block text-slate-300 text-sm font-medium mb-2">Company Name</label>
                                <input type="text" name="company" placeholder="Your Company Inc."
                                       class="form-input w-full px-4 py-3 rounded-xl text-sm">
                            </div>

                            <!-- Subject -->
                            <div>
                                <label class="block text-slate-300 text-sm font-medium mb-2">Subject <span class="text-red-400">*</span></label>
                                <select name="subject" required class="form-input w-full px-4 py-3 rounded-xl text-sm appearance-none cursor-pointer">
                                    <option value="" disabled selected>Select a subject...</option>
                                    <option value="sales">Sales Inquiry</option>
                                    <option value="demo">Request a Demo</option>
                                    <option value="support">Technical Support</option>
                                    <option value="billing">Billing Question</option>
                                    <option value="partnership">Partnership</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <!-- Message -->
                            <div>
                                <label class="block text-slate-300 text-sm font-medium mb-2">Message <span class="text-red-400">*</span></label>
                                <textarea name="message" rows="5" placeholder="Tell us how we can help you..." required
                                          class="form-input w-full px-4 py-3 rounded-xl text-sm resize-none"></textarea>
                            </div>

                            <!-- Submit -->
                            <div>
                                <button type="submit"
                                        :disabled="submitting || submitted"
                                        class="w-full btn-primary-gradient text-white font-bold py-4 rounded-xl shadow-xl transition-all duration-300 flex items-center justify-center gap-2 disabled:opacity-70">
                                    <span x-show="!submitting && !submitted" class="flex items-center gap-2">
                                        <i class="fa-solid fa-paper-plane"></i>
                                        Send Message
                                    </span>
                                    <span x-show="submitting" class="flex items-center gap-2">
                                        <i class="fa-solid fa-spinner fa-spin"></i>
                                        Sending...
                                    </span>
                                    <span x-show="submitted" class="flex items-center gap-2">
                                        <i class="fa-solid fa-check"></i>
                                        Message Sent!
                                    </span>
                                </button>
                            </div>

                            <!-- Success Message -->
                            <div x-show="submitted" x-transition class="glass rounded-xl p-4 border border-green-500/30 bg-green-500/5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-check text-green-400 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-green-400 font-semibold text-sm">Message sent successfully!</p>
                                        <p class="text-slate-400 text-xs">We'll get back to you within 24 hours.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Map + Info -->
            <div class="space-y-6" data-aos="fade-left">
                <!-- Map Placeholder -->
                <div id="map" class="map-placeholder rounded-2xl overflow-hidden h-72 relative flex items-center justify-center">
                    <div class="absolute inset-0 opacity-20">
                        <!-- Grid lines to simulate map -->
                        <div class="w-full h-full" style="background-image: linear-gradient(rgba(59,130,246,0.3) 1px, transparent 1px), linear-gradient(90deg, rgba(59,130,246,0.3) 1px, transparent 1px); background-size: 40px 40px;"></div>
                    </div>
                    <div class="relative text-center">
                        <div class="w-16 h-16 rounded-full btn-primary-gradient flex items-center justify-center mx-auto mb-3 shadow-xl shadow-electric-blue/30">
                            <i class="fa-solid fa-location-dot text-white text-2xl"></i>
                        </div>
                        <p class="text-white font-semibold">123 Tech Park</p>
                        <p class="text-slate-400 text-sm">Silicon Valley, CA 94025</p>
                        <a href="#" class="mt-3 inline-flex items-center gap-2 text-electric-blue text-sm hover:underline">
                            <i class="fa-solid fa-map text-xs"></i>
                            Open in Google Maps
                        </a>
                    </div>
                </div>

                <!-- Office Hours -->
                <div class="glass rounded-2xl p-6 border border-slate-700/50">
                    <h3 class="text-white font-bold text-lg mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-clock text-electric-blue"></i>
                        Office Hours
                    </h3>
                    <div class="space-y-3">
                        @php
                        $hours = [
                            ['day'=>'Monday – Friday','time'=>'9:00 AM – 6:00 PM EST','active'=>true],
                            ['day'=>'Saturday','time'=>'10:00 AM – 2:00 PM EST','active'=>true],
                            ['day'=>'Sunday','time'=>'Closed','active'=>false],
                        ];
                        @endphp
                        @foreach($hours as $h)
                        <div class="flex items-center justify-between py-2 border-b border-slate-700/30 last:border-0">
                            <span class="text-slate-400 text-sm">{{ $h['day'] }}</span>
                            <span class="{{ $h['active'] ? 'text-white' : 'text-slate-600' }} text-sm font-medium">{{ $h['time'] }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4 flex items-center gap-2 text-sm">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-green-400 font-medium">Support available 24/7 via chat</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== FAQ ===== -->
<section class="py-20 bg-slate-900/40">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="text-electric-blue text-sm font-semibold uppercase tracking-widest">FAQ</span>
            <h2 class="text-3xl sm:text-4xl font-black text-white mt-3 mb-4">Frequently Asked <span class="gradient-text">Questions</span></h2>
        </div>

        <div class="space-y-3" x-data="{ open: null }">
            @php
            $faqs = [
                ['q'=>'How quickly can I get started with NexaERP?','a'=>'You can be up and running in under 10 minutes. Sign up, choose your plan, and your dedicated ERP environment is provisioned instantly. No installation, no IT team required.'],
                ['q'=>'Do you offer a free trial?','a'=>'Yes! We offer a 14-day free trial with full access to all features. No credit card required. You can upgrade, downgrade, or cancel at any time during or after the trial.'],
                ['q'=>'Can I migrate data from my existing system?','a'=>'Absolutely. We provide data migration tools and dedicated support to help you import your existing data from Excel, CSV, or other ERP systems. Our team will guide you through the entire process.'],
                ['q'=>'Is my data secure?','a'=>'Security is our top priority. We use AES-256 encryption, SOC 2 Type II compliance, daily backups, and each client gets their own isolated database. Your data is never shared with other tenants.'],
                ['q'=>'What kind of support do you offer?','a'=>'All plans include email support. Professional and Enterprise plans include priority support with guaranteed response times. Enterprise clients get a dedicated account manager and 24/7 phone support.'],
            ];
            @endphp

            @foreach($faqs as $i => $faq)
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
    </div>
</section>

@endsection
