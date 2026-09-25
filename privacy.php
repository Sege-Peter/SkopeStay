<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth overflow-x-hidden w-full">
<head>
    <title>Privacy Policy | SkopeStay</title>
<?php include 'includes/public_head.php'; ?>
<style>
    .policy-section:target {
        scroll-margin-top: 100px;
    }
    .active-link {
        border-left: 2px solid #F59E0B; /* gold */
        color: #F59E0B;
        font-weight: 700;
        padding-left: 12px !important;
    }
</style>
</head>
<body class="bg-[#F8FAFC] text-navy relative overflow-x-hidden flex flex-col min-h-screen">
<?php include 'includes/public_header.php'; ?>

    <!-- Hero Section -->
    <section class="relative bg-navy py-16 mt-16 lg:mt-0 overflow-hidden border-b border-gray-800">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1574362848149-11496d93a7c7?q=80&w=2070&auto=format&fit=crop" class="w-full h-full object-cover opacity-20" alt="Privacy Concept">
            <div class="absolute inset-0 bg-gradient-to-t from-navy to-transparent"></div>
        </div>
        <div class="relative z-10 max-w-4xl mx-auto px-6 text-center" data-aos="fade-up">
            <span class="font-montserrat text-gold tracking-widest text-sm font-bold uppercase block mb-4">Security & Compliance</span>
            <h1 class="font-poppins text-5xl font-bold text-white mb-4">Privacy Policy</h1>
            <p class="font-inter text-gray-300 text-lg">Effective Date: June 26, 2026</p>
        </div>
    </section>

    <!-- Main Content Area with Sidebar -->
    <div class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 lg:grid-cols-12 gap-12">
        <!-- Sticky Sidebar Navigation -->
        <aside class="hidden lg:block lg:col-span-3">
            <div class="sticky top-32 flex flex-col gap-2 p-6 bg-white border border-gray-100 rounded-3xl shadow-xl">
                <h3 class="font-poppins font-bold text-navy uppercase tracking-wider pb-4 border-b border-gray-100 mb-2">Sections</h3>
                <nav class="flex flex-col gap-1 font-inter">
                    <a class="text-sm text-gray-600 hover:text-gold px-2 py-2 rounded-lg transition-all" href="#intro">1. Introduction</a>
                    <a class="text-sm text-gray-600 hover:text-gold px-2 py-2 rounded-lg transition-all" href="#collect">2. Information We Collect</a>
                    <a class="text-sm text-gray-600 hover:text-gold px-2 py-2 rounded-lg transition-all" href="#use">3. How We Use Information</a>
                    <a class="text-sm text-gray-600 hover:text-gold px-2 py-2 rounded-lg transition-all" href="#share">4. Sharing Your Data</a>
                    <a class="text-sm text-gray-600 hover:text-gold px-2 py-2 rounded-lg transition-all" href="#cookies">5. Cookies & Tracking</a>
                    <a class="text-sm text-gray-600 hover:text-gold px-2 py-2 rounded-lg transition-all" href="#security">6. Data Security</a>
                    <a class="text-sm text-gray-600 hover:text-gold px-2 py-2 rounded-lg transition-all" href="#retention">7. Data Retention</a>
                    <a class="text-sm text-gray-600 hover:text-gold px-2 py-2 rounded-lg transition-all" href="#rights">8. Your Rights</a>
                    <a class="text-sm text-gray-600 hover:text-gold px-2 py-2 rounded-lg transition-all" href="#international">9. International Transfers</a>
                    <a class="text-sm text-gray-600 hover:text-gold px-2 py-2 rounded-lg transition-all" href="#third-party">10. Third-Party Links</a>
                    <a class="text-sm text-gray-600 hover:text-gold px-2 py-2 rounded-lg transition-all" href="#children">11. Children's Privacy</a>
                    <a class="text-sm text-gray-600 hover:text-gold px-2 py-2 rounded-lg transition-all" href="#changes">12. Changes to Policy</a>
                    <a class="text-sm text-gray-600 hover:text-gold px-2 py-2 rounded-lg transition-all" href="#contact">13. Contact Information</a>
                </nav>
            </div>
        </aside>

        <!-- Document Content -->
        <article class="col-span-1 lg:col-span-9 space-y-12">
            <section class="policy-section bg-white p-10 rounded-3xl border border-gray-100 shadow-xl hover-lift" id="intro" data-aos="fade-up">
                <h2 class="font-poppins text-3xl font-bold text-navy mb-6">1. Introduction</h2>
                <p class="font-inter text-gray-600 leading-relaxed text-lg">
                    At SkopeStay Luxury Hospitality, we value your privacy and are committed to protecting your personal information. This Privacy Policy outlines how we collect, use, and safeguard the data you provide when using our facilities, booking platforms, and digital services. By engaging with SkopeStay, you agree to the practices described herein.
                </p>
            </section>

            <section class="policy-section bg-white p-10 rounded-3xl border border-gray-100 shadow-xl hover-lift" id="collect" data-aos="fade-up">
                <h2 class="font-poppins text-3xl font-bold text-navy mb-6">2. Information We Collect</h2>
                <p class="font-inter text-gray-600 leading-relaxed text-lg mb-6">
                    We collect personal information that you provide voluntarily, such as your name, contact details, identification documents for check-in, and payment information.
                </p>
                <ul class="space-y-4 font-inter text-gray-600 list-disc pl-6 text-lg">
                    <li><strong class="text-navy">Personal Identifiers:</strong> Name, email address, phone number.</li>
                    <li><strong class="text-navy">Transaction Data:</strong> Payment details, booking history, facility usage records.</li>
                    <li><strong class="text-navy">Technical Data:</strong> IP address, device type, browser information when visiting our website.</li>
                </ul>
            </section>

            <section class="policy-section bg-white p-10 rounded-3xl border border-gray-100 shadow-xl hover-lift" id="use" data-aos="fade-up">
                <h2 class="font-poppins text-3xl font-bold text-navy mb-6">3. How We Use Your Information</h2>
                <p class="font-inter text-gray-600 leading-relaxed text-lg">
                    The information we collect is used primarily to provide premium hospitality services, process bookings, manage property operations, and ensure a personalized guest experience. We also use data for internal analytics to improve our service delivery and for security purposes across our properties.
                </p>
            </section>

            <section class="policy-section bg-white p-10 rounded-3xl border border-gray-100 shadow-xl hover-lift" id="share" data-aos="fade-up">
                <h2 class="font-poppins text-3xl font-bold text-navy mb-6">4. Sharing Your Information</h2>
                <p class="font-inter text-gray-600 leading-relaxed text-lg">
                    We do not sell your personal data to third parties. We may share information with trusted service providers who assist us in operations, such as payment processors, IT support, and facility management consultants. Disclosure may also occur if required by law or to protect the safety and rights of SkopeStay and its guests.
                </p>
            </section>

            <section class="policy-section bg-white p-10 rounded-3xl border border-gray-100 shadow-xl hover-lift" id="cookies" data-aos="fade-up">
                <h2 class="font-poppins text-3xl font-bold text-navy mb-6">5. Cookies and Tracking Technologies</h2>
                <p class="font-inter text-gray-600 leading-relaxed text-lg">
                    Our website uses cookies to enhance navigation, analyze site usage, and assist in our marketing efforts. You can manage your cookie preferences through your browser settings, though some site functionalities may be limited if cookies are disabled.
                </p>
            </section>

            <section class="policy-section bg-white p-10 rounded-3xl border border-gray-100 shadow-xl hover-lift" id="security" data-aos="fade-up">
                <h2 class="font-poppins text-3xl font-bold text-navy mb-6">6. Data Security</h2>
                <div class="flex items-start gap-4 mb-6 p-6 bg-gray-50 rounded-2xl border border-gray-200">
                    <span class="material-symbols-outlined text-gold text-3xl">lock</span>
                    <p class="font-inter text-gray-600 text-lg">We employ high-grade encryption and secure physical storage for all sensitive data.</p>
                </div>
                <p class="font-inter text-gray-600 leading-relaxed text-lg">
                    We implement robust technical and organizational measures to protect your data against unauthorized access, loss, or alteration. Our systems are regularly audited to ensure compliance with global data protection standards.
                </p>
            </section>

            <section class="policy-section bg-white p-10 rounded-3xl border border-gray-100 shadow-xl hover-lift" id="retention" data-aos="fade-up">
                <h2 class="font-poppins text-3xl font-bold text-navy mb-6">7. Data Retention</h2>
                <p class="font-inter text-gray-600 leading-relaxed text-lg">
                    We retain your personal information only for as long as necessary to fulfill the purposes for which it was collected, including satisfying legal, accounting, or reporting requirements.
                </p>
            </section>

            <section class="policy-section bg-white p-10 rounded-3xl border border-gray-100 shadow-xl hover-lift" id="rights" data-aos="fade-up">
                <h2 class="font-poppins text-3xl font-bold text-navy mb-6">8. Your Rights</h2>
                <p class="font-inter text-gray-600 leading-relaxed text-lg">
                    You have the right to access, correct, or delete your personal data held by us. You may also object to the processing of your data or request data portability. To exercise these rights, please contact our privacy officer using the details provided below.
                </p>
            </section>

            <section class="policy-section bg-white p-10 rounded-3xl border border-gray-100 shadow-xl hover-lift" id="international" data-aos="fade-up">
                <h2 class="font-poppins text-3xl font-bold text-navy mb-6">9. International Data Transfers</h2>
                <p class="font-inter text-gray-600 leading-relaxed text-lg">
                    As a luxury hospitality group operating globally, your data may be transferred to and maintained on servers located outside your home country. We ensure that such transfers comply with applicable data protection laws.
                </p>
            </section>

            <section class="policy-section bg-white p-10 rounded-3xl border border-gray-100 shadow-xl hover-lift" id="third-party" data-aos="fade-up">
                <h2 class="font-poppins text-3xl font-bold text-navy mb-6">10. Third-Party Links</h2>
                <p class="font-inter text-gray-600 leading-relaxed text-lg">
                    Our services may contain links to third-party websites. We are not responsible for the privacy practices or content of these external sites and encourage you to review their policies separately.
                </p>
            </section>

            <section class="policy-section bg-white p-10 rounded-3xl border border-gray-100 shadow-xl hover-lift" id="children" data-aos="fade-up">
                <h2 class="font-poppins text-3xl font-bold text-navy mb-6">11. Children's Privacy</h2>
                <p class="font-inter text-gray-600 leading-relaxed text-lg">
                    Our services are not intended for children under the age of 18. We do not knowingly collect personal data from minors without explicit parental consent.
                </p>
            </section>

            <section class="policy-section bg-white p-10 rounded-3xl border border-gray-100 shadow-xl hover-lift" id="changes" data-aos="fade-up">
                <h2 class="font-poppins text-3xl font-bold text-navy mb-6">12. Changes to This Policy</h2>
                <p class="font-inter text-gray-600 leading-relaxed text-lg">
                    We may update this Privacy Policy from time to time to reflect changes in our practices or for other operational, legal, or regulatory reasons. The 'Last Updated' date will be adjusted accordingly.
                </p>
            </section>

            <section class="policy-section bg-navy p-10 rounded-3xl shadow-xl text-white hover-lift" id="contact" data-aos="fade-up">
                <h2 class="font-poppins text-3xl font-bold text-gold mb-6">13. Contact Information</h2>
                <p class="font-inter text-gray-300 leading-relaxed text-lg mb-8">
                    If you have any questions or concerns about this Privacy Policy or our data handling practices, please reach out to us:
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white/5 p-6 rounded-2xl border border-white/10 flex items-center gap-6">
                        <span class="material-symbols-outlined text-gold text-4xl">support_agent</span>
                        <div>
                            <p class="font-montserrat text-xs text-gray-400 uppercase tracking-widest mb-1">Support</p>
                            <a class="font-poppins font-bold text-lg text-white hover:text-gold transition-colors" href="mailto:support@skopestay.com">support@skopestay.com</a>
                        </div>
                    </div>
                    <div class="bg-white/5 p-6 rounded-2xl border border-white/10 flex items-center gap-6">
                        <span class="material-symbols-outlined text-gold text-4xl">policy</span>
                        <div>
                            <p class="font-montserrat text-xs text-gray-400 uppercase tracking-widest mb-1">Privacy Officer</p>
                            <a class="font-poppins font-bold text-lg text-white hover:text-gold transition-colors" href="mailto:privacy@skopestay.com">privacy@skopestay.com</a>
                        </div>
                    </div>
                </div>
            </section>
        </article>
    </div>

<?php include 'includes/public_footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sections = document.querySelectorAll('.policy-section');
        const navLinks = document.querySelectorAll('aside nav a');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (pageYOffset >= (sectionTop - 200)) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active-link');
                if (link.getAttribute('href').includes(current)) {
                    link.classList.add('active-link');
                }
            });
        });
    });
</script>
