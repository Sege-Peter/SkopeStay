<!DOCTYPE html>

<html class="light scroll-smooth" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Application Submitted | SkopeStay</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Geist:wght@400;500&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                    "on-error-container": "#93000a",
                    "secondary": "#855300",
                    "outline-variant": "#c6c6cd",
                    "surface-dim": "#cbdbf5",
                    "secondary-container": "#fea619",
                    "primary": "#000000",
                    "on-secondary": "#ffffff",
                    "background": "#f8f9ff",
                    "primary-fixed-dim": "#bec6e0",
                    "surface-tint": "#565e74",
                    "error-container": "#ffdad6",
                    "secondary-fixed-dim": "#ffb95f",
                    "on-primary": "#ffffff",
                    "error": "#ba1a1a",
                    "surface-container-low": "#eff4ff",
                    "on-surface": "#0b1c30",
                    "surface-container-high": "#dce9ff",
                    "surface-container": "#e5eeff",
                    "secondary-fixed": "#ffddb8",
                    "inverse-primary": "#bec6e0",
                    "on-primary-fixed": "#131b2e",
                    "on-secondary-container": "#684000",
                    "on-error": "#ffffff",
                    "tertiary-container": "#001a42",
                    "surface-bright": "#f8f9ff",
                    "tertiary-fixed": "#d8e2ff",
                    "on-secondary-fixed-variant": "#653e00",
                    "on-primary-container": "#7c839b",
                    "surface-container-lowest": "#ffffff",
                    "on-surface-variant": "#45464d",
                    "primary-container": "#131b2e",
                    "on-tertiary-container": "#3980f4",
                    "primary-fixed": "#dae2fd",
                    "on-background": "#0b1c30",
                    "inverse-surface": "#213145",
                    "on-tertiary-fixed-variant": "#004395",
                    "on-tertiary": "#ffffff",
                    "on-secondary-fixed": "#2a1700",
                    "on-tertiary-fixed": "#001a42",
                    "inverse-on-surface": "#eaf1ff",
                    "tertiary-fixed-dim": "#adc6ff",
                    "surface-variant": "#d3e4fe",
                    "surface": "#f8f9ff",
                    "outline": "#76777d",
                    "tertiary": "#000000",
                    "surface-container-highest": "#d3e4fe",
                    "on-primary-fixed-variant": "#3f465c"
            },
            "borderRadius": {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "full": "9999px"
            },
            "spacing": {
                    "gutter": "1.5rem",
                    "xs": "0.25rem",
                    "margin-desktop": "2.5rem",
                    "lg": "1.5rem",
                    "base": "4px",
                    "2xl": "3rem",
                    "sm": "0.5rem",
                    "md": "1rem",
                    "margin-mobile": "1rem",
                    "xl": "2rem"
            },
            "fontFamily": {
                    "headline-lg": ["Inter"],
                    "display-lg": ["Inter"],
                    "headline-md": ["Inter"],
                    "body-lg": ["Inter"],
                    "body-md": ["Inter"],
                    "label-md": ["Geist"],
                    "headline-lg-mobile": ["Inter"],
                    "title-lg": ["Inter"],
                    "caption": ["Inter"]
            },
            "fontSize": {
                    "headline-lg": ["32px", {"lineHeight": "1.3", "fontWeight": "600"}],
                    "display-lg": ["48px", {"lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                    "headline-md": ["24px", {"lineHeight": "1.4", "fontWeight": "600"}],
                    "body-lg": ["16px", {"lineHeight": "1.6", "fontWeight": "400"}],
                    "body-md": ["14px", {"lineHeight": "1.5", "fontWeight": "400"}],
                    "label-md": ["12px", {"lineHeight": "1.4", "letterSpacing": "0.05em", "fontWeight": "500"}],
                    "headline-lg-mobile": ["24px", {"lineHeight": "1.3", "fontWeight": "600"}],
                    "title-lg": ["20px", {"lineHeight": "1.5", "fontWeight": "600"}],
                    "caption": ["12px", {"lineHeight": "1.4", "fontWeight": "400"}]
            }
          },
        },
      }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
        }
        @keyframes subtle-bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-check {
            animation: subtle-bounce 2.5s infinite ease-in-out;
        }
        .animate-fade-up {
            animation: fadeInUp 0.6s ease forwards;
        }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.25s; }
        .delay-3 { animation-delay: 0.4s; }
        .bento-card {
            background: white;
            border: 1px solid #E2E8F0;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .bento-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.08);
        }
    </style>
