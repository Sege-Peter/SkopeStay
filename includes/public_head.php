    <!-- Meta & SEO Essentials -->
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0"/>
    
    <!-- Premium Google Fonts: Playfair Display, Montserrat, Poppins, Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,400;1,600&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Material Icons & Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Tailwind CSS (CDN) -->
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
                        cream: '#FDFBF7',
                        lightgray: '#F8FAFC',
                        surface: 'rgba(255, 255, 255, 0.07)',
                        'surface-dark': 'rgba(11, 19, 43, 0.88)'
                    },
                    fontFamily: {
                        display: ['Playfair Display', 'serif'],
                        garamond: ['Cormorant Garamond', 'serif'],
                        poppins: ['Poppins', 'sans-serif'],
                        inter: ['Inter', 'sans-serif'],
                        montserrat: ['Montserrat', 'sans-serif']
                    },
                    boxShadow: {
                        'luxury': '0 20px 40px -15px rgba(11, 19, 43, 0.15)',
                        'gold-glow': '0 0 25px rgba(212, 175, 55, 0.35)',
                        'gold-sm': '0 4px 14px rgba(212, 175, 55, 0.25)',
                        'glass': '0 8px 32px 0 rgba(0, 0, 0, 0.15)'
                    },
                    animation: {
                        'slow-zoom': 'slowZoom 24s ease-in-out infinite alternate',
                        'ripple': 'ripple 5s linear infinite',
                        'float': 'float 4s ease-in-out infinite',
                        'float-delayed': 'float 4s ease-in-out 2s infinite',
                        'shimmer': 'shimmer 2.5s infinite',
                        'pulse-glow': 'pulseGlow 2s ease-in-out infinite'
                    },
                    keyframes: {
                        slowZoom: {
                            '0%': { transform: 'scale(1)' },
                            '100%': { transform: 'scale(1.12)' }
                        },
                        ripple: {
                            '0%': { transform: 'scale(0.85)', opacity: '0.6' },
                            '100%': { transform: 'scale(2.6)', opacity: '0' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-8px)' }
                        },
                        shimmer: {
                            '0%': { backgroundPosition: '-200% 0' },
                            '100%': { backgroundPosition: '200% 0' }
                        },
                        pulseGlow: {
                            '0%, 100%': { opacity: '0.4', transform: 'scale(1)' },
                            '50%': { opacity: '0.8', transform: 'scale(1.05)' }
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Modern Base & Smooth Rendering */
        html {
            scroll-behavior: smooth;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #F8FAFC; 
            color: #0B132B;
            overflow-x: hidden;
            width: 100%;
            padding-bottom: 74px; /* Space for mobile bottom bar */
        }
        @media (min-width: 768px) {
            body { padding-bottom: 0; }
        }

        h1, h2, h3, .font-luxury { 
            font-family: 'Playfair Display', serif; 
            letter-spacing: -0.01em;
        }
        .font-brand { font-family: 'Montserrat', sans-serif; }
        .font-ui { font-family: 'Poppins', sans-serif; }

        /* Luxury Glassmorphism Styles */
        .glass-luxury {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.65);
            box-shadow: 0 10px 30px -10px rgba(11, 19, 43, 0.08);
        }
        .glass-dark-luxury {
            background: rgba(11, 19, 43, 0.85);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.35);
        }

        /* Gold Gradient Text */
        .gold-gradient-text {
            background: linear-gradient(135deg, #F5E6AB 0%, #D4AF37 50%, #A6821C 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .gold-gradient-bg {
            background: linear-gradient(135deg, #F3E5AB 0%, #D4AF37 60%, #B89025 100%);
        }

        /* 3D Dynamic Hover Lift */
        .hover-lift {
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s ease;
        }
        .hover-lift:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 35px -10px rgba(11, 19, 43, 0.18);
        }

        /* Smooth Image Zoom */
        .img-zoom-container { overflow: hidden; }
        .img-zoom-container img { transition: transform 0.7s cubic-bezier(0.16, 1, 0.3, 1); }
        .img-zoom-container:hover img { transform: scale(1.08); }

        /* Custom Modern Scrollbars */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #F1F5F9; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Mega Menu Transition */
        .mega-menu {
            display: none;
            opacity: 0;
            transform: translateY(12px);
            transition: opacity 0.25s ease, transform 0.25s ease;
            pointer-events: none;
        }
        .nav-item:hover .mega-menu, .nav-item:focus-within .mega-menu {
            display: block;
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        /* Mobile Slide Menu */
        #mobile-menu {
            transform: translateX(-100%);
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        #mobile-menu.open {
            transform: translateX(0);
        }
        #mobile-overlay {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        #mobile-overlay.open {
            opacity: 1;
            pointer-events: auto;
        }

        /* Responsive Mobile Bottom Dock */
        .mobile-bottom-dock {
            background: rgba(11, 19, 43, 0.94);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 -8px 25px rgba(0, 0, 0, 0.2);
        }

        /* Pool Parallax Overlay */
        .pool-overlay {
            background: radial-gradient(circle at 60% 50%, transparent 20%, rgba(11, 19, 43, 0.85) 90%);
        }
    </style>
