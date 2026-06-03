@extends('landlord::components.layouts.master')

@section('title', 'Subscribe — ' . ($package->name ?? 'Choose Your Plan') . ' | NexaERP')

@section('meta')
<meta name="description" content="Subscribe to NexaERP and get your business ERP system up and running in minutes.">
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
    .form-input::placeholder { color: rgba(148,163,184,0.5); }
    .step-indicator.active { background: linear-gradient(135deg, #3B82F6, #06B6D4); color: white; }
    .step-indicator.done { background: #22C55E; color: white; }
    .step-indicator.pending { background: rgba(255,255,255,0.08); color: #64748B; }
    .step-line.done { background: linear-gradient(90deg, #3B82F6, #06B6D4); }
    .step-line.pending { background: rgba(255,255,255,0.1); }
    .order-card { background: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(6,182,212,0.05)); }
    .subdomain-preview { background: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.2); }
</style>
@endsection

@section('content')

<!-- ===== PAGE HERO ===== -->
<section class="relative pt-32 pb-12 gradient-bg overflow-hidden">
    <div class="absolute inset-0 hero-glow pointer-events-none"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div data-aos="fade-up">
            <div class="inline-flex items-center gap-2 glass rounded-full px-4 py-2 mb-4 border border-electric-blue/20">
                <i class="fa-solid fa-rocket text-electric-blue text-xs"></i>
                <span class="text-slate-300 text-xs font-medium">You're one step away from your ERP</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-black text-white mb-3">
                Subscribe to <span class="gradient-text">{{ $package->name ?? 'Professional' }}</span> Plan
            </h1>
            <p class="text-slate-400 text-base max-w-xl mx-auto">
                Complete the form below to create your dedicated ERP environment.
            </p>
        </div>
    </div>
</section>

<!-- ===== MAIN CONTENT ===== -->
<section class="py-12 bg-slate-900/40">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div
            x-data="{
                step: 1,
                totalSteps: 3,
                loading: false,
                subdomain: '',
                companyName: '',
                adminName: '',
                adminEmail: '',
                subdomainStatus: null,
                checkSubdomain() {
                    if (this.subdomain.length >= 3) {
                        this.subdomainStatus = 'checking';
                        setTimeout(() => {
                            this.subdomainStatus = this.subdomain.length > 3 ? 'available' : 'taken';
                        }, 800);
                    } else {
                        this.subdomainStatus = null;
                    }
                },
                nextStep() { if (this.step < this.totalSteps) this.step++; },
                prevStep() { if (this.step > 1) this.step--; },
            }"
        >
            <div class="grid lg:grid-cols-3 gap-8 items-start">

                <!-- LEFT: Multi-step Form -->
                <div class="lg:col-span-2">

                    <!-- Step Indicators -->
                    <div class="flex items-center mb-8" data-aos="fade-right">
                        @php $steps = ['Company Info', 'Admin Account', 'Confirm']; @endphp
                        @foreach($steps as $i => $stepLabel)
                        <div class="flex items-center {{ $i < count($steps)-1 ? 'flex-1' : '' }}">
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300"
                                     :class="step > {{ $i+1 }} ? 'step-indicator done' : (step === {{ $i+1 }} ? 'step-indicator active' : 'step-indicator pending')">
                                    <span x-show="step <= {{ $i+1 }}">{{ $i+1 }}</span>
                                    <i x-show="step > {{ $i+1 }}" class="fa-solid fa-check text-xs"></i>
                                </div>
                                <span class="text-xs mt-1.5 font-medium transition-colors duration-300 whitespace-nowrap"
                                      :class="step === {{ $i+1 }} ? 'text-white' : (step > {{ $i+1 }} ? 'text-green-400' : 'text-slate-600')">
                                    {{ $stepLabel }}
                                </span>
                            </div>
                            @if($i < count($steps)-1)
                            <div class="flex-1 h-0.5 mx-3 mb-5 rounded-full transition-all duration-500"
                                 :class="step > {{ $i+1 }} ? 'step-line done' : 'step-line pending'"></div>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <!-- Form Card -->
                    <div class="glass rounded-2xl border border-slate-700/50 overflow-hidden" data-aos="fade-right" data-aos-delay="100">
                        <form action="{{ route('landlord.subscribe.checkout', $package->id ?? 1) }}" method="POST" id="subscribeForm">
                            @csrf

                            <!-- ===== STEP 1: Company Info ===== -->
                            <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                                <div class="p-8">
                                    <div class="flex items-center gap-3 mb-6">
                                        <div class="w-10 h-10 rounded-xl btn-primary-gradient flex items-center justify-center">
                                            <i class="fa-solid fa-building text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <h2 class="text-white font-black text-xl">Company Information</h2>
                                            <p class="text-slate-400 text-xs">Tell us about your business</p>
                                        </div>
                                    </div>

                                    <div class="space-y-5">
                                        <!-- Company Name -->
                                        <div>
                                            <label class="block text-slate-300 text-sm font-medium mb-2">
                                                Company Name <span class="text-red-400">*</span>
                                            </label>
                                            <input type="text" name="company_name" x-model="companyName"
                                                   placeholder="Acme Corporation" required
                                                   class="form-input w-full px-4 py-3 rounded-xl text-sm">
                                            @error('company_name')
                                            <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Subdomain -->
                                        <div>
                                            <label class="block text-slate-300 text-sm font-medium mb-2">
                                                Subdomain <span class="text-red-400">*</span>
                                                <span class="text-slate-500 font-normal ml-1">(your unique ERP address)</span>
                                            </label>
                                            <div class="relative">
                                                <input type="text" name="subdomain" x-model="subdomain"
                                                       @input.debounce.500ms="checkSubdomain()"
                                                       placeholder="acme" required minlength="3" maxlength="20"
                                                       pattern="[a-zA-Z]+"
                                                       class="form-input w-full px-4 py-3 rounded-xl text-sm pr-32">
                                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm font-medium">.erp.test</span>
                                            </div>

                                            <!-- Subdomain Preview -->
                                            <div x-show="subdomain.length >= 3" x-transition class="mt-2 subdomain-preview rounded-lg px-4 py-2.5 flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    <i class="fa-solid fa-globe text-electric-blue text-xs"></i>
                                                    <span class="text-slate-300 text-sm">
                                                        Your URL: <span class="text-white font-semibold" x-text="subdomain + '.erp.test'"></span>
                                                    </span>
                                                </div>
                                                <div>
                                                    <span x-show="subdomainStatus === 'checking'" class="text-slate-400 text-xs flex items-center gap-1">
                                                        <i class="fa-solid fa-spinner fa-spin text-xs"></i> Checking...
                                                    </span>
                                                    <span x-show="subdomainStatus === 'available'" class="text-green-400 text-xs flex items-center gap-1">
                                                        <i class="fa-solid fa-check-circle"></i> Available!
                                                    </span>
                                                    <span x-show="subdomainStatus === 'taken'" class="text-red-400 text-xs flex items-center gap-1">
                                                        <i class="fa-solid fa-times-circle"></i> Taken
                                                    </span>
                                                </div>
                                            </div>

                                            @error('subdomain')
                                            <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                                            @enderror
                                            <p class="text-slate-500 text-xs mt-1.5">Only letters allowed. Min 3, max 20 characters.</p>
                                        </div>
                                    </div>

                                    <div class="mt-8 flex justify-end">
                                        <button type="button" @click="nextStep()"
                                                :disabled="!companyName || subdomain.length < 3"
                                                class="btn-primary-gradient text-white font-bold px-8 py-3 rounded-xl flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg">
                                            Next Step
                                            <i class="fa-solid fa-arrow-right text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- ===== STEP 2: Admin Account ===== -->
                            <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                                <div class="p-8">
                                    <div class="flex items-center gap-3 mb-6">
                                        <div class="w-10 h-10 rounded-xl btn-primary-gradient flex items-center justify-center">
                                            <i class="fa-solid fa-user-shield text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <h2 class="text-white font-black text-xl">Admin Account</h2>
                                            <p class="text-slate-400 text-xs">Create your administrator credentials</p>
                                        </div>
                                    </div>

                                    <div class="space-y-5">
                                        <div>
                                            <label class="block text-slate-300 text-sm font-medium mb-2">Full Name <span class="text-red-400">*</span></label>
                                            <input type="text" name="admin_name" x-model="adminName" placeholder="John Smith" required
                                                   class="form-input w-full px-4 py-3 rounded-xl text-sm">
                                            @error('admin_name')
                                            <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="block text-slate-300 text-sm font-medium mb-2">Email Address <span class="text-red-400">*</span></label>
                                            <input type="email" name="admin_email" x-model="adminEmail" placeholder="admin@company.com" required
                                                   class="form-input w-full px-4 py-3 rounded-xl text-sm">
                                            @error('admin_email')
                                            <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="block text-slate-300 text-sm font-medium mb-2">Password <span class="text-red-400">*</span></label>
                                            <input type="password" name="admin_password" placeholder="Min. 6 characters" required minlength="6"
                                                   class="form-input w-full px-4 py-3 rounded-xl text-sm">
                                            @error('admin_password')
                                            <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="block text-slate-300 text-sm font-medium mb-2">Confirm Password <span class="text-red-400">*</span></label>
                                            <input type="password" name="password_confirmation" placeholder="Repeat your password" required
                                                   class="form-input w-full px-4 py-3 rounded-xl text-sm">
                                        </div>
                                    </div>

                                    <div class="mt-8 flex justify-between">
                                        <button type="button" @click="prevStep()"
                                                class="glass border border-slate-600 hover:border-electric-blue/50 text-white font-semibold px-6 py-3 rounded-xl flex items-center gap-2 transition-all hover:bg-white/5">
                                            <i class="fa-solid fa-arrow-left text-sm"></i>
                                            Back
                                        </button>
                                        <button type="button" @click="nextStep()"
                                                :disabled="!adminName || !adminEmail"
                                                class="btn-primary-gradient text-white font-bold px-8 py-3 rounded-xl flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg">
                                            Review Order
                                            <i class="fa-solid fa-arrow-right text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- ===== STEP 3: Confirm ===== -->
                            <div x-show="step === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                                <div class="p-8">
                                    <div class="flex items-center gap-3 mb-6">
                                        <div class="w-10 h-10 rounded-xl bg-green-500/20 border border-green-500/30 flex items-center justify-center">
                                            <i class="fa-solid fa-clipboard-check text-green-400 text-sm"></i>
                                        </div>
                                        <div>
                                            <h2 class="text-white font-black text-xl">Confirm & Launch</h2>
                                            <p class="text-slate-400 text-xs">Review your details before creating your ERP</p>
                                        </div>
                                    </div>

                                    <!-- Summary -->
                                    <div class="space-y-3 mb-6">
                                        <div class="glass rounded-xl p-4 border border-slate-700/50">
                                            <p class="text-slate-500 text-xs mb-1">Company</p>
                                            <p class="text-white font-semibold" x-text="companyName || '—'"></p>
                                        </div>
                                        <div class="glass rounded-xl p-4 border border-slate-700/50">
                                            <p class="text-slate-500 text-xs mb-1">Your ERP URL</p>
                                            <p class="gradient-text font-bold" x-text="(subdomain || 'yourname') + '.erp.test'"></p>
                                        </div>
                                        <div class="glass rounded-xl p-4 border border-slate-700/50">
                                            <p class="text-slate-500 text-xs mb-1">Admin Account</p>
                                            <p class="text-white font-semibold" x-text="adminName || '—'"></p>
                                            <p class="text-slate-400 text-sm" x-text="adminEmail || '—'"></p>
                                        </div>
                                    </div>

                                    <!-- Terms -->
                                    <div class="flex items-start gap-3 mb-6 p-4 glass rounded-xl border border-slate-700/50">
                                        <input type="checkbox" id="terms" required class="mt-0.5 w-4 h-4 rounded border-slate-600 bg-slate-800 text-electric-blue cursor-pointer">
                                        <label for="terms" class="text-slate-400 text-sm cursor-pointer">
                                            I agree to the <a href="#" class="text-electric-blue hover:underline">Terms of Service</a> and <a href="#" class="text-electric-blue hover:underline">Privacy Policy</a>
                                        </label>
                                    </div>

                                    <div class="flex justify-between">
                                        <button type="button" @click="prevStep()"
                                                class="glass border border-slate-600 hover:border-electric-blue/50 text-white font-semibold px-6 py-3 rounded-xl flex items-center gap-2 transition-all hover:bg-white/5">
                                            <i class="fa-solid fa-arrow-left text-sm"></i>
                                            Back
                                        </button>
                                        <button type="submit" @click="loading = true"
                                                :class="loading ? 'opacity-80 cursor-wait' : ''"
                                                class="btn-primary-gradient text-white font-bold px-8 py-3 rounded-xl flex items-center gap-2 shadow-xl shadow-electric-blue/20">
                                            <span x-show="!loading" class="flex items-center gap-2">
                                                <i class="fa-solid fa-rocket"></i>
                                                Launch My ERP
                                            </span>
                                            <span x-show="loading" class="flex items-center gap-2">
                                                <i class="fa-solid fa-spinner fa-spin"></i>
                                                Creating your ERP...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- RIGHT: Order Summary -->
                <div class="lg:col-span-1" data-aos="fade-left">
                    <div class="order-card glass rounded-2xl border border-electric-blue/20 p-6 sticky top-24">
                        <h3 class="text-white font-black text-lg mb-1">Order Summary</h3>
                        <p class="text-slate-400 text-xs mb-5">Your selected plan details</p>

                        <!-- Plan Badge -->
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-electric-blue/10 border border-electric-blue/20 mb-5">
                            <div class="w-10 h-10 rounded-xl btn-primary-gradient flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-rocket text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="text-white font-bold">{{ $package->name ?? 'Professional' }} Plan</p>
                                <p class="text-slate-400 text-xs">Monthly billing</p>
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="flex items-end justify-between mb-5 pb-5 border-b border-slate-700/50">
                            <span class="text-slate-400 text-sm">Monthly Total</span>
                            <div class="text-right">
                                <span class="text-3xl font-black gradient-text">${{ $package->price ?? '79' }}</span>
                                <span class="text-slate-400 text-sm">/mo</span>
                            </div>
                        </div>

                        <!-- Features List -->
                        <div class="space-y-2.5 mb-5">
                            @php
                            $planFeatures = $package->features ?? ['Up to 50 Users','Full HR Suite','Advanced Inventory','POS System','CRM Module','Payroll','Full Accounting','Priority Support','50GB Storage'];
                            if(is_string($planFeatures)) $planFeatures = json_decode($planFeatures, true) ?? [];
                            @endphp
                            @foreach(array_slice((array)$planFeatures, 0, 8) as $feat)
                            <div class="flex items-center gap-2.5">
                                <div class="w-4 h-4 rounded-full bg-electric-blue/10 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-check text-electric-blue" style="font-size:8px"></i>
                                </div>
                                <span class="text-slate-300 text-xs">{{ $feat }}</span>
                            </div>
                            @endforeach
                        </div>

                        <!-- Guarantees -->
                        <div class="space-y-2 pt-4 border-t border-slate-700/50">
                            <div class="flex items-center gap-2 text-xs text-slate-400">
                                <i class="fa-solid fa-shield-halved text-green-400"></i>
                                30-day money-back guarantee
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-400">
                                <i class="fa-solid fa-lock text-electric-blue"></i>
                                Secure SSL encrypted checkout
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-400">
                                <i class="fa-solid fa-bolt text-yellow-400"></i>
                                Instant account provisioning
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

@endsection
