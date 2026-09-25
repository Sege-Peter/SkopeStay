<?php
require_once __DIR__ . '/../includes/config.php';

// If already authenticated, redirect straight to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: ../modules/dashboard.php");
    exit;
}
$page_title = "Sign In | SkopeStay Luxury Resort & ERP";
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= htmlspecialchars($page_title) ?></title>

    <!-- Google Fonts: Playfair Display, Montserrat, Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Tailwind CSS with Luxury Theme Tokens -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            DEFAULT: '#0B132B',
                            dark: '#050A18',
                            light: '#1C2541',
                            subtle: '#243257'
                        },
                        gold: {
                            DEFAULT: '#D4AF37',
                            light: '#F5E6AB',
                            dark: '#A6821C',
                            accent: '#F59E0B'
                        },
                        cream: '#FDFBF7'
                    },
                    fontFamily: {
                        display: ['Playfair Display', 'serif'],
                        brand: ['Montserrat', 'sans-serif'],
                        ui: ['Poppins', 'sans-serif'],
                        sans: ['Inter', 'sans-serif']
                    },
                    boxShadow: {
                        'luxury': '0 25px 50px -12px rgba(11, 19, 43, 0.25)',
                        'gold-glow': '0 0 30px rgba(212, 175, 55, 0.35)',
                        'gold-sm': '0 4px 15px rgba(212, 175, 55, 0.25)'
                    },
                    animation: {
                        'slow-zoom': 'slowZoom 28s ease-in-out infinite alternate',
                        'pulse-glow': 'pulseGlow 2.5s ease-in-out infinite'
                    },
                    keyframes: {
                        slowZoom: {
                            '0%': { transform: 'scale(1)' },
                            '100%': { transform: 'scale(1.10)' }
                        },
                        pulseGlow: {
                            '0%, 100%': { opacity: '0.4', transform: 'scale(1)' },
                            '50%': { opacity: '0.8', transform: 'scale(1.03)' }
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0B132B;
        }
        .font-luxury {
            font-family: 'Playfair Display', serif;
        }
        .gold-gradient-text {
            background: linear-gradient(135deg, #F5E6AB 0%, #D4AF37 50%, #A6821C 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .gold-gradient-bg {
            background: linear-gradient(135deg, #F3E5AB 0%, #D4AF37 60%, #B89025 100%);
        }
        .gold-gradient-btn {
            background: linear-gradient(135deg, #D4AF37 0%, #C49826 50%, #B0851A 100%);
            transition: all 0.3s ease;
        }
        .gold-gradient-btn:hover {
            background: linear-gradient(135deg, #F5E6AB 0%, #D4AF37 70%, #A6821C 100%);
            box-shadow: 0 10px 25px -5px rgba(212, 175, 55, 0.45);
        }
        .glass-luxury {
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }
        .glass-dark-badge {
            background: rgba(11, 19, 43, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(212, 175, 55, 0.25);
        }
    </style>
</head>
<body class="h-full flex antialiased text-gray-800 selection:bg-gold/30 selection:text-navy">

    <div class="w-full h-full min-h-screen flex flex-col lg:flex-row overflow-y-auto lg:overflow-hidden">

        <!-- =========================================================================
             LEFT PANEL: CINEMATIC RESORT SHOWCASE (Desktop & Large Screens)
             ========================================================================= -->
        <div class="hidden lg:flex lg:w-7/12 relative flex-col justify-between p-12 xl:p-16 overflow-hidden bg-navy select-none">
            
            <!-- High-Res Luxury Background Imagery -->
            <div class="absolute inset-0 z-0">
                <img src="https://images.unsplash.com/photo-1542314831-c53cd3816002?q=80&w=2070&auto=format&fit=crop" 
                     alt="SkopeStay Luxury Resort Exterior" 
                     class="w-full h-full object-cover animate-slow-zoom opacity-60">
                <!-- Multi-layer cinematic vignette -->
                <div class="absolute inset-0 bg-gradient-to-t from-navy via-navy/60 to-navy/40"></div>
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_bottom_left,rgba(212,175,55,0.15),transparent_70%)]"></div>
            </div>

            <!-- Top Left: Brand Header with Logo -->
            <div class="relative z-10 flex items-center justify-between">
                <a href="../index.php" class="inline-flex items-center gap-3.5 group">
                    <div class="w-12 h-12 rounded-2xl bg-white/95 p-1.5 shadow-xl border border-gold/40 flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
                        <img src="../assets/images/SkopeStay logo.png" alt="SkopeStay Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <div class="font-display font-bold text-2xl text-white tracking-tight leading-none">
                            Skope<span class="text-gold">Stay</span>
                        </div>
                        <div class="text-[10px] font-brand font-semibold text-gray-300 tracking-[0.25em] uppercase mt-1">
                            Resort & Enterprise ERP
                        </div>
                    </div>
                </a>

                <div class="glass-dark-badge px-4 py-1.5 rounded-full flex items-center gap-2 border border-gold/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span class="text-xs font-brand tracking-wider uppercase text-gold font-bold">5-Star Verified</span>
                </div>
            </div>

            <!-- Middle Left: Editorial Luxury Quote & Vision -->
            <div class="relative z-10 max-w-xl my-auto py-12">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-gold text-xs font-brand font-semibold tracking-widest uppercase mb-6 shadow-sm">
                    <span class="material-symbols-outlined text-[16px]">stars</span>
                    Forbes Hospitality Standards
                </div>
                
                <h2 class="text-4xl xl:text-5xl font-luxury font-bold text-white leading-tight mb-6">
                    Where Pure Luxury Meets <span class="gold-gradient-text italic font-normal">Intelligent Operations.</span>
                </h2>
                
                <p class="text-gray-300 text-base xl:text-lg font-light leading-relaxed mb-8">
                    Seamlessly orchestrate guest reservations, banquets, housekeeping turnover, procurement lifecycles, and double-entry financial ledgers through one unified enterprise ecosystem.
                </p>

                <!-- Value Highlights Grid -->
                <div class="grid grid-cols-3 gap-4 pt-4 border-t border-white/15">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gold/15 text-gold flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[20px]">room_service</span>
                        </div>
                        <div>
                            <div class="text-white font-bold text-sm">Real-time</div>
                            <div class="text-gray-400 text-xs">Guest Folios</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gold/15 text-gold flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[20px]">cleaning_services</span>
                        </div>
                        <div>
                            <div class="text-white font-bold text-sm">Smart CMMS</div>
                            <div class="text-gray-400 text-xs">Auto Turnover</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gold/15 text-gold flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[20px]">account_balance</span>
                        </div>
                        <div>
                            <div class="text-white font-bold text-sm">Audited GL</div>
                            <div class="text-gray-400 text-xs">P&L Analytics</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Left: Security Assurance Footer -->
            <div class="relative z-10 flex items-center justify-between text-xs text-gray-400 pt-6 border-t border-white/10 font-sans">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-emerald-400">lock</span>
                    <span>256-Bit TLS Encryption & Role-Based RBAC Protection</span>
                </div>
                <span>SkopeStay Core v2.5.0</span>
            </div>
        </div>

        <!-- =========================================================================
             RIGHT PANEL: MODERN LUXURY LOGIN CARD
             ========================================================================= -->
        <div class="w-full lg:w-5/12 flex flex-col justify-between p-6 sm:p-10 xl:p-14 bg-[#FDFBF7] relative min-h-screen">
            
            <!-- Top Controls (Back Link & Brand Pill) -->
            <div class="flex items-center justify-between mb-8 sm:mb-12">
                <a href="../index.php" class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-gray-500 hover:text-navy transition-all duration-200 group">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center group-hover:bg-navy group-hover:text-gold transition-colors shadow-sm">
                        <span class="material-symbols-outlined text-[18px] group-hover:-translate-x-0.5 transition-transform">arrow_back</span>
                    </div>
                    <span>Back to Website</span>
                </a>

                <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>System Active</span>
                </div>
            </div>

            <!-- Main Authentication Card Form Container -->
            <div class="max-w-md w-full mx-auto my-auto py-4">

                <!-- Logo & Heading Section -->
                <div class="text-center mb-8">
                    <!-- Brand Logo Showcase with Luxury Halo -->
                    <div class="relative inline-block mb-4">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-white shadow-luxury p-3 border border-amber-200/60 flex items-center justify-center mx-auto transition-transform duration-300 hover:scale-105">
                            <img src="../assets/images/SkopeStay logo.png" 
                                 alt="SkopeStay Official Logo" 
                                 class="w-full h-full object-contain filter drop-shadow-sm">
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-navy text-gold flex items-center justify-center shadow-md ring-2 ring-white">
                            <span class="material-symbols-outlined text-[15px]">verified</span>
                        </div>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-luxury font-bold text-navy tracking-tight">
                        Welcome to <span class="text-navy">Skope<span class="text-gold">Stay</span></span>
                    </h1>
                    <p class="text-sm text-gray-500 mt-2 font-sans font-normal">
                        Sign in to access your administrative suite & guest portal
                    </p>
                </div>

                <!-- Quick Demo 1-Click Autofill Pill -->
                <div class="mb-6">
                    <button type="button" 
                            id="demoAdminBtn" 
                            class="w-full py-2.5 px-4 rounded-xl bg-amber-50/80 hover:bg-amber-100 border border-amber-200/80 text-amber-900 text-xs font-semibold flex items-center justify-between transition-all duration-200 shadow-sm group">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-amber-600 group-hover:scale-110 transition-transform">key</span>
                            <span>Quick Demo Fill: <strong class="text-navy">admin</strong> / <strong class="text-navy">admin123</strong></span>
                        </div>
                        <span class="text-[11px] uppercase tracking-wider text-amber-700 bg-white px-2 py-0.5 rounded-md border border-amber-200 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                            Autofill
                        </span>
                    </button>
                </div>

                <!-- Login Form -->
                <form id="loginForm" class="space-y-5" autocomplete="on">
                    
                    <!-- Username or Email Field -->
                    <div>
                        <label for="username" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                            Username or Email Address
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-gold transition-colors">
                                <span class="material-symbols-outlined text-[20px]">person</span>
                            </div>
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   required 
                                   autocomplete="username"
                                   placeholder="e.g. admin or admin@skopestay.com" 
                                   class="w-full pl-11 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-sm font-medium text-navy placeholder-gray-400 focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/20 transition-all shadow-sm">
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                                Password
                            </label>
                            <a href="#" id="forgotPassBtn" class="text-xs font-medium text-gold hover:text-gold-dark hover:underline transition-colors">
                                Forgot password?
                            </a>
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-gold transition-colors">
                                <span class="material-symbols-outlined text-[20px]">lock</span>
                            </div>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password"
                                   placeholder="••••••••••••" 
                                   class="w-full pl-11 pr-12 py-3 bg-white border border-gray-200 rounded-xl text-sm font-medium text-navy placeholder-gray-400 focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/20 transition-all shadow-sm">
                            
                            <!-- Toggle Password Visibility -->
                            <button type="button" 
                                    id="togglePassword" 
                                    aria-label="Toggle password visibility"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-navy transition-colors focus:outline-none">
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me Option -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" 
                                   id="remember" 
                                   name="remember" 
                                   class="w-4 h-4 rounded text-navy border-gray-300 focus:ring-gold/40 focus:ring-offset-0 cursor-pointer">
                            <span class="text-xs text-gray-600 font-medium">Keep me signed in</span>
                        </label>
                    </div>

                    <!-- Submit CTA Button -->
                    <button type="submit" 
                            id="submitBtn" 
                            class="w-full py-3.5 px-6 rounded-xl gold-gradient-btn text-navy font-bold text-sm tracking-wide shadow-gold-sm hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-2 mt-6 cursor-pointer">
                        <span class="material-symbols-outlined text-[20px]">login</span>
                        <span>Sign In to Portal</span>
                    </button>
                </form>

                <!-- Create Account Link -->
                <div class="text-center pt-8 mt-6 border-t border-gray-200/80">
                    <p class="text-xs text-gray-500 font-medium">
                        Don't have an enterprise account? 
                        <a href="signup.php" class="text-navy font-bold hover:text-gold transition-colors inline-flex items-center gap-1">
                            <span>Create an Account</span>
                            <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                        </a>
                    </p>
                </div>
            </div>

            <!-- Footer / Copyright -->
            <div class="text-center pt-8 text-xs text-gray-400 font-sans">
                <p>&copy; <?= date('Y') ?> SkopeStay Luxury Resort & Enterprise Suite. All rights reserved.</p>
                <div class="flex justify-center items-center gap-4 mt-2 text-gray-400 text-[11px]">
                    <a href="../terms.php" class="hover:text-gray-600 transition-colors">Terms of Service</a>
                    <span>•</span>
                    <a href="mailto:support@skopestay.com" class="hover:text-gray-600 transition-colors">IT Support</a>
                    <span>•</span>
                    <a href="../index.php" class="hover:text-gray-600 transition-colors">Guest Portal</a>
                </div>
            </div>
        </div>

    </div>

    <!-- Client-Side Authentication Handler -->
    <script>
        // Password Visibility Toggle
        const toggleBtn = document.getElementById('togglePassword');
        const passInput = document.getElementById('password');
        
        toggleBtn.addEventListener('click', () => {
            const isPassword = passInput.getAttribute('type') === 'password';
            passInput.setAttribute('type', isPassword ? 'text' : 'password');
            toggleBtn.querySelector('span').textContent = isPassword ? 'visibility_off' : 'visibility';
        });

        // Demo Admin 1-Click Autofill
        const demoBtn = document.getElementById('demoAdminBtn');
        demoBtn.addEventListener('click', () => {
            const userInput = document.getElementById('username');
            userInput.value = 'admin';
            passInput.value = 'admin123';
            
            // Visual feedback
            userInput.classList.add('ring-2', 'ring-amber-400', 'bg-amber-50/50');
            passInput.classList.add('ring-2', 'ring-amber-400', 'bg-amber-50/50');
            setTimeout(() => {
                userInput.classList.remove('ring-2', 'ring-amber-400', 'bg-amber-50/50');
                passInput.classList.remove('ring-2', 'ring-amber-400', 'bg-amber-50/50');
            }, 1000);

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1800,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'info',
                title: 'Demo admin credentials loaded'
            });
        });

        // Forgot Password Modal
        document.getElementById('forgotPassBtn').addEventListener('click', (e) => {
            e.preventDefault();
            Swal.fire({
                icon: 'info',
                title: 'Password Recovery',
                html: `
                    <div class="text-left text-sm space-y-2 text-gray-600 mt-2">
                        <p>For security reasons, enterprise passwords must be reset via your Property Administrator or IT Operations.</p>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 font-mono text-xs text-gray-800">
                            <strong>IT Desk:</strong> support@skopestay.com<br>
                            <strong>Ext:</strong> 108 / +254 742 380 183
                        </div>
                    </div>
                `,
                confirmButtonText: 'Understood',
                confirmButtonColor: '#0B132B'
            });
        });

        // Form Submission with Feedback
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            const originalHTML = btn.innerHTML;

            // Loading state
            btn.innerHTML = `
                <span class="material-symbols-outlined animate-spin text-[20px]">sync</span>
                <span>Authenticating...</span>
            `;
            btn.classList.add('opacity-80', 'cursor-wait');
            btn.disabled = true;

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            try {
                const res = await fetch('../api/auth.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const json = await res.json();

                if (json.success) {
                    btn.innerHTML = `
                        <span class="material-symbols-outlined text-[20px]">check_circle</span>
                        <span>Authorized! Redirecting...</span>
                    `;
                    btn.classList.remove('gold-gradient-btn', 'text-navy');
                    btn.classList.add('bg-emerald-600', 'text-white');

                    Swal.fire({
                        icon: 'success',
                        title: 'Welcome Back!',
                        text: 'Authentication successful. Preparing your workspace...',
                        showConfirmButton: false,
                        timer: 1200,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = '../modules/dashboard.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Authentication Failed',
                        text: json.message || 'Invalid username or password.',
                        confirmButtonText: 'Try Again',
                        confirmButtonColor: '#0B132B'
                    });
                    btn.innerHTML = originalHTML;
                    btn.classList.remove('opacity-80', 'cursor-wait');
                    btn.disabled = false;
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Error',
                    text: 'Unable to reach the authentication server. Please check your connection.',
                    confirmButtonText: 'Retry',
                    confirmButtonColor: '#0B132B'
                });
                btn.innerHTML = originalHTML;
                btn.classList.remove('opacity-80', 'cursor-wait');
                btn.disabled = false;
            }
        });
    </script>
</body>
</html>
