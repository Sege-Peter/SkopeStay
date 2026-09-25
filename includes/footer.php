<footer class="flex flex-col md:flex-row justify-between items-center gap-4 w-full px-4 md:px-margin-desktop py-4 bg-surface-container text-on-surface-variant font-caption text-caption border-t border-outline-variant mt-auto text-center md:text-left">
    <div class="flex flex-col md:flex-row items-center gap-2 md:gap-4">
        <img src="../assets/images/SkopeStay logo.png" alt="SkopeStay Logo"
             style="width:28px; height:28px; border-radius:50px; object-fit:cover;">
        <span class="font-bold text-on-surface font-label-md text-label-md">SkopeStay</span>
        <span>&copy; <span id="current-year"></span> SkopeStay v2.1.0</span>
    </div>
    <div class="flex flex-wrap justify-center gap-4 md:gap-lg">
        <a class="text-on-surface-variant hover:text-primary transition-colors" href="mailto:support@skopestay.com">Support</a>
        <a class="text-on-surface-variant hover:text-primary transition-colors" href="../privacy.php">Privacy Policy</a>
        <a class="text-on-surface-variant hover:text-primary transition-colors" href="../terms.php">Terms of Service</a>
    </div>
</footer>

<script>
    // Dynamically set current year for real year accuracy
    const yearSpan = document.getElementById('current-year');
    if (yearSpan) {
        yearSpan.textContent = new Date().getFullYear();
    }
</script>
