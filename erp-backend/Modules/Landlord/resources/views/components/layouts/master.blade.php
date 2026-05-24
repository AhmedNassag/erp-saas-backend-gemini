<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'NexaERP — The Future of Business Management')</title>
    @yield('meta')

    <!-- Google Fonts: Inter + Cairo -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'deep-blue': '#0F172A',
                        'electric-blue': '#3B82F6',
                        'cyan-brand': '#06B6D4',
                    },
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                        'cairo': ['Cairo', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- AOS — Animate On Scroll -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        * { font-family: 'Inter', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .glass-dark {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(59, 130, 246, 0.2);
        }
        .gradient-text {
            background: linear-gradient(135deg, #3B82F6, #06B6D4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #0F172A 0%, #1E3A5F 50%, #0F172A 100%);
        }
        .btn-primary-gradient {
            background: linear-gradient(135deg, #3B82F6, #06B6D4);
            transition: all 0.3s ease;
        }
        .btn-primary-gradient:hover {
            background: linear-gradient(135deg, #2563EB, #0891B2);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.4);
        }
        .nav-link-hover {
            position: relative;
        }
        .nav-link-hover::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #3B82F6, #06B6D4);
            transition: width 0.3s ease;
        }
        .nav-link-hover:hover::after { width: 100%; }
        html { scroll-behavior: smooth; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0F172A; }
        ::-webkit-scrollbar-thumb { background: #3B82F6; border-radius: 3px; }
    </style>

    @yield('styles')
</head>
<body class="bg-deep-blue text-white antialiased" x-data>

    <!-- ===== NAVBAR ===== -->
    <nav
        x-data="{ open: false, scrolled: false }"
        x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 })"
        :class="scrolled ? 'glass-dark shadow-2xl' : 'bg-transparent'"
        class="fixed top-0 left-0 right-0 z-50 transition-all duration-500"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-20">

                <!-- Logo -->
                <a href="{{ route('landlord.home') }}" class="flex items-center gap-3 group">
                    <div class="w-9 h-9 rounded-xl btn-primary-gradient flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-bolt text-white text-sm"></i>
                    </div>
                    <span class="text-xl font-bold">
                        <span class="gradient-text">Nexa</span><span class="text-white">ERP</span>
                    </span>
                </a>

                <!-- Desktop Nav Links -->
                <div class="hidden lg:flex items-center gap-8">
                    <a href="{{ route('landlord.home') }}" class="nav-link-hover text-slate-300 hover:text-white text-sm font-medium transition-colors duration-200">Home</a>
                    <a href="{{ route('landlord.about') }}" class="nav-link-hover text-slate-300 hover:text-white text-sm font-medium transition-colors duration-200">About</a>
                    <a href="{{ route('landlord.pricing') }}" class="nav-link-hover text-slate-300 hover:text-white text-sm font-medium transition-colors duration-200">Pricing</a>
                    <a href="{{ route('landlord.contact') }}" class="nav-link-hover text-slate-300 hover:text-white text-sm font-medium transition-colors duration-200">Contact</a>
                </div>

                <!-- CTA Button -->
                <div class="hidden lg:flex items-center gap-4">
                    <a href="{{ route('landlord.pricing') }}"
                       class="btn-primary-gradient text-white text-sm font-semibold px-6 py-2.5 rounded-xl shadow-lg">
                        Start Free Trial
                    </a>
                </div>

                <!-- Mobile Menu Toggle -->
                <button @click="open = !open" class="lg:hidden text-slate-300 hover:text-white p-2 rounded-lg transition-colors">
                    <i x-show="!open" class="fa-solid fa-bars text-xl"></i>
                    <i x-show="open" class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="open" x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="lg:hidden glass-dark border-t border-slate-700/50">
            <div class="px-4 py-4 space-y-2">
                <a href="{{ route('landlord.home') }}" class="block px-4 py-2.5 text-slate-300 hover:text-white hover:bg-white/5 rounded-lg transition-all">Home</a>
                <a href="{{ route('landlord.about') }}" class="block px-4 py-2.5 text-slate-300 hover:text-white hover:bg-white/5 rounded-lg transition-all">About</a>
                <a href="{{ route('landlord.pricing') }}" class="block px-4 py-2.5 text-slate-300 hover:text-white hover:bg-white/5 rounded-lg transition-all">Pricing</a>
                <a href="{{ route('landlord.contact') }}" class="block px-4 py-2.5 text-slate-300 hover:text-white hover:bg-white/5 rounded-lg transition-all">Contact</a>
                <div class="pt-2">
                    <a href="{{ route('landlord.pricing') }}" class="block btn-primary-gradient text-white text-center font-semibold px-6 py-3 rounded-xl">
                        Start Free Trial
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- ===== MAIN CONTENT ===== -->
    <main>
        @yield('content')
    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="bg-slate-900 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">

                <!-- Col 1: Logo + Description -->
                <div class="lg:col-span-1">
                    <a href="{{ route('landlord.home') }}" class="flex items-center gap-3 mb-4">
                        <div class="w-9 h-9 rounded-xl btn-primary-gradient flex items-center justify-center">
                            <i class="fa-solid fa-bolt text-white text-sm"></i>
                        </div>
                        <span class="text-xl font-bold">
                            <span class="gradient-text">Nexa</span><span class="text-white">ERP</span>
                        </span>
                    </a>
                    <p class="text-slate-400 text-sm leading-relaxed mb-5">
                        The future of business management. Streamline operations, boost productivity, and scale with confidence.
                    </p>
                    <div class="flex gap-3">
                        <a href="#" class="w-9 h-9 glass rounded-lg flex items-center justify-center text-slate-400 hover:text-electric-blue hover:border-electric-blue/50 transition-all duration-200">
                            <i class="fa-brands fa-twitter text-sm"></i>
                        </a>
                        <a href="#" class="w-9 h-9 glass rounded-lg flex items-center justify-center text-slate-400 hover:text-electric-blue hover:border-electric-blue/50 transition-all duration-200">
                            <i class="fa-brands fa-linkedin-in text-sm"></i>
                        </a>
                        <a href="#" class="w-9 h-9 glass rounded-lg flex items-center justify-center text-slate-400 hover:text-electric-blue hover:border-electric-blue/50 transition-all duration-200">
                            <i class="fa-brands fa-github text-sm"></i>
                        </a>
                        <a href="#" class="w-9 h-9 glass rounded-lg flex items-center justify-center text-slate-400 hover:text-electric-blue hover:border-electric-blue/50 transition-all duration-200">
                            <i class="fa-brands fa-youtube text-sm"></i>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Product Links -->
                <div>
                    <h4 class="text-white font-semibold mb-5 text-sm uppercase tracking-wider">Product</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ route('landlord.pricing') }}" class="text-slate-400 hover:text-white text-sm transition-colors duration-200 flex items-center gap-2"><i class="fa-solid fa-chevron-right text-xs text-electric-blue"></i>Pricing</a></li>
                        <li><a href="{{ route('landlord.home') }}#features" class="text-slate-400 hover:text-white text-sm transition-colors duration-200 flex items-center gap-2"><i class="fa-solid fa-chevron-right text-xs text-electric-blue"></i>Features</a></li>
                        <li><a href="{{ route('landlord.home') }}#demo" class="text-slate-400 hover:text-white text-sm transition-colors duration-200 flex items-center gap-2"><i class="fa-solid fa-chevron-right text-xs text-electric-blue"></i>Demo</a></li>
                        <li><a href="{{ route('landlord.home') }}#how-it-works" class="text-slate-400 hover:text-white text-sm transition-colors duration-200 flex items-center gap-2"><i class="fa-solid fa-chevron-right text-xs text-electric-blue"></i>How It Works</a></li>
                        <li><a href="{{ route('landlord.home') }}#testimonials" class="text-slate-400 hover:text-white text-sm transition-colors duration-200 flex items-center gap-2"><i class="fa-solid fa-chevron-right text-xs text-electric-blue"></i>Testimonials</a></li>
                    </ul>
                </div>

                <!-- Col 3: Company Links -->
                <div>
                    <h4 class="text-white font-semibold mb-5 text-sm uppercase tracking-wider">Company</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ route('landlord.about') }}" class="text-slate-400 hover:text-white text-sm transition-colors duration-200 flex items-center gap-2"><i class="fa-solid fa-chevron-right text-xs text-electric-blue"></i>About Us</a></li>
                        <li><a href="{{ route('landlord.contact') }}" class="text-slate-400 hover:text-white text-sm transition-colors duration-200 flex items-center gap-2"><i class="fa-solid fa-chevron-right text-xs text-electric-blue"></i>Contact</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white text-sm transition-colors duration-200 flex items-center gap-2"><i class="fa-solid fa-chevron-right text-xs text-electric-blue"></i>Blog</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white text-sm transition-colors duration-200 flex items-center gap-2"><i class="fa-solid fa-chevron-right text-xs text-electric-blue"></i>Careers</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white text-sm transition-colors duration-200 flex items-center gap-2"><i class="fa-solid fa-chevron-right text-xs text-electric-blue"></i>Privacy Policy</a></li>
                    </ul>
                </div>

                <!-- Col 4: Contact Info -->
                <div>
                    <h4 class="text-white font-semibold mb-5 text-sm uppercase tracking-wider">Contact</h4>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-electric-blue/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-envelope text-electric-blue text-xs"></i>
                            </div>
                            <div>
                                <p class="text-slate-500 text-xs mb-0.5">Email</p>
                                <a href="mailto:hello@nexaerp.com" class="text-slate-300 hover:text-white text-sm transition-colors">hello@nexaerp.com</a>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-electric-blue/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-phone text-electric-blue text-xs"></i>
                            </div>
                            <div>
                                <p class="text-slate-500 text-xs mb-0.5">Phone</p>
                                <a href="tel:+1234567890" class="text-slate-300 hover:text-white text-sm transition-colors">+1 (234) 567-890</a>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-electric-blue/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-location-dot text-electric-blue text-xs"></i>
                            </div>
                            <div>
                                <p class="text-slate-500 text-xs mb-0.5">Address</p>
                                <p class="text-slate-300 text-sm">123 Tech Park, Silicon Valley, CA 94025</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="mt-12 pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-slate-500 text-sm">
                    &copy; {{ date('Y') }} <span class="gradient-text font-semibold">NexaERP</span>. All rights reserved.
                </p>
                <div class="flex items-center gap-6">
                    <a href="#" class="text-slate-500 hover:text-slate-300 text-xs transition-colors">Terms of Service</a>
                    <a href="#" class="text-slate-500 hover:text-slate-300 text-xs transition-colors">Privacy Policy</a>
                    <a href="#" class="text-slate-500 hover:text-slate-300 text-xs transition-colors">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 700,
            easing: 'ease-out-cubic',
            once: true,
            offset: 60,
        });
    </script>

    @yield('scripts')
</body>
</html>
