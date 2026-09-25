<!DOCTYPE html>
<html lang="en">
<?php $page_title = "Login | SkopeStay"; include '../includes/head.php'; ?>
<body class="bg-gray-50 min-h-screen flex items-center justify-center font-sans antialiased selection:bg-gray-200">

    <div class="w-full max-w-sm px-6">
        <!-- Minimalistic Header -->
        <div class="text-center mb-12">
            <a href="../index.php" class="inline-block mb-6 text-gray-400 hover:text-gray-900 transition-colors">
                <span class="material-symbols-outlined text-2xl">arrow_back</span>
            </a>
            <h1 class="text-3xl font-light text-gray-900 tracking-tight">SkopeStay</h1>
            <p class="text-sm text-gray-500 mt-3 font-light">Sign in to your account</p>
        </div>

        <!-- Form -->
        <form action="../modules/dashboard.php" method="GET" id="loginForm" class="space-y-6">
            
            <div class="space-y-1">
                <input class="w-full px-0 py-3 bg-transparent border-0 border-b border-gray-300 focus:ring-0 focus:border-gray-900 text-sm text-gray-900 placeholder-gray-400 transition-colors outline-none" 
                       id="username" name="username" placeholder="Username or Email" type="text" autocomplete="username" required/>
            </div>
            
            <div class="space-y-1 relative">
                <input class="w-full px-0 py-3 bg-transparent border-0 border-b border-gray-300 focus:ring-0 focus:border-gray-900 text-sm text-gray-900 placeholder-gray-400 transition-colors outline-none pr-10" 
                       id="password" name="password" placeholder="Password" type="password" autocomplete="current-password" required/>
                <button type="button" class="absolute right-0 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-900 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                </button>
            </div>

            <div class="flex items-center justify-between pt-2">
                <div class="flex items-center gap-2">
                    <input class="w-3.5 h-3.5 text-gray-900 bg-transparent border-gray-300 rounded-sm focus:ring-gray-900 focus:ring-offset-gray-50 cursor-pointer" 
                           id="remember" name="remember" type="checkbox"/>
                    <label class="text-xs text-gray-500 cursor-pointer hover:text-gray-900 transition-colors" for="remember">Remember me</label>
                </div>
                <a class="text-xs text-gray-500 hover:text-gray-900 transition-colors" href="#">Forgot Password?</a>
            </div>

            <button class="w-full py-3.5 mt-6 bg-gray-900 text-white text-sm tracking-wide rounded hover:bg-black active:scale-[0.98] transition-all flex justify-center items-center gap-2" type="submit">
                <span>Sign In</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </button>
            
            <div class="text-center pt-8">
                <a href="signup.php" class="text-xs text-gray-500 hover:text-gray-900 transition-colors">Create an account</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[16px]">sync</span><span class="ml-2">Signing In...</span>';
            btn.classList.add('opacity-80', 'cursor-wait');
            btn.disabled = true;
            
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
                    btn.innerHTML = '<span class="material-symbols-outlined text-[16px]">check</span><span class="ml-2">Success</span>';
                    setTimeout(() => window.location.href = '../modules/dashboard.php', 500);
                } else {
                    if(window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Authentication Failed',
                            text: json.message,
                            confirmButtonColor: '#111827'
                        });
                    } else {
                        alert(json.message);
                    }
                    btn.innerHTML = originalText;
                    btn.classList.remove('opacity-80', 'cursor-wait');
                    btn.disabled = false;
                }
            } catch (err) {
                if(window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: 'Unable to reach the server. Please check your connection.',
                        confirmButtonColor: '#111827'
                    });
                } else {
                    alert('Network Error');
                }
                btn.innerHTML = originalText;
                btn.classList.remove('opacity-80', 'cursor-wait');
                btn.disabled = false;
            }
        });

        // Toggle Password Visibility
        const togglePass = document.querySelector('button[type="button"]');
        const passInput = document.getElementById('password');
        togglePass.addEventListener('click', () => {
            const type = passInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passInput.setAttribute('type', type);
            togglePass.querySelector('span').textContent = type === 'password' ? 'visibility' : 'visibility_off';
        });
    </script>
</body>
</html>