</head>
<body class="bg-background text-on-surface font-body-md selection:bg-secondary-container selection:text-on-secondary-container overflow-x-hidden w-full">
<!-- TopNavBar -->
<nav class="bg-surface-container-lowest top-0 shadow-sm transition-all duration-200 ease-in-out z-50 sticky">
<div class="flex justify-between items-center h-20 px-margin-desktop w-full max-w-7xl mx-auto">
<a href="index.php" class="font-headline-md text-headline-md font-bold text-on-surface dark:text-surface-bright">SkopeStay</a>
<div class="hidden md:flex items-center gap-xl">
<a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="index.php">Explore</a>
<a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="index.php#halls">Business</a>
<a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="about.php">About</a>
<a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="mailto:support@skopestay.com">Support</a>
</div>
<div class="flex items-center gap-md">
<a href="auth/login.php" class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors px-4 py-2">Login</a>
<a href="careers.php" class="bg-primary text-on-primary px-6 py-2 rounded-lg font-body-md font-bold hover:opacity-90 transition-opacity">View Careers</a>
</div>
</div>
</nav>

<main class="min-h-screen py-2xl px-margin-mobile md:px-margin-desktop max-w-7xl mx-auto">
<!-- Success Header Section -->
<section class="flex flex-col items-center text-center mb-2xl opacity-0 animate-fade-up">
<div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mb-lg animate-check shadow-lg shadow-green-200">
<span class="material-symbols-outlined text-6xl text-green-600" style="font-variation-settings: 'FILL' 1;">check_circle</span>
</div>
<h1 class="font-display-lg text-display-lg mb-md text-on-surface">Application Submitted!</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mx-auto">
    Thank you for your interest in joining SkopeStay. We have received your application and our talent acquisition team will review your profile shortly.
</p>
</section>

<!-- Recruitment Journey - Asymmetric Bento Layout -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter mb-2xl opacity-0 animate-fade-up delay-1">
<!-- Timeline Visualization -->
<div class="md:col-span-2 bento-card p-xl rounded-xl">
<h3 class="font-title-lg text-title-lg mb-xl flex items-center gap-2">
<span class="material-symbols-outlined text-primary">route</span>
    Your Recruitment Journey
</h3>
<div class="relative py-8">
<!-- Progress Line Background -->
<div class="absolute top-[40px] left-0 w-full h-1 bg-surface-container-high rounded-full overflow-hidden">
<div class="h-full bg-green-500 w-[16%] rounded-full transition-all duration-1000"></div>
</div>
<!-- Steps -->
<div class="relative flex justify-between items-start w-full">
<!-- Step 1: Submitted (Active) -->
<div class="flex flex-col items-center gap-3 z-10">
<div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white ring-4 ring-green-100 shadow-md">
<span class="material-symbols-outlined text-sm">done</span>
</div>
<span class="font-label-md text-label-md font-bold text-green-600 text-center">Submitted</span>
</div>
<!-- Step 2: Review -->
<div class="flex flex-col items-center gap-3 z-10">
<div class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center text-on-surface-variant">
<span class="material-symbols-outlined text-sm">pageview</span>
</div>
<span class="font-label-md text-label-md text-on-surface-variant text-center">Review</span>
</div>
<!-- Step 3: Shortlisting -->
<div class="flex flex-col items-center gap-3 z-10">
<div class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center text-on-surface-variant">
<span class="material-symbols-outlined text-sm">list_alt</span>
</div>
<span class="font-label-md text-label-md text-on-surface-variant text-center">Shortlist</span>
</div>
<!-- Step 4: Interview -->
<div class="flex flex-col items-center gap-3 z-10">
<div class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center text-on-surface-variant">
<span class="material-symbols-outlined text-sm">groups</span>
</div>
<span class="font-label-md text-label-md text-on-surface-variant text-center">Interview</span>
</div>
<!-- Step 5: Final Evaluation -->
<div class="flex flex-col items-center gap-3 z-10">
<div class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center text-on-surface-variant">
<span class="material-symbols-outlined text-sm">verified</span>
</div>
<span class="font-label-md text-label-md text-on-surface-variant text-center">Evaluation</span>
</div>
<!-- Step 6: Offer -->
<div class="flex flex-col items-center gap-3 z-10">
<div class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center text-on-surface-variant">
<span class="material-symbols-outlined text-sm">description</span>
</div>
<span class="font-label-md text-label-md text-on-surface-variant text-center">Offer</span>
</div>
</div>
</div>
<p class="font-caption text-caption mt-xl text-on-surface-variant bg-surface-container-low p-md rounded-lg border border-outline-variant/50">
<strong>⏱ Expected Timeline:</strong> On average, the review process takes 7–10 business days. You will receive an email notification once your status changes.
</p>
</div>
<!-- Contact/Support Card -->
<div class="md:col-span-1 bento-card p-xl rounded-xl bg-on-surface text-surface-bright border-none shadow-xl">
<h3 class="font-title-lg text-title-lg mb-lg">Need Assistance?</h3>
<p class="font-body-md text-body-md opacity-80 mb-xl">Our recruitment team is here to help if you have any questions regarding your application or the SkopeStay hiring process.</p>
<div class="space-y-lg">
<div class="flex items-start gap-md">
<div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-secondary-fixed">mail</span>
</div>
<div>
<p class="font-label-md text-label-md opacity-60">Email Support</p>
<a href="mailto:careers@skopestay.com" class="font-body-md text-body-md font-bold text-secondary-container hover:underline">careers@skopestay.com</a>
</div>
</div>
<div class="flex items-start gap-md">
<div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-secondary-fixed">call</span>
</div>
<div>
<p class="font-label-md text-label-md opacity-60">Recruitment Office</p>
<p class="font-body-md text-body-md font-bold">+254 700 000 000</p>
</div>
</div>
</div>
<div class="mt-xl pt-xl border-t border-surface-variant/20">
<p class="font-caption text-caption italic opacity-60">Office Hours: Mon – Fri, 8:00 AM – 5:00 PM (EAT)</p>
</div>
</div>
</div>

