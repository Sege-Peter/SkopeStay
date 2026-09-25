<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth overflow-x-hidden w-full">
<head>
    <title>Terms of Service | SkopeStay</title>
<?php include 'includes/public_head.php'; ?>
<style>
    section[id] {
        scroll-margin-top: 100px;
    }
</style>
</head>
<body class="bg-[#F8FAFC] text-navy relative overflow-x-hidden flex flex-col min-h-screen">
<?php include 'includes/public_header.php'; ?>

    <!-- Legal Header -->
    <section class="relative bg-navy py-16 mt-16 lg:mt-0 overflow-hidden border-b border-gray-800">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1589829085413-56de8ae18c73?q=80&w=2070&auto=format&fit=crop" class="w-full h-full object-cover opacity-20" alt="Legal Concept">
            <div class="absolute inset-0 bg-gradient-to-t from-navy to-transparent"></div>
        </div>
        <div class="relative z-10 max-w-7xl mx-auto px-6 text-center" data-aos="fade-up">
            <div class="flex flex-col gap-2 items-center">
                <span class="font-montserrat text-gold tracking-[0.2em] uppercase text-sm font-bold bg-gold/10 px-4 py-1 rounded-full backdrop-blur-sm border border-gold/20">Legal & Compliance</span>
                <h1 class="font-poppins text-5xl font-bold text-white mt-4">Terms of Service</h1>
                <div class="flex items-center justify-center gap-2 text-gray-300 font-medium mt-2">
                    <span class="material-symbols-outlined text-sm">event</span>
                    <p class="font-inter">Effective Date: June 26, 2026</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content Area -->
    <main class="max-w-7xl mx-auto px-6 py-12 flex flex-col md:flex-row gap-12 relative">
        <!-- Sticky Sidebar Table of Contents -->
        <aside class="hidden md:block w-72 shrink-0">
            <div class="sticky top-24 max-h-[calc(100vh-120px)] overflow-y-auto no-scrollbar pr-4">
                <h3 class="font-poppins font-bold text-navy uppercase mb-6 border-l-4 border-gold pl-4">Table of Contents</h3>
                <ul class="space-y-2 font-inter text-sm">
                    <li><a class="block py-1 text-gray-500 hover:text-gold transition-colors border-l-2 border-transparent pl-4" href="#sec1">1. Definitions</a></li>
                    <li><a class="block py-1 text-gray-500 hover:text-gold transition-colors border-l-2 border-transparent pl-4" href="#sec2">2. Eligibility</a></li>
                    <li><a class="block py-1 text-gray-500 hover:text-gold transition-colors border-l-2 border-transparent pl-4" href="#sec3">3. User Accounts</a></li>
                    <li><a class="block py-1 text-gray-500 hover:text-gold transition-colors border-l-2 border-transparent pl-4" href="#sec4">4. Bookings</a></li>
                    <li><a class="block py-1 text-gray-500 hover:text-gold transition-colors border-l-2 border-transparent pl-4" href="#sec5">5. Pricing</a></li>
                    <li><a class="block py-1 text-gray-500 hover:text-gold transition-colors border-l-2 border-transparent pl-4" href="#sec6">6. Payments</a></li>
                    <li><a class="block py-1 text-gray-500 hover:text-gold transition-colors border-l-2 border-transparent pl-4" href="#sec7">7. Cancellation and Refunds</a></li>
                    <li><a class="block py-1 text-gray-500 hover:text-gold transition-colors border-l-2 border-transparent pl-4" href="#sec8">8. Check-In and Check-Out</a></li>
                    <li><a class="block py-1 text-gray-500 hover:text-gold transition-colors border-l-2 border-transparent pl-4" href="#sec9">9. Restaurant Services</a></li>
                    <li><a class="block py-1 text-gray-500 hover:text-gold transition-colors border-l-2 border-transparent pl-4" href="#sec10">10. Hall and Event Bookings</a></li>
                    <li><a class="block py-1 text-gray-500 hover:text-gold transition-colors border-l-2 border-transparent pl-4" href="#sec11">11. Swimming Pool & Recreation</a></li>
                    <li><a class="block py-1 text-gray-500 hover:text-gold transition-colors border-l-2 border-transparent pl-4" href="#sec12">12. User Responsibilities</a></li>
                    <li><a class="block py-1 text-gray-500 hover:text-gold transition-colors border-l-2 border-transparent pl-4" href="#sec13">13. Intellectual Property</a></li>
                    <li><a class="block py-1 text-gray-500 hover:text-gold transition-colors border-l-2 border-transparent pl-4" href="#sec14">14. Privacy</a></li>
                    <li><a class="block py-1 text-gray-500 hover:text-gold transition-colors border-l-2 border-transparent pl-4" href="#sec15">15. Limitation of Liability</a></li>
                    <li><a class="block py-1 text-gray-500 hover:text-gold transition-colors border-l-2 border-transparent pl-4" href="#sec16">16. Service Availability</a></li>
                    <li><a class="block py-1 text-gray-500 hover:text-gold transition-colors border-l-2 border-transparent pl-4" href="#sec17">17. Suspension or Termination</a></li>
                    <li><a class="block py-1 text-gray-500 hover:text-gold transition-colors border-l-2 border-transparent pl-4" href="#sec18">18. Amendments</a></li>
                    <li><a class="block py-1 text-gray-500 hover:text-gold transition-colors border-l-2 border-transparent pl-4" href="#sec19">19. Governing Law</a></li>
                    <li><a class="block py-1 text-gray-500 hover:text-gold transition-colors border-l-2 border-transparent pl-4" href="#sec20">20. Contact Information</a></li>
                </ul>
            </div>
        </aside>

        <!-- Main Document -->
        <article class="flex-1 max-w-3xl">
            <div class="space-y-12">
                <section id="sec1">
                    <h2 class="font-poppins text-2xl font-bold text-navy mb-4">1. Definitions</h2>
                    <p class="font-inter text-gray-600 leading-relaxed">In these Terms of Service, "SkopeStay," "we," "us," or "our" refers to SkopeStay Luxury Hospitality and its affiliates. "Services" refers to the booking platform, hotel accommodations, dining, and recreational facilities provided. "User," "Guest," or "you" refers to any individual accessing our platform or utilizing our physical premises.</p>
                </section>
                <section id="sec2">
                    <h2 class="font-poppins text-2xl font-bold text-navy mb-4">2. Eligibility</h2>
                    <p class="font-inter text-gray-600 leading-relaxed">By using our services, you represent that you are at least 18 years of age and possess the legal authority to enter into this agreement. If you are booking on behalf of a corporate entity, you represent that you have the authority to bind such entity to these terms.</p>
                </section>
                <section id="sec3">
                    <h2 class="font-poppins text-2xl font-bold text-navy mb-4">3. User Accounts</h2>
                    <p class="font-inter text-gray-600 leading-relaxed">To access certain features of the SkopeStay platform, you may be required to create an account. You are responsible for maintaining the confidentiality of your credentials and for all activities that occur under your account. SkopeStay reserves the right to disable any account at our discretion.</p>
                </section>
                <section id="sec4">
                    <h2 class="font-poppins text-2xl font-bold text-navy mb-4">4. Bookings</h2>
                    <p class="font-inter text-gray-600 leading-relaxed">All bookings made through our platform are subject to availability and confirmation. A booking is considered confirmed only upon receipt of a confirmation email and a valid transaction reference. Specific room types or facility requests are subject to on-site availability unless explicitly guaranteed in writing.</p>
                </section>
                <section id="sec5">
                    <h2 class="font-poppins text-2xl font-bold text-navy mb-4">5. Pricing</h2>
                    <p class="font-inter text-gray-600 leading-relaxed">Prices for our services are displayed in the local currency and are inclusive of applicable taxes unless otherwise stated. SkopeStay reserves the right to adjust pricing based on seasonal demand, special events, or administrative errors. The price confirmed at the time of booking will be honored except in cases of obvious technical errors.</p>
                </section>
                <section id="sec6">
                    <h2 class="font-poppins text-2xl font-bold text-navy mb-4">6. Payments</h2>
                    <p class="font-inter text-gray-600 leading-relaxed">Payment must be made via the approved methods listed on our platform. For certain bookings, a deposit or full prepayment may be required. By providing payment information, you authorize SkopeStay to charge the specified amount for the selected services, including any additional incidentals incurred during your stay.</p>
                </section>
                <section id="sec7">
                    <h2 class="font-poppins text-2xl font-bold text-navy mb-4">7. Cancellation and Refunds</h2>
                    <p class="font-inter text-gray-600 leading-relaxed">Cancellation policies vary depending on the rate type and booking category. "Non-refundable" rates are ineligible for refunds. For flexible rates, cancellations must be made within the specified window (typically 48-72 hours prior to check-in) to avoid a penalty fee. Refunds, when applicable, will be processed to the original payment method within 7-14 business days.</p>
                </section>
                <section id="sec8">
                    <h2 class="font-poppins text-2xl font-bold text-navy mb-4">8. Check-In and Check-Out</h2>
                    <p class="font-inter text-gray-600 leading-relaxed">Standard check-in time is 3:00 PM and check-out time is 11:00 AM. Early check-in and late check-out are subject to availability and may incur additional charges. A valid government-issued photo ID and a security deposit (credit card or cash) are required at the time of check-in.</p>
                </section>
                <section id="sec9">
                    <h2 class="font-poppins text-2xl font-bold text-navy mb-4">9. Restaurant Services</h2>
                    <p class="font-inter text-gray-600 leading-relaxed">Dining reservations are recommended. Guests with specific dietary requirements or allergies must inform the staff prior to ordering. SkopeStay is not liable for any reactions if information was not disclosed. Outside food and beverages are generally prohibited in public dining areas.</p>
                </section>
                <section id="sec10">
                    <h2 class="font-poppins text-2xl font-bold text-navy mb-4">10. Hall and Event Bookings</h2>
                    <p class="font-inter text-gray-600 leading-relaxed">Event space bookings require a separate contract detailing capacity, layout, and catering. Any damage caused to the facilities during an event will be the sole responsibility of the booking party. Noise levels must comply with local regulations and hotel policies.</p>
                </section>
                <section id="sec11">
                    <h2 class="font-poppins text-2xl font-bold text-navy mb-4">11. Swimming Pool and Recreation Facilities</h2>
                    <p class="font-inter text-gray-600 leading-relaxed">Use of the swimming pool and fitness center is at the guest's own risk. Proper attire is required. Children must be supervised by an adult at all times. SkopeStay reserves the right to close facilities for maintenance or private events without prior notice.</p>
                </section>
                <section id="sec12">
                    <h2 class="font-poppins text-2xl font-bold text-navy mb-4">12. User Responsibilities</h2>
                    <p class="font-inter text-gray-600 leading-relaxed">Guests are expected to behave in a respectful manner toward staff and other guests. Illegal activities, harassment, or excessive noise are grounds for immediate eviction without refund. Guests are liable for any damage to hotel property caused by themselves or their invitees.</p>
                </section>
                <section id="sec13">
                    <h2 class="font-poppins text-2xl font-bold text-navy mb-4">13. Intellectual Property</h2>
                    <p class="font-inter text-gray-600 leading-relaxed">All content on the SkopeStay platform, including text, graphics, logos, and software, is the property of SkopeStay or its licensors and is protected by intellectual property laws. You may not reproduce or distribute any part of our services without prior written consent.</p>
                </section>
                <section id="sec14">
                    <h2 class="font-poppins text-2xl font-bold text-navy mb-4">14. Privacy</h2>
                    <p class="font-inter text-gray-600 leading-relaxed">Your privacy is important to us. Please refer to our Privacy Policy for details on how we collect, use, and protect your personal information. By using our services, you consent to our data practices as described in the Privacy Policy.</p>
                </section>
                <section id="sec15">
                    <h2 class="font-poppins text-2xl font-bold text-navy mb-4">15. Limitation of Liability</h2>
                    <p class="font-inter text-gray-600 leading-relaxed">To the maximum extent permitted by law, SkopeStay shall not be liable for any indirect, incidental, or consequential damages arising from your use of the services. Our total liability for any claim shall not exceed the amount paid by you for the specific booking in question.</p>
                </section>
                <section id="sec16">
                    <h2 class="font-poppins text-2xl font-bold text-navy mb-4">16. Service Availability</h2>
                    <p class="font-inter text-gray-600 leading-relaxed">While we strive for 100% uptime, our digital platform may be temporarily unavailable for maintenance or due to technical issues beyond our control. Physical facilities may also be restricted due to force majeure events, including extreme weather or public health emergencies.</p>
                </section>
                <section id="sec17">
                    <h2 class="font-poppins text-2xl font-bold text-navy mb-4">17. Suspension or Termination</h2>
                    <p class="font-inter text-gray-600 leading-relaxed">SkopeStay reserves the right to suspend or terminate your access to our services if you violate these terms or engage in any fraudulent or harmful activity. Termination does not waive any accrued rights or liabilities under these terms.</p>
                </section>
                <section id="sec18">
                    <h2 class="font-poppins text-2xl font-bold text-navy mb-4">18. Amendments</h2>
                    <p class="font-inter text-gray-600 leading-relaxed">We may update these Terms of Service from time to time. The updated version will be indicated by the "Effective Date" at the top of the page. Your continued use of the services after any changes constitutes your acceptance of the new terms.</p>
                </section>
                <section id="sec19">
                    <h2 class="font-poppins text-2xl font-bold text-navy mb-4">19. Governing Law</h2>
                    <p class="font-inter text-gray-600 leading-relaxed">These Terms of Service are governed by the laws of the jurisdiction in which the specific property is located, without regard to conflict of law principles. Any disputes shall be resolved in the competent courts of said jurisdiction.</p>
                </section>
                <section id="sec20" class="bg-gray-50 p-8 rounded-3xl border border-gray-200">
                    <h2 class="font-poppins text-2xl font-bold text-navy mb-4">20. Contact Information</h2>
                    <p class="font-inter text-gray-600 leading-relaxed mb-6">If you have any questions or concerns regarding these Terms of Service, please contact our legal and support teams at the following addresses:</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="flex items-center gap-4">
                            <span class="material-symbols-outlined text-gold text-3xl">support_agent</span>
                            <div>
                                <p class="font-montserrat text-xs text-gray-400 uppercase tracking-widest">Customer Support</p>
                                <a class="font-poppins text-navy font-bold hover:text-gold transition-colors" href="mailto:support@skopestay.com">support@skopestay.com</a>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="material-symbols-outlined text-gold text-3xl">gavel</span>
                            <div>
                                <p class="font-montserrat text-xs text-gray-400 uppercase tracking-widest">Legal Department</p>
                                <a class="font-poppins text-navy font-bold hover:text-gold transition-colors" href="mailto:legal@skopestay.com">legal@skopestay.com</a>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </article>
    </main>

<?php include 'includes/public_footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('aside nav a');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (pageYOffset >= (sectionTop - 150)) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('text-gold', 'font-bold', 'border-gold');
                link.classList.add('text-gray-500', 'border-transparent');
                if (link.getAttribute('href').includes(current)) {
                    link.classList.add('text-gold', 'font-bold', 'border-gold');
                    link.classList.remove('text-gray-500', 'border-transparent');
                }
            });
        });
    });
</script>
