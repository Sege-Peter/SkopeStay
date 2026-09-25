<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth overflow-x-hidden w-full">
<head>
    <title>Careers | SkopeStay Technology</title>
<?php include 'includes/public_head.php'; ?>
</head>
<body class="bg-[#F8FAFC] text-navy relative overflow-x-hidden flex flex-col min-h-screen">
<?php include 'includes/public_header.php'; ?>

    <!-- Cinematic Hero Section -->
    <section class="relative h-[60vh] w-full overflow-hidden flex items-center justify-center bg-navy mt-16 lg:mt-0">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?q=80&w=2070&auto=format&fit=crop" class="w-full h-full object-cover animate-slow-zoom opacity-50" alt="Join SkopeStay Team">
            <div class="absolute inset-0 bg-gradient-to-t from-navy via-navy/50 to-transparent"></div>
        </div>
        <div class="relative z-10 text-center px-6 max-w-4xl mx-auto" data-aos="fade-up" data-aos-duration="1500">
            <span class="font-montserrat text-gold tracking-[0.2em] text-sm md:text-base font-bold uppercase mb-4 block">Careers</span>
            <h1 class="font-poppins text-5xl md:text-6xl font-bold text-white leading-tight mb-6 drop-shadow-2xl">
                Build the Future of <span class="text-transparent bg-clip-text bg-gradient-to-r from-gold to-yellow-300">Hospitality Tech</span>
            </h1>
            <p class="font-inter text-gray-300 text-lg md:text-xl max-w-2xl mx-auto mb-10">
                Join our mission to revolutionize global hospitality management.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="#opportunities" class="bg-gold text-white px-8 py-3 rounded-full font-bold shadow-lg hover:bg-yellow-500 transition-colors">Explore Roles</a>
                <button class="glass text-white px-8 py-3 rounded-full font-bold hover:bg-white hover:text-navy transition-colors">Our Culture</button>
            </div>
        </div>
    </section>

    <main class="max-w-7xl mx-auto px-6 py-12 space-y-16">
        
        <!-- Why Work With Us (Bento Style) -->
        <section>
            <div class="mb-12 text-center md:text-left" data-aos="fade-up">
                <span class="font-montserrat text-gold tracking-widest text-sm font-bold uppercase block mb-2">Benefits</span>
                <h2 class="font-poppins text-4xl font-bold text-navy">Why Work With Us?</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                <!-- Large Card -->
                <div class="md:col-span-12 lg:col-span-8 bg-white p-10 rounded-3xl border border-gray-100 shadow-xl hover-lift flex flex-col justify-center" data-aos="fade-up">
                    <div class="mb-6 w-16 h-16 bg-navy/5 rounded-2xl flex items-center justify-center text-navy">
                        <span class="material-symbols-outlined text-4xl">rocket_launch</span>
                    </div>
                    <h3 class="font-poppins text-3xl font-bold text-navy mb-4">Career Growth & Mobility</h3>
                    <p class="text-gray-600 text-lg">We prioritize internal promotions and provide clear pathways for professional advancement. Whether you're entering management or specializing in a technical field, we support your trajectory.</p>
                </div>

                <!-- Small Grid Items -->
                <div class="md:col-span-6 lg:col-span-4 grid gap-6">
                    <div class="bg-navy text-white p-8 rounded-3xl shadow-xl hover-lift relative overflow-hidden" data-aos="fade-left">
                        <div class="absolute -right-6 -top-6 w-32 h-32 bg-gold/20 rounded-full blur-2xl"></div>
                        <span class="material-symbols-outlined text-gold text-4xl mb-4 relative z-10">payments</span>
                        <h3 class="font-poppins text-xl font-bold mb-2 relative z-10">Competitive Pay</h3>
                        <p class="text-gray-400 text-sm relative z-10">Industry-leading packages with annual bonuses.</p>
                    </div>
                    <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-xl hover-lift" data-aos="fade-left" data-aos-delay="100">
                        <span class="material-symbols-outlined text-navy text-4xl mb-4">groups</span>
                        <h3 class="font-poppins text-xl font-bold text-navy mb-2">Collaboration</h3>
                        <p class="text-gray-600 text-sm">Work alongside top-tier talent worldwide.</p>
                    </div>
                </div>

                <!-- Wide Row Item -->
                <div class="md:col-span-6 lg:col-span-4 bg-white p-10 rounded-3xl border border-gray-100 shadow-xl hover-lift" data-aos="fade-up">
                    <span class="material-symbols-outlined text-navy text-4xl mb-6">school</span>
                    <h3 class="font-poppins text-2xl font-bold text-navy mb-4">Professional Development</h3>
                    <p class="text-gray-600">Annual learning stipends for certifications, workshops, and international conferences.</p>
                </div>
                
                <div class="md:col-span-12 lg:col-span-8 rounded-3xl overflow-hidden relative h-[300px] hover-lift shadow-xl border border-gray-100" data-aos="fade-up" data-aos-delay="100">
                    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=2070&auto=format&fit=crop" alt="Culture" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-navy/60 backdrop-blur-[2px] flex items-center p-8">
                        <div class="glass p-6 rounded-2xl max-w-md border border-white/20">
                            <p class="font-inter italic text-white mb-4">"SkopeStay isn't just a workplace; it's an incubator for excellence. The support for innovation here is unparalleled."</p>
                            <p class="font-bold text-gold">— Sarah Chen, Senior Product Lead</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Current Opportunities -->
        <section id="opportunities">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
                <div data-aos="fade-right">
                    <span class="font-montserrat text-gold tracking-widest text-sm font-bold uppercase block mb-2">Join Us</span>
                    <h2 class="font-poppins text-4xl font-bold text-navy">Current Opportunities</h2>
                </div>
                <div class="flex gap-3 overflow-x-auto pb-2 w-full md:w-auto no-scrollbar" data-aos="fade-left">
                    <button class="bg-navy text-white px-6 py-2 rounded-full font-bold text-sm shadow-md whitespace-nowrap">All Roles</button>
                    <button class="bg-white text-navy border border-gray-200 px-6 py-2 rounded-full font-bold text-sm hover:bg-gray-50 whitespace-nowrap">Technology</button>
                    <button class="bg-white text-navy border border-gray-200 px-6 py-2 rounded-full font-bold text-sm hover:bg-gray-50 whitespace-nowrap">Hospitality</button>
                    <button class="bg-white text-navy border border-gray-200 px-6 py-2 rounded-full font-bold text-sm hover:bg-gray-50 whitespace-nowrap">Marketing</button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Job Card 1 -->
                <div class="bg-white p-8 rounded-3xl border border-gray-100 hover:border-gold transition-colors shadow-lg hover-lift group flex flex-col" data-aos="fade-up">
                    <div class="flex justify-between items-start mb-6">
                        <span class="bg-navy/10 text-navy px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider">Technology</span>
                        <span class="text-gray-500 text-sm flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">location_on</span> Remote</span>
                    </div>
                    <h3 class="font-poppins text-xl font-bold text-navy mb-3 group-hover:text-gold transition-colors">Senior Full Stack Engineer</h3>
                    <p class="text-gray-600 text-sm mb-8 flex-1">Scale our property management API and build frictionless guest experiences.</p>
                    <div class="flex justify-between items-center border-t border-gray-100 pt-6 mt-auto">
                        <span class="font-bold text-navy">$140k - $180k</span>
                        <a href="apply.php" class="text-gold font-bold flex items-center gap-1 hover:gap-2 transition-all">Apply <span class="material-symbols-outlined text-[18px]">arrow_forward</span></a>
                    </div>
                </div>

                <!-- Job Card 2 -->
                <div class="bg-white p-8 rounded-3xl border border-gray-100 hover:border-gold transition-colors shadow-lg hover-lift group flex flex-col" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex justify-between items-start mb-6">
                        <span class="bg-gold/10 text-gold px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider">Hospitality</span>
                        <span class="text-gray-500 text-sm flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">location_on</span> New York, NY</span>
                    </div>
                    <h3 class="font-poppins text-xl font-bold text-navy mb-3 group-hover:text-gold transition-colors">Luxury Experience Manager</h3>
                    <p class="text-gray-600 text-sm mb-8 flex-1">Curate world-class stays for our most discerning corporate clientele.</p>
                    <div class="flex justify-between items-center border-t border-gray-100 pt-6 mt-auto">
                        <span class="font-bold text-navy">$95k - $120k</span>
                        <a href="apply.php" class="text-gold font-bold flex items-center gap-1 hover:gap-2 transition-all">Apply <span class="material-symbols-outlined text-[18px]">arrow_forward</span></a>
                    </div>
                </div>

                <!-- Job Card 3 -->
                <div class="bg-white p-8 rounded-3xl border border-gray-100 hover:border-gold transition-colors shadow-lg hover-lift group flex flex-col" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex justify-between items-start mb-6">
                        <span class="bg-blue-500/10 text-blue-600 px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider">Marketing</span>
                        <span class="text-gray-500 text-sm flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">location_on</span> Austin, TX</span>
                    </div>
                    <h3 class="font-poppins text-xl font-bold text-navy mb-3 group-hover:text-gold transition-colors">Growth Marketing Lead</h3>
                    <p class="text-gray-600 text-sm mb-8 flex-1">Drive acquisition strategy across premium digital channels and partnerships.</p>
                    <div class="flex justify-between items-center border-t border-gray-100 pt-6 mt-auto">
                        <span class="font-bold text-navy">$110k - $150k</span>
                        <a href="apply.php" class="text-gold font-bold flex items-center gap-1 hover:gap-2 transition-all">Apply <span class="material-symbols-outlined text-[18px]">arrow_forward</span></a>
                    </div>
                </div>
                
                <!-- Custom CTA Card -->
                <div class="bg-navy p-8 rounded-3xl border border-navy shadow-lg hover-lift flex flex-col justify-center items-center text-center text-white" data-aos="fade-up" data-aos-delay="300">
                    <span class="material-symbols-outlined text-gold text-5xl mb-4">auto_awesome</span>
                    <h3 class="font-poppins text-2xl font-bold mb-3">Don't see your fit?</h3>
                    <p class="text-gray-400 text-sm mb-8">We're always looking for exceptional talent. Send us an open application.</p>
                    <a href="apply.php" class="bg-gold text-white px-8 py-3 rounded-full font-bold hover:bg-yellow-500 transition-colors shadow-lg">Submit CV</a>
                </div>
            </div>
        </section>

        <!-- Newsletter / Contact -->
        <section class="bg-white rounded-3xl overflow-hidden relative shadow-2xl flex flex-col lg:flex-row border border-gray-100" data-aos="fade-up">
            <div class="lg:w-1/2 p-12 lg:p-16 z-10 flex flex-col justify-center">
                <h2 class="font-poppins text-3xl font-bold text-navy mb-4">Stay Informed</h2>
                <p class="text-gray-600 mb-8 max-w-md">Get notified about new career opportunities and insights into the future of hospitality tech.</p>
                <form class="flex flex-col sm:flex-row gap-4" onsubmit="event.preventDefault(); Swal.fire({toast:true, position:'top-end', icon:'success', title:'Joined Talent Pool!', showConfirmButton:false, timer:3000});">
                    <input class="flex-grow bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-navy focus:ring-2 focus:ring-gold focus:border-gold outline-none transition-all placeholder:text-gray-400" placeholder="Your work email" type="email" required/>
                    <button class="bg-navy text-white px-8 py-3 rounded-xl font-bold hover:bg-gold transition-colors shadow-md" type="submit">Join Talent Pool</button>
                </form>
            </div>
            <div class="lg:w-1/2 relative min-h-[300px]">
                <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover" alt="Architecture">
                <div class="absolute inset-0 bg-gradient-to-r from-white to-transparent lg:hidden"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-white to-transparent hidden lg:block lg:bg-gradient-to-l"></div>
            </div>
        </section>

    </main>

<?php include 'includes/public_footer.php'; ?>
