<!DOCTYPE html>

<html class="light" lang="en"><?php $page_title = "Sign Up | SkopeStay"; include '../includes/head.php'; ?>
<body class="bg-background text-on-background font-body-md min-h-screen flex items-center justify-center overflow-hidden">
<main class="w-full h-screen flex flex-col md:flex-row">
<!-- Left Side: Image Slider -->
<section class="hidden md:flex relative w-1/2 h-full bg-primary-container overflow-hidden">
<div class="absolute inset-0" id="slider">
<div class="slider-image absolute inset-0 opacity-100 bg-cover bg-center" data-alt="A sprawling luxury resort at sunset featuring a glowing infinity pool." style="background-image: url('https://images.unsplash.com/photo-1571896349842-33c89424de2d?q=80&w=2080&auto=format&fit=crop')"></div>
</div>
<!-- Branding Overlay -->
<div class="absolute inset-0 bg-gradient-to-t from-primary-container/80 via-transparent to-transparent z-10"></div>
<div class="absolute bottom-12 left-12 z-20 max-w-md">
<h2 class="font-display-lg text-display-lg text-white mb-4">Join SkopeStay.</h2>
<p class="font-body-lg text-body-lg text-primary-fixed-dim">Experience seamless hospitality and facility management at your fingertips.</p>
</div>
</section>
<!-- Right Side: Signup Form -->
<section class="w-full md:w-1/2 h-full flex flex-col justify-center items-center p-8 bg-surface-container-lowest overflow-y-auto">
<div class="w-full max-w-md space-y-8 py-8">
<!-- Back to Homepage -->
<a href="../index.php" class="inline-flex items-center gap-2 text-sm font-semibold text-on-surface-variant hover:text-primary transition-colors group">
    <span class="material-symbols-outlined text-[18px] group-hover:-translate-x-1 transition-transform">arrow_back</span>
    Back to Homepage
</a>
<!-- Logo & Heading -->
<div class="flex flex-col items-start space-y-4">
<div class="flex items-center gap-3">
<img src="../assets/images/SkopeStay logo.png" alt="SkopeStay Logo"
     style="width:40px; height:40px; border-radius:50px; object-fit:cover;">
<span class="font-headline-md text-headline-md font-bold text-primary">SkopeStay</span>
</div>
<div class="space-y-1">
<h1 class="font-title-lg text-title-lg text-on-surface">Create an Account</h1>
<p class="font-body-md text-on-surface-variant">Sign up to access exclusive guest and corporate features.</p>
</div>
</div>
<!-- Form -->
<form action="../api/auth.php" method="POST" class="space-y-5" id="signupForm">
<input type="hidden" name="action" value="signup">

<div class="space-y-2">
<label class="font-label-md text-label-md text-on-surface-variant block" for="full_name">FULL NAME *</label>
<div class="relative group">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline-variant group-focus-within:text-primary transition-colors">badge</span>
<input class="w-full pl-10 pr-4 py-3 bg-white border border-outline-variant rounded-lg font-body-md text-on-surface focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none" id="full_name" name="full_name" placeholder="John Doe" type="text" required/>
</div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="space-y-2">
    <label class="font-label-md text-label-md text-on-surface-variant block" for="username">USERNAME *</label>
    <div class="relative group">
    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline-variant group-focus-within:text-primary transition-colors">person</span>
    <input class="w-full pl-10 pr-4 py-3 bg-white border border-outline-variant rounded-lg font-body-md text-on-surface focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none" id="username" name="username" placeholder="johndoe" type="text" required/>
    </div>
    </div>

    <div class="space-y-2">
    <label class="font-label-md text-label-md text-on-surface-variant block" for="email">EMAIL *</label>
    <div class="relative group">
    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline-variant group-focus-within:text-primary transition-colors">mail</span>
    <input class="w-full pl-10 pr-4 py-3 bg-white border border-outline-variant rounded-lg font-body-md text-on-surface focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none" id="email" name="email" placeholder="john@example.com" type="email" required/>
    </div>
    </div>
</div>

<div class="space-y-2">
<label class="font-label-md text-label-md text-on-surface-variant block" for="password">PASSWORD *</label>
<div class="relative group">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline-variant group-focus-within:text-primary transition-colors">lock</span>
<input class="w-full pl-10 pr-12 py-3 bg-white border border-outline-variant rounded-lg font-body-md text-on-surface focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none" id="password" name="password" placeholder="••••••••" type="password" required/>
<button class="absolute right-3 top-1/2 -translate-y-1/2 text-outline-variant hover:text-on-surface-variant" type="button" id="togglePass">
<span class="material-symbols-outlined">visibility</span>
</button>
</div>
</div>

<button class="w-full py-4 bg-primary text-white font-title-lg rounded-lg shadow-lg hover:shadow-xl hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2 mt-2" type="submit">
                        Create Account
                        <span class="material-symbols-outlined">person_add</span>
</button>

<div class="mt-6 text-center text-on-surface-variant font-body-md">
    Already have an account? <a href="login.php" class="text-primary font-bold hover:underline transition-all">Sign In</a>
</div>
</form>

<!-- Footer Info -->
<div class="pt-8 mt-4 border-t border-outline-variant/30 flex flex-col sm:flex-row justify-between items-center gap-4">
<div class="flex items-center gap-4 text-outline">
<a class="font-caption text-caption hover:text-primary transition-colors" href="mailto:support@skopestay.com">Support</a>
<a class="font-caption text-caption hover:text-primary transition-colors" href="../terms.php">Terms of Service</a>
</div>
<div class="font-caption text-caption text-outline">
                        SkopeStay v2.1.0
                    </div>
</div>
</div>
</section>
</main>
<script>
        // Form Submission Micro-interaction
        document.getElementById('signupForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="material-symbols-outlined animate-spin">sync</span> Creating...';
            btn.classList.add('opacity-80', 'cursor-not-allowed');
            
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
                    btn.innerHTML = '<span class="material-symbols-outlined">check_circle</span> Success';
                    btn.classList.remove('bg-primary');
                    btn.classList.add('bg-green-600');
                    Swal.fire({
                        icon: 'success',
                        title: 'Welcome!',
                        text: json.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = 'login.php';
                    });
                } else {
                    Swal.fire('Error', json.message, 'error');
                    btn.innerHTML = originalText;
                    btn.classList.remove('opacity-80', 'cursor-not-allowed');
                }
            } catch (err) {
                Swal.fire('Error', 'Network connection failed.', 'error');
                btn.innerHTML = originalText;
                btn.classList.remove('opacity-80', 'cursor-not-allowed');
            }
        });

        // Toggle Password Visibility
        const togglePass = document.getElementById('togglePass');
        const passInput = document.getElementById('password');
        togglePass.addEventListener('click', () => {
            const type = passInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passInput.setAttribute('type', type);
            togglePass.querySelector('span').textContent = type === 'password' ? 'visibility' : 'visibility_off';
        });
    </script>
</body></html>
