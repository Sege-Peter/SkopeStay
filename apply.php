<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth overflow-x-hidden w-full">
<head>
    <title>Apply | SkopeStay Careers</title>
<?php include 'includes/public_head.php'; ?>
<style>
    .form-input-focus {
        transition: all 0.2s ease-in-out;
    }
    .form-input-focus:focus {
        box-shadow: 0 0 0 2px #F59E0B;
        border-color: #F59E0B;
    }
    .drop-zone {
        border: 2px dashed #E2E8F0;
        transition: background 0.3s ease;
    }
    .drop-zone:hover {
        border-color: #F59E0B;
        background: #FFFBEB; /* very light gold */
    }
</style>
</head>
<body class="bg-[#F8FAFC] text-navy relative overflow-x-hidden flex flex-col min-h-screen">
<?php include 'includes/public_header.php'; ?>

    <main class="flex-grow w-full max-w-7xl mx-auto px-6 py-16 mt-16 lg:mt-0">
        <!-- Hero Section -->
        <section class="mb-12 text-center md:text-left" data-aos="fade-up">
            <h1 class="font-poppins text-4xl md:text-5xl font-bold text-navy mb-4">Join Our Team</h1>
            <p class="font-inter text-gray-600 max-w-2xl text-lg">We're looking for passionate individuals to redefine professional stays. Submit your application below.</p>
        </section>

        <!-- Multi-Section Form -->
        <form class="space-y-8" id="applicationForm" action="apply_success.php" method="POST">
            
            <!-- Section 1 & 2 Wrapper -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Section 1: Personal Information -->
                <div class="lg:col-span-2 bg-white p-8 rounded-3xl border border-gray-100 shadow-xl hover-lift" data-aos="fade-up">
                    <div class="flex items-center gap-3 mb-8 border-b border-gray-100 pb-4">
                        <span class="material-symbols-outlined text-gold text-3xl">person</span>
                        <h2 class="font-poppins text-2xl font-bold text-navy">Personal Information</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 font-inter">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Full Name</label>
                            <input class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 form-input-focus outline-none text-navy" placeholder="John Doe" type="text" required/>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Date of Birth</label>
                            <input class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 form-input-focus outline-none text-navy" type="date" required/>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Gender</label>
                            <select class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 form-input-focus outline-none text-navy" required>
                                <option value="">Select</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Prefer not to say">Prefer not to say</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Nationality</label>
                            <input class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 form-input-focus outline-none text-navy" placeholder="e.g. Canadian" type="text" required/>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">ID / Passport</label>
                            <input class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 form-input-focus outline-none text-navy" placeholder="A12345678" type="text" required/>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Email Address</label>
                            <input class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 form-input-focus outline-none text-navy" placeholder="john@example.com" type="email" required/>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Phone Number</label>
                            <input class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 form-input-focus outline-none text-navy" placeholder="+1 (555) 000-0000" type="tel" required/>
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Residential Address</label>
                            <textarea class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 form-input-focus outline-none text-navy resize-none" placeholder="123 Luxury Ave, Manhattan, NY" rows="2" required></textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Position Information -->
                <div class="bg-navy p-8 rounded-3xl border border-navy shadow-xl hover-lift text-white" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center gap-3 mb-8 border-b border-white/10 pb-4">
                        <span class="material-symbols-outlined text-gold text-3xl">work</span>
                        <h2 class="font-poppins text-2xl font-bold">Position</h2>
                    </div>
                    <div class="space-y-6 font-inter">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Target Position</label>
                            <select class="w-full bg-white/10 border border-white/20 rounded-xl p-3 form-input-focus outline-none text-white [&>option]:text-navy" required>
                                <option value="Facility Manager">Facility Manager</option>
                                <option value="Corporate Booking Lead">Corporate Booking Lead</option>
                                <option value="Software Engineer (Full Stack)">Software Engineer (Full Stack)</option>
                                <option value="UX Designer">UX Designer</option>
                                <option value="Guest Relations Specialist">Guest Relations Specialist</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Department</label>
                            <div class="p-3 bg-white/5 border border-white/10 rounded-xl flex items-center gap-2">
                                <span class="material-symbols-outlined text-gray-400 text-sm">corporate_fare</span>
                                <span>Operations / Tech</span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Employment Type</label>
                            <select class="w-full bg-white/10 border border-white/20 rounded-xl p-3 form-input-focus outline-none text-white [&>option]:text-navy" required>
                                <option value="Full-Time">Full-Time</option>
                                <option value="Part-Time">Part-Time</option>
                                <option value="Contract">Contract</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Expected Salary ($)</label>
                            <input class="w-full bg-white/10 border border-white/20 rounded-xl p-3 form-input-focus outline-none text-white placeholder-gray-400" placeholder="e.g. 5000" type="number" required/>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Earliest Start Date</label>
                            <input class="w-full bg-white/10 border border-white/20 rounded-xl p-3 form-input-focus outline-none text-white" type="date" required/>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3 & 4 Wrapper -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Education & Experience -->
                <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-xl hover-lift" data-aos="fade-up">
                    <div class="flex items-center gap-3 mb-8 border-b border-gray-100 pb-4">
                        <span class="material-symbols-outlined text-gold text-3xl">school</span>
                        <h2 class="font-poppins text-2xl font-bold text-navy">Education & Experience</h2>
                    </div>
                    <div class="space-y-8 font-inter">
                        <div class="p-6 bg-gray-50 border border-gray-200 rounded-2xl border-l-4 border-l-gold">
                            <p class="font-bold text-navy mb-4">Highest Qualification</p>
                            <div class="grid grid-cols-2 gap-4">
                                <input class="col-span-2 w-full bg-white border border-gray-200 rounded-xl p-3 outline-none focus:border-gold transition-colors" placeholder="Institution" type="text" required/>
                                <input class="w-full bg-white border border-gray-200 rounded-xl p-3 outline-none focus:border-gold transition-colors" placeholder="Course/Major" type="text" required/>
                                <input class="w-full bg-white border border-gray-200 rounded-xl p-3 outline-none focus:border-gold transition-colors" placeholder="Year Graduated" type="text" required/>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Work Experience Summary</label>
                            <textarea class="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 form-input-focus outline-none text-navy resize-none" placeholder="Briefly describe your last two roles and achievements..." rows="4" required></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Professional Skills -->
                <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-xl hover-lift" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center gap-3 mb-8 border-b border-gray-100 pb-4">
                        <span class="material-symbols-outlined text-gold text-3xl">psychology</span>
                        <h2 class="font-poppins text-2xl font-bold text-navy">Professional Skills</h2>
                    </div>
                    <div class="grid grid-cols-2 gap-4 font-inter">
                        <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors hover:border-gold">
                            <input class="w-5 h-5 rounded border-gray-300 text-gold focus:ring-gold" type="checkbox"/>
                            <span class="text-navy">Customer Service</span>
                        </label>
                        <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors hover:border-gold">
                            <input class="w-5 h-5 rounded border-gray-300 text-gold focus:ring-gold" type="checkbox"/>
                            <span class="text-navy">Software Dev</span>
                        </label>
                        <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors hover:border-gold">
                            <input class="w-5 h-5 rounded border-gray-300 text-gold focus:ring-gold" type="checkbox"/>
                            <span class="text-navy">Facility Mgmt</span>
                        </label>
                        <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors hover:border-gold">
                            <input class="w-5 h-5 rounded border-gray-300 text-gold focus:ring-gold" type="checkbox"/>
                            <span class="text-navy">Data Analysis</span>
                        </label>
                        <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors hover:border-gold">
                            <input class="w-5 h-5 rounded border-gray-300 text-gold focus:ring-gold" type="checkbox"/>
                            <span class="text-navy">Team Leadership</span>
                        </label>
                        <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors hover:border-gold">
                            <input class="w-5 h-5 rounded border-gray-300 text-gold focus:ring-gold" type="checkbox"/>
                            <span class="text-navy">CRM Proficient</span>
                        </label>
                        <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors hover:border-gold">
                            <input class="w-5 h-5 rounded border-gray-300 text-gold focus:ring-gold" type="checkbox"/>
                            <span class="text-navy">Project Mgmt</span>
                        </label>
                        <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors hover:border-gold">
                            <input class="w-5 h-5 rounded border-gray-300 text-gold focus:ring-gold" type="checkbox"/>
                            <span class="text-navy">Hospitality Ops</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Section 5: Document Upload -->
            <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-xl hover-lift" data-aos="fade-up">
                <div class="flex items-center gap-3 mb-8 border-b border-gray-100 pb-4">
                    <span class="material-symbols-outlined text-gold text-3xl">upload_file</span>
                    <h2 class="font-poppins text-2xl font-bold text-navy">Document Upload</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 font-inter">
                    <div class="drop-zone flex flex-col items-center justify-center p-8 rounded-2xl text-center cursor-pointer">
                        <span class="material-symbols-outlined text-gray-400 text-4xl mb-3">description</span>
                        <p class="font-bold text-navy">Resume/CV</p>
                        <p class="text-xs text-gray-500">PDF, DOC (Max 5MB)</p>
                    </div>
                    <div class="drop-zone flex flex-col items-center justify-center p-8 rounded-2xl text-center cursor-pointer">
                        <span class="material-symbols-outlined text-gray-400 text-4xl mb-3">history_edu</span>
                        <p class="font-bold text-navy">Cover Letter</p>
                        <p class="text-xs text-gray-500">PDF (Max 2MB)</p>
                    </div>
                    <div class="drop-zone flex flex-col items-center justify-center p-8 rounded-2xl text-center cursor-pointer">
                        <span class="material-symbols-outlined text-gray-400 text-4xl mb-3">badge</span>
                        <p class="font-bold text-navy">Certificates</p>
                        <p class="text-xs text-gray-500">ZIP or PDF</p>
                    </div>
                    <div class="drop-zone flex flex-col items-center justify-center p-8 rounded-2xl text-center cursor-pointer">
                        <span class="material-symbols-outlined text-gray-400 text-4xl mb-3">attachment</span>
                        <p class="font-bold text-navy">Portfolio</p>
                        <p class="text-xs text-gray-500">URL or PDF</p>
                    </div>
                </div>
            </div>

            <!-- Section 6: Declaration -->
            <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-xl hover-lift" data-aos="fade-up">
                <div class="space-y-4 font-inter text-gray-600">
                    <label class="flex items-start gap-4 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors">
                        <input class="mt-1 w-5 h-5 rounded border-gray-300 text-gold focus:ring-gold" required="" type="checkbox"/>
                        <span class="text-sm">I hereby declare that the information provided above is true and correct to the best of my knowledge and belief. I understand that any false statement may disqualify me from the recruitment process.</span>
                    </label>
                    <label class="flex items-start gap-4 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors">
                        <input class="mt-1 w-5 h-5 rounded border-gray-300 text-gold focus:ring-gold" required="" type="checkbox"/>
                        <span class="text-sm">I have read and agree to the <a class="text-gold font-bold hover:underline" href="privacy.php">Privacy Policy</a> regarding the collection and storage of my personal data.</span>
                    </label>
                    <label class="flex items-start gap-4 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors">
                        <input class="mt-1 w-5 h-5 rounded border-gray-300 text-gold focus:ring-gold" type="checkbox"/>
                        <span class="text-sm">I consent to being contacted by SkopeStay's recruitment team via email or phone for interview purposes.</span>
                    </label>
                </div>
                
                <div class="mt-10 pt-8 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-3 text-gray-500">
                        <span class="material-symbols-outlined text-green-500">verified_user</span>
                        <p class="text-sm font-medium">Your data is secured with enterprise-grade encryption.</p>
                    </div>
                    <button type="submit" class="w-full md:w-auto bg-navy text-white px-10 py-4 rounded-full font-poppins font-bold text-lg hover:bg-gold transition-colors shadow-[0_0_20px_rgba(245,158,11,0.2)] hover:shadow-[0_0_20px_rgba(245,158,11,0.6)]">
                        Submit Application
                    </button>
                </div>
            </div>
        </form>
    </main>

<?php include 'includes/public_footer.php'; ?>

<script>
    document.getElementById('applicationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Application Submitted!',
            text: 'Thank you for applying. Our recruitment team will review your application and get back to you shortly.',
            icon: 'success',
            confirmButtonColor: '#0F172A',
            confirmButtonText: 'Return to Careers'
        }).then((result) => {
            window.location.href = 'careers.php';
        });
    });

    const zones = document.querySelectorAll('.drop-zone');
    zones.forEach(zone => {
        zone.addEventListener('dragover', (e) => { e.preventDefault(); zone.style.background = '#FFFBEB'; });
        zone.addEventListener('dragleave', (e) => { e.preventDefault(); zone.style.background = 'transparent'; });
        zone.addEventListener('drop', (e) => {
            e.preventDefault();
            zone.innerHTML = `<span class="material-symbols-outlined text-green-500 text-4xl mb-3">check_circle</span><p class="font-bold text-green-500">File Uploaded</p>`;
            zone.style.borderColor = '#10B981';
        });
    });
</script>