<!-- Next Steps / Actions -->
<section class="flex flex-col sm:flex-row items-center justify-center gap-md mb-2xl opacity-0 animate-fade-up delay-2">
<a class="bg-primary text-on-primary px-xl py-lg rounded-lg font-headline-md text-headline-md flex items-center gap-2 hover:bg-primary/90 transition-all shadow-lg active:scale-95" href="index.php">
    Return to Home
    <span class="material-symbols-outlined">arrow_forward</span>
</a>
<a href="careers.php" class="px-xl py-lg rounded-lg font-headline-md text-headline-md border border-outline hover:bg-surface-container-low transition-all flex items-center gap-2">
    <span class="material-symbols-outlined">work</span>
    Browse More Jobs
</a>
</section>

<!-- Aesthetic Imagery Component -->
<div class="mt-2xl h-80 rounded-2xl overflow-hidden relative shadow-2xl opacity-0 animate-fade-up delay-3">
<div class="w-full h-full bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=2069&auto=format&fit=crop')"></div>
<div class="absolute inset-0 bg-gradient-to-t from-on-surface/80 via-on-surface/30 to-transparent flex items-end p-xl">
<p class="text-surface-bright font-title-lg text-title-lg max-w-xl leading-snug">Join a team that values precision, luxury, and professional excellence.</p>
</div>
</div>
</main>

<!-- Footer -->
<footer class="bg-on-surface text-surface-bright">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter px-margin-desktop py-xl w-full max-w-7xl mx-auto">
<div class="flex flex-col gap-md">
<div class="font-title-lg text-title-lg font-bold text-surface-bright">SkopeStay</div>
<p class="font-caption text-caption opacity-70 text-surface-variant">Empowering global facility management with precision and luxury.</p>
</div>
<div class="flex flex-col gap-sm">
<h4 class="font-body-md font-bold mb-xs">Legal</h4>
<a class="font-caption text-caption text-surface-variant opacity-80 hover:opacity-100 hover:text-secondary-container transition-all" href="privacy.php">Privacy Policy</a>
<a class="font-caption text-caption text-surface-variant opacity-80 hover:opacity-100 hover:text-secondary-container transition-all" href="terms.php">Terms of Service</a>
<a class="font-caption text-caption text-surface-variant opacity-80 hover:opacity-100 hover:text-secondary-container transition-all" href="privacy.php">Cookie Policy</a>
</div>
<div class="flex flex-col gap-sm">
<h4 class="font-body-md font-bold mb-xs">Contact</h4>
<a class="font-caption text-caption text-surface-variant opacity-80 hover:opacity-100 hover:text-secondary-container transition-all" href="mailto:support@skopestay.com">Contact Us</a>
<a class="font-caption text-caption text-surface-variant opacity-80 hover:opacity-100 hover:text-secondary-container transition-all" href="mailto:support@skopestay.com">Global Offices</a>
<a class="font-caption text-caption text-surface-variant opacity-80 hover:opacity-100 hover:text-secondary-container transition-all" href="mailto:support@skopestay.com">Accessibility</a>
</div>
<div class="flex flex-col gap-md">
<h4 class="font-body-md font-bold mb-xs">Follow Us</h4>
<div class="flex gap-md">
<span class="material-symbols-outlined cursor-pointer hover:text-secondary-container transition-colors">public</span>
<span class="material-symbols-outlined cursor-pointer hover:text-secondary-container transition-colors">hub</span>
<span class="material-symbols-outlined cursor-pointer hover:text-secondary-container transition-colors">business_center</span>
</div>
</div>
</div>
<div class="w-full border-t border-surface-variant/10 py-md px-margin-desktop max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center opacity-60">
<p class="font-caption text-caption">© 2024 SkopeStay Technology. All rights reserved.</p>
<p class="font-caption text-caption">Precision • Reliability • Excellence</p>
</div>
</footer>
<script>
        document.querySelectorAll('.bento-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                const icon = card.querySelector('.material-symbols-outlined');
                if (icon) {
                    icon.style.transform = 'scale(1.1) rotate(5deg)';
                    icon.style.transition = 'transform 0.3s ease';
                }
            });
            card.addEventListener('mouseleave', () => {
                const icon = card.querySelector('.material-symbols-outlined');
                if (icon) icon.style.transform = 'scale(1) rotate(0deg)';
            });
        });
    </script>
</body></html>
