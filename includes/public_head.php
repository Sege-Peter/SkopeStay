    <!-- Fonts: Poppins, Inter, Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: '#0F172A',
                        gold: '#F59E0B',
                        lightgray: '#F8FAFC',
                        surface: 'rgba(255, 255, 255, 0.05)',
                        'surface-dark': 'rgba(15, 23, 42, 0.8)'
                    },
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                        inter: ['Inter', 'sans-serif'],
                        montserrat: ['Montserrat', 'sans-serif']
                    },
                    animation: {
                        'slow-zoom': 'slowZoom 20s ease-in-out infinite alternate',
                        'ripple': 'ripple 4s linear infinite',
                        'float': 'float 4s ease-in-out infinite',
                        'slide-in': 'slideIn 0.3s ease-out forwards'
                    },
                    keyframes: {
                        slowZoom: {
                            '0%': { transform: 'scale(1)' },
                            '100%': { transform: 'scale(1.15)' }
                        },
                        ripple: {
                            '0%': { transform: 'scale(0.8)', opacity: '0.5' },
                            '100%': { transform: 'scale(2.5)', opacity: '0' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-15px)' }
                        },
                        slideIn: {
                            '0%': { transform: 'translateX(-100%)' },
                            '100%': { transform: 'translateX(0)' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        html, body { 
            max-width: 100%; 
            overflow-x: hidden; 
        }
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Poppins', sans-serif; }
        .font-accent { font-family: 'Montserrat', sans-serif; }
        
        /* Premium Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }
        .glass-dark {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* 3D Hover Lift */
        .hover-lift { transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.4s ease; }
        .hover-lift:hover { transform: translateY(-10px); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
        
        /* Image Zoom on Hover */
        .img-zoom-container { overflow: hidden; }
        .img-zoom-container img { transition: transform 0.8s ease; }
        .img-zoom-container:hover img { transform: scale(1.1); }
        
        /* Hide scrollbar */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Skeleton Loader */
        .skeleton {
            background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
            background-size: 200% 100%;
            animation: skeletonLoading 1.5s infinite;
        }
        @keyframes skeletonLoading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Mega Menu */
        .mega-menu { display: none; opacity: 0; transition: opacity 0.3s ease; }
        .nav-item:hover .mega-menu { display: block; opacity: 1; animation: fadeIn 0.3s forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        /* Mobile Nav Override */
        #mobile-menu { transform: translateX(-100%); transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        #mobile-menu.open { transform: translateX(0); }
        #mobile-overlay { opacity: 0; pointer-events: none; transition: opacity 0.3s ease; }
        #mobile-overlay.open { opacity: 1; pointer-events: auto; }
        
        /* Bottom Action Bar spacing */
        body { padding-bottom: 70px; }
        @media(min-width: 768px) { body { padding-bottom: 0; } }

        /* Pool Ripple Overlay */
        .pool-overlay {
            background: radial-gradient(circle at center, transparent 30%, rgba(15,23,42,0.8) 100%);
        }
    </style>
