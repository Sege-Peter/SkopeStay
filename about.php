<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth overflow-x-hidden w-full">
<head>
    <title>About Us | SkopeStay</title>
<?php include 'includes/public_head.php'; ?>
</head>
<body class="text-navy relative flex flex-col min-h-screen">
<?php include 'includes/public_header.php'; ?>

    <!-- Cinematic Hero Section -->
    <section class="relative h-[60vh] w-full overflow-hidden flex items-center justify-center bg-navy mt-16 lg:mt-0">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1542314831-c53cd3816002?q=80&w=2070&auto=format&fit=crop" class="w-full h-full object-cover animate-slow-zoom opacity-60" alt="About SkopeStay">
            <div class="absolute inset-0 bg-gradient-to-t from-navy via-navy/50 to-transparent"></div>
        </div>
        <div class="relative z-10 text-center px-6 max-w-4xl mx-auto" data-aos="fade-up" data-aos-duration="1500">
            <span class="font-montserrat text-gold tracking-[0.2em] text-sm md:text-base font-bold uppercase mb-4 block">Our Story</span>
            <h1 class="font-poppins text-5xl md:text-6xl font-bold text-white leading-tight mb-6 drop-shadow-2xl">
                Redefining Hospitality Through <span class="text-transparent bg-clip-text bg-gradient-to-r from-gold to-yellow-300">Innovation</span>
            </h1>
            <p class="font-inter text-gray-300 text-lg md:text-xl max-w-2xl mx-auto">
                SkopeStay bridges the gap between discerning guests and premier properties.
            </p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-6 py-12 space-y-16">
        
        <!-- Introduction -->
        <section class="grid lg:grid-cols-2 gap-16 items-center">
            <div class="space-y-8" data-aos="fade-right">
                <div>
                    <span class="font-montserrat text-gold tracking-widest text-sm font-bold uppercase block mb-2">The Platform</span>
                    <h2 class="font-poppins text-4xl font-bold text-navy">Innovating the Guest Journey</h2>
                </div>
                <div class="prose prose-lg text-gray-600">
                    <p>SkopeStay is an all-in-one platform designed to revolutionize the way travelers and property managers interact. Our technology bridge connects guests with premier locations, offering a unified ecosystem for every hospitality need.</p>
                    <p>From individual room reservations to large-scale event planning, we eliminate friction through data-driven precision and a commitment to elegant service.</p>
                </div>
                <div class="bg-lightgray p-8 rounded-3xl border border-gray-100 shadow-sm border-l-4 border-l-gold">
                    <h3 class="font-poppins text-xl font-bold text-navy mb-3">Who We Are</h3>
                    <p class="text-gray-600 italic">"At our core, SkopeStay is a collective of hospitality veterans and tech innovators driven by the vision of transforming hospitality through technology. We believe that professional management should be as effortless as a luxury stay."</p>
                </div>
            </div>
            <div class="relative group h-[500px]" data-aos="fade-left">
                <div class="absolute inset-0 bg-gold/10 rounded-3xl translate-x-4 translate-y-4 group-hover:translate-x-2 group-hover:translate-y-2 transition-transform duration-500"></div>
                <img class="relative rounded-3xl shadow-2xl w-full h-full object-cover border border-white/50" src="https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=2069&auto=format&fit=crop" alt="Control Center">
            </div>
        </section>

        <!-- Mission & Vision (Bento) -->
        <section class="grid md:grid-cols-2 gap-8">
            <div class="bg-white p-12 rounded-[2rem] shadow-xl hover-lift border border-gray-100" data-aos="fade-up">
                <div class="w-20 h-20 bg-navy/5 rounded-2xl flex items-center justify-center mb-8">
                    <span class="material-symbols-outlined text-4xl text-navy">flag</span>
                </div>
                <h2 class="font-poppins text-3xl font-bold text-navy mb-4">Our Mission</h2>
                <p class="text-gray-600 text-lg leading-relaxed">To empower property owners and delight guests by providing a seamless, secure, and comprehensive digital ecosystem for all hospitality and facility management needs.</p>
            </div>
            <div class="bg-navy p-12 rounded-[2rem] shadow-xl hover-lift border border-navy text-white relative overflow-hidden" data-aos="fade-up" data-aos-delay="100">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-gold/20 rounded-full blur-3xl"></div>
                <div class="w-20 h-20 bg-white/10 rounded-2xl flex items-center justify-center mb-8 relative z-10 backdrop-blur-sm">
                    <span class="material-symbols-outlined text-4xl text-gold">visibility</span>
                </div>
                <h2 class="font-poppins text-3xl font-bold mb-4 relative z-10">Our Vision</h2>
                <p class="text-gray-300 text-lg leading-relaxed relative z-10">To become the global gold standard in smart hospitality management, where every booking, interaction, and service is optimized for excellence through intelligent technology.</p>
            </div>
        </section>

        <!-- Values -->
        <section class="text-center">
            <div class="mb-16" data-aos="fade-up">
                <span class="font-montserrat text-gold tracking-widest text-sm font-bold uppercase block mb-2">Our Principles</span>
                <h2 class="font-poppins text-4xl font-bold text-navy">The SkopeStay Values</h2>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                <div class="bg-white p-8 rounded-3xl shadow-lg border border-gray-100 hover-lift group" data-aos="fade-up" data-aos-delay="100">
                    <span class="material-symbols-outlined text-4xl text-navy mb-4 group-hover:text-gold transition-colors">diamond</span>
                    <h4 class="font-poppins font-bold text-navy mb-2">Excellence</h4>
                    <p class="text-xs text-gray-500 uppercase tracking-widest">In Every Detail</p>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-lg border border-gray-100 hover-lift group" data-aos="fade-up" data-aos-delay="200">
                    <span class="material-symbols-outlined text-4xl text-navy mb-4 group-hover:text-gold transition-colors">lightbulb</span>
                    <h4 class="font-poppins font-bold text-navy mb-2">Innovation</h4>
                    <p class="text-xs text-gray-500 uppercase tracking-widest">Tech Forward</p>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-lg border border-gray-100 hover-lift group" data-aos="fade-up" data-aos-delay="300">
                    <span class="material-symbols-outlined text-4xl text-navy mb-4 group-hover:text-gold transition-colors">verified_user</span>
                    <h4 class="font-poppins font-bold text-navy mb-2">Integrity</h4>
                    <p class="text-xs text-gray-500 uppercase tracking-widest">Building Trust</p>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-lg border border-gray-100 hover-lift group" data-aos="fade-up" data-aos-delay="400">
                    <span class="material-symbols-outlined text-4xl text-navy mb-4 group-hover:text-gold transition-colors">favorite</span>
                    <h4 class="font-poppins font-bold text-navy mb-2">Customer First</h4>
                    <p class="text-xs text-gray-500 uppercase tracking-widest">Your Success</p>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-lg border border-gray-100 hover-lift group" data-aos="fade-up" data-aos-delay="500">
                    <span class="material-symbols-outlined text-4xl text-navy mb-4 group-hover:text-gold transition-colors">lock</span>
                    <h4 class="font-poppins font-bold text-navy mb-2">Security</h4>
                    <p class="text-xs text-gray-500 uppercase tracking-widest">Data Protection</p>
                </div>
            </div>
        </section>

    </main>

<?php include 'includes/public_footer.php'; ?>
