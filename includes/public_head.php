    <!-- Meta & SEO Essentials -->
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0"/>
    <meta name="description" content="<?= e(get_setting($pdo, 'meta_description', '')) ?>">
    <meta property="og:site_name" content="<?= e(brand('hotel_name')) ?>">
    <meta property="og:image" content="<?= e(brand_logo_url()) ?>">
    <?= brand_favicon_tags() ?>
    
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
    
    <?php
    $pub_pal = brand_palette();
    $pub_primary = $pub_pal['primary'];
    $pub_secondary = $pub_pal['secondary'];
    ?>
    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            DEFAULT: '<?= $pub_primary ?>',
                            dark: '<?= $pub_pal['primary_dark'] ?>',
                            light: '<?= $pub_pal['primary_light'] ?>',
                            subtle: '<?= $pub_pal['primary_subtle'] ?>'
                        },
                        gold: {
                            DEFAULT: '<?= $pub_secondary ?>',
                            light: '<?= $pub_pal['secondary_light'] ?>',
                            dark: '<?= $pub_pal['secondary_dark'] ?>',
                            accent: '#F59E0B'
                        },
                        cream: '#FDFBF7',
                        lightgray: '#F8FAFC',
                        surface: 'rgba(255, 255, 255, 0.07)',
                        'surface-dark': 'rgba(<?= implode(', ', hex_rgb_array($pub_primary)) ?>, 0.88)'
                    },
                    fontFamily: {
                        display: ['Playfair Display', 'serif'],
                        garamond: ['Cormorant Garamond', 'serif'],
                        poppins: ['Poppins', 'sans-serif'],
                        inter: ['Inter', 'sans-serif'],
                        montserrat: ['Montserrat', 'sans-serif']
                    },
                    boxShadow: {
                        'luxury': '0 20px 40px -15px rgba(var(--theme-primary-rgb), 0.15)',
                        'gold-glow': '0 0 25px rgba(var(--theme-secondary-rgb), 0.35)',
                        'gold-sm': '0 4px 14px rgba(var(--theme-secondary-rgb), 0.25)',
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
            color: var(--theme-primary);
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
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 10px 30px -10px rgba(var(--theme-primary-rgb), 0.15);
        }
        .glass-dark-luxury {
            background: rgba(var(--theme-primary-rgb), 0.85);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.35);
        }

        /* Dynamic Brand Overrides from Database Settings */
        <?= brand_css_vars() ?>
        .bg-navy { background-color: var(--theme-primary) !important; }
        .text-navy { color: var(--theme-primary) !important; }
        .border-navy { border-color: var(--theme-primary) !important; }
        .bg-gold { background-color: var(--theme-secondary) !important; }
        .text-gold { color: var(--theme-secondary) !important; }
        .border-gold { border-color: var(--theme-secondary) !important; }

        /* Gold Gradient Text */
        .gold-gradient-text {
            background: linear-gradient(135deg, var(--theme-secondary-light) 0%, var(--theme-secondary) 50%, var(--theme-secondary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .gold-gradient-bg {
            background: linear-gradient(135deg, var(--theme-secondary-light) 0%, var(--theme-secondary) 60%, var(--theme-secondary-dark) 100%);
        }
        .swal2-styled.swal2-confirm { background-color: var(--theme-primary) !important; }
        :focus-visible { outline: 2px solid var(--theme-secondary); outline-offset: 2px; }

        /* 3D Dynamic Hover Lift */
        .hover-lift {
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s ease;
        }
        .hover-lift:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 35px -10px rgba(var(--theme-primary-rgb), 0.18);
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
            background: rgba(var(--theme-primary-rgb), 0.94);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 -8px 25px rgba(0, 0, 0, 0.2);
        }

        /* Pool Parallax Overlay */
        .pool-overlay {
            background: radial-gradient(circle at 60% 50%, transparent 20%, rgba(var(--theme-primary-rgb), 0.85) 90%);
        }
    </style>
    <?= brand_js() ?>
