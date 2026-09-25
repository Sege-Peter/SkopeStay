<!-- SkopeStay Multi-Step Payment Modal -->
<!-- Dependencies: TailwindCSS, canvas-confetti -->
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<style>
    /* Glassmorphism & Animations */
    .payment-modal-backdrop {
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }
    .payment-modal-container {
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    
    .payment-step {
        display: none;
        animation: slideInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .payment-step.active {
        display: block;
    }

    @keyframes slideInUp {
        0% { opacity: 0; transform: translateY(20px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    .method-card {
        transition: all 0.2s ease;
    }
    .method-card:hover {
        transform: translateY(-2px);
        border-color: #2563eb;
        background: #eff6ff;
    }
    .method-card.selected {
        border-color: #2563eb;
        background: #eff6ff;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
    }

    /* Loading Spinner */
    .spinner-ring {
        display: inline-block;
        width: 40px;
        height: 40px;
    }
    .spinner-ring:after {
        content: " ";
        display: block;
        width: 32px;
        height: 32px;
        margin: 4px;
        border-radius: 50%;
        border: 3px solid #2563eb;
        border-color: #2563eb transparent #2563eb transparent;
        animation: spinner-ring 1.2s linear infinite;
    }
    @keyframes spinner-ring {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Success Checkmark Animation */
    .success-checkmark {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: block;
        stroke-width: 2;
        stroke: #22c55e;
        stroke-miterlimit: 10;
        box-shadow: inset 0px 0px 0px #22c55e;
        animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
        margin: 0 auto 20px auto;
    }
    .success-checkmark__circle {
        stroke-dasharray: 166;
        stroke-dashoffset: 166;
        stroke-width: 2;
        stroke-miterlimit: 10;
        stroke: #22c55e;
        fill: none;
        animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    }
    .success-checkmark__check {
        transform-origin: 50% 50%;
        stroke-dasharray: 48;
        stroke-dashoffset: 48;
        animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
    }
    @keyframes stroke { 100% { stroke-dashoffset: 0; } }
    @keyframes scale { 0%, 100% { transform: none; } 50% { transform: scale3d(1.1, 1.1, 1); } }
    @keyframes fill { 100% { box-shadow: inset 0px 0px 0px 30px transparent; } }

</style>

<div id="payment-modal-wrapper" class="fixed inset-0 z-[999] hidden items-center justify-center payment-modal-backdrop opacity-0 transition-opacity duration-300">
    <div class="payment-modal-container w-full max-w-lg rounded-3xl overflow-hidden relative m-4 flex flex-col max-h-[90vh]">
        
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-white/50 sticky top-0 z-10">
            <div>
                <h3 class="font-bold text-xl text-gray-900" id="pm-title">Complete Payment</h3>
                <p class="text-xs text-gray-500 mt-0.5" id="pm-subtitle">Choose your preferred method</p>
            </div>
            <button onclick="closePaymentModal()" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-200 hover:text-gray-900 transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <div class="p-6 overflow-y-auto flex-1">
            
            <!-- Step 1: Summary & Select Method -->
            <div id="pm-step-1" class="payment-step active">
                <div class="bg-blue-50 rounded-2xl p-6 mb-6 text-center border border-blue-100">
                    <p class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-1">Amount Due</p>
                    <h2 class="text-4xl font-extrabold text-blue-900" id="pm-amount-display">KSh 0</h2>
                </div>

                <p class="text-sm font-bold text-gray-700 mb-3">Select Payment Method</p>
                <div class="space-y-3">
                    <label class="method-card flex items-center justify-between p-4 rounded-xl border border-gray-200 cursor-pointer">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center">
                                <span class="material-symbols-outlined">phone_iphone</span>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 text-sm">M-Pesa</p>
                                <p class="text-xs text-gray-500">Pay via STK Push</p>
                            </div>
                        </div>
                        <input type="radio" name="pay_method" value="M-Pesa" class="w-5 h-5 text-blue-600 focus:ring-blue-500" onchange="highlightMethod(this)">
                    </label>

                    <label class="method-card flex items-center justify-between p-4 rounded-xl border border-gray-200 cursor-pointer">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                <span class="material-symbols-outlined">credit_card</span>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 text-sm">Credit / Debit Card</p>
                                <p class="text-xs text-gray-500">Visa, Mastercard</p>
                            </div>
                        </div>
                        <input type="radio" name="pay_method" value="Card" class="w-5 h-5 text-blue-600 focus:ring-blue-500" onchange="highlightMethod(this)">
                    </label>

                    <label class="method-card flex items-center justify-between p-4 rounded-xl border border-gray-200 cursor-pointer">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <span class="material-symbols-outlined">payments</span>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 text-sm">Cash</p>
                                <p class="text-xs text-gray-500">Pay at counter</p>
                            </div>
                        </div>
                        <input type="radio" name="pay_method" value="Cash" class="w-5 h-5 text-blue-600 focus:ring-blue-500" onchange="highlightMethod(this)">
                    </label>

                    <label class="method-card flex items-center justify-between p-4 rounded-xl border border-gray-200 cursor-pointer">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center">
                                <span class="material-symbols-outlined">account_balance</span>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 text-sm">Bank Transfer</p>
                                <p class="text-xs text-gray-500">EFT / RTGS</p>
                            </div>
                        </div>
                        <input type="radio" name="pay_method" value="Bank" class="w-5 h-5 text-blue-600 focus:ring-blue-500" onchange="highlightMethod(this)">
                    </label>

                    <label id="method-room-charge" class="method-card flex items-center justify-between p-4 rounded-xl border border-gray-200 cursor-pointer hidden">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                                <span class="material-symbols-outlined">room_preferences</span>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 text-sm">Charge to Room</p>
                                <p class="text-xs text-gray-500">Add to guest folio</p>
                            </div>
                        </div>
                        <input type="radio" name="pay_method" value="ChargeToRoom" class="w-5 h-5 text-blue-600 focus:ring-blue-500" onchange="highlightMethod(this)">
                    </label>
                </div>
            </div>

            <!-- Step 2A: M-Pesa -->
            <div id="pm-step-mpesa" class="payment-step">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-3"><span class="material-symbols-outlined text-3xl">phone_iphone</span></div>
                    <h4 class="font-bold text-lg">M-Pesa STK Push</h4>
                    <p class="text-sm text-gray-500">Enter phone number to receive prompt</p>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Phone Number</label>
                    <input type="text" id="mpesa-phone" placeholder="07XX XXX XXX" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-lg font-bold text-center focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
                </div>
            </div>

            <!-- Step 2B: Card -->
            <div id="pm-step-card" class="payment-step">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-3"><span class="material-symbols-outlined text-3xl">credit_card</span></div>
                    <h4 class="font-bold text-lg">Secure Card Payment</h4>
                    <p class="text-sm text-gray-500">PCI DSS Compliant</p>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Card Number</label>
                        <input type="text" placeholder="XXXX XXXX XXXX XXXX" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-mono focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Expiry Date</label>
                            <input type="text" placeholder="MM / YY" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-mono text-center focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">CVV</label>
                            <input type="password" placeholder="***" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-mono text-center focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Cardholder Name</label>
                        <input type="text" placeholder="JOHN DOE" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 outline-none uppercase">
                    </div>
                </div>
            </div>

            <!-- Step 2C: Cash -->
            <div id="pm-step-cash" class="payment-step">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3"><span class="material-symbols-outlined text-3xl">payments</span></div>
                    <h4 class="font-bold text-lg">Cash Calculator</h4>
                </div>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-sm font-bold text-gray-500">Amount Due:</span>
                        <span class="text-xl font-extrabold text-gray-900" id="cash-due">KSh 0</span>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Amount Received (KSh)</label>
                        <input type="number" id="cash-received" oninput="calculateChange()" placeholder="0" class="w-full bg-gray-50 border border-emerald-200 rounded-xl px-4 py-3 text-2xl font-bold text-center focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div class="bg-gray-100 rounded-xl p-4 text-center mt-4">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Change to Return</p>
                        <p class="text-3xl font-extrabold text-emerald-600" id="cash-change">KSh 0</p>
                    </div>
                </div>
            </div>

            <!-- Step 2D: Room Charge -->
            <div id="pm-step-room" class="payment-step">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-3"><span class="material-symbols-outlined text-3xl">room_preferences</span></div>
                    <h4 class="font-bold text-lg">Charge to Room</h4>
                    <p class="text-sm text-gray-500">Post charges to guest folio</p>
                </div>
                <div class="bg-purple-50 rounded-2xl p-5 border border-purple-100 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Guest Room:</span>
                        <span class="font-bold text-gray-900" id="room-charge-ref">Room 0</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Current Balance:</span>
                        <span class="font-bold text-gray-900">KSh 0</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">This Order:</span>
                        <span class="font-bold text-purple-600" id="room-charge-amt">KSh 0</span>
                    </div>
                    <div class="border-t border-purple-200 pt-3 mt-3 flex justify-between">
                        <span class="font-bold text-gray-900">New Folio Total:</span>
                        <span class="font-extrabold text-gray-900">Pending</span>
                    </div>
                </div>
            </div>

            <!-- Step 3: Processing -->
            <div id="pm-step-processing" class="payment-step text-center py-10">
                <div class="spinner-ring mb-6"></div>
                <h4 class="font-bold text-xl text-gray-900 mb-2">Processing Payment...</h4>
                <p class="text-sm text-gray-500">Please wait while we secure the transaction.</p>
                <div class="mt-8 flex justify-center items-center gap-2 text-xs text-gray-400 font-bold uppercase tracking-widest">
                    <span class="material-symbols-outlined text-[16px]">lock</span> 256-bit Secure
                </div>
            </div>

            <!-- Step 4: Success -->
            <div id="pm-step-success" class="payment-step text-center py-6">
                <svg class="success-checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                    <circle class="success-checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                    <path class="success-checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                </svg>
                <h4 class="font-extrabold text-2xl text-gray-900 mb-2">Payment Successful!</h4>
                <p class="text-gray-500 mb-6">Transaction ID: <span class="font-mono text-gray-900 font-bold" id="pm-txn-id">SKP-XXXXXX</span></p>
                
                <div class="bg-gray-50 rounded-xl p-4 text-left border border-gray-200 mb-6 space-y-2">
                    <div class="flex justify-between text-sm"><span class="text-gray-500">Method:</span><span class="font-bold text-gray-900" id="pm-success-method">Cash</span></div>
                    <div class="flex justify-between text-sm"><span class="text-gray-500">Amount:</span><span class="font-bold text-green-600" id="pm-success-amount">KSh 0</span></div>
                    <div class="flex justify-between text-sm"><span class="text-gray-500">Date:</span><span class="font-bold text-gray-900"><?= date('d M Y, H:i') ?></span></div>
                </div>

                <div class="flex flex-col gap-3">
                    <button id="btn-print-receipt" class="w-full bg-blue-600 text-white font-bold py-3.5 rounded-xl hover:bg-blue-700 transition-colors flex justify-center items-center gap-2">
                        <span class="material-symbols-outlined">receipt_long</span> Download Receipt
                    </button>
                    <button onclick="closePaymentModal(true)" class="w-full bg-gray-100 text-gray-700 font-bold py-3.5 rounded-xl hover:bg-gray-200 transition-colors">
                        Close
                    </button>
                </div>
            </div>

        </div>

        <!-- Footer Actions (Sticky Bottom) -->
        <div id="pm-footer" class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-between items-center">
            <button id="pm-btn-back" onclick="pmGoBack()" class="text-gray-500 font-bold text-sm hover:text-gray-900 hidden px-4 py-2">Back</button>
            <div class="flex-1"></div>
            <button id="pm-btn-next" onclick="pmNextStep()" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:bg-blue-700 transition-colors active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                Continue
            </button>
        </div>

    </div>
</div>

<script>
    let pmCurrentStep = 1;
    let pmTotalAmount = 0;
    let pmOrderPayload = null;
    let pmSelectedMethod = null;

    // External trigger to open modal
    function openPaymentModal(payload, totalAmount) {
        pmOrderPayload = payload;
        pmTotalAmount = totalAmount;
        
        // Reset states
        pmCurrentStep = 1;
        pmSelectedMethod = null;
        document.querySelectorAll('input[name="pay_method"]').forEach(el => el.checked = false);
        document.querySelectorAll('.method-card').forEach(el => el.classList.remove('selected'));
        
        // Setup UI
        document.getElementById('pm-amount-display').textContent = 'KSh ' + totalAmount.toLocaleString();
        document.getElementById('cash-due').textContent = 'KSh ' + totalAmount.toLocaleString();
        document.getElementById('room-charge-amt').textContent = 'KSh ' + totalAmount.toLocaleString();
        document.getElementById('cash-received').value = '';
        document.getElementById('cash-change').textContent = 'KSh 0';
        
        // Room Charge visibility
        if(payload.order_type === 'Room Service') {
            document.getElementById('method-room-charge').classList.remove('hidden');
            document.getElementById('room-charge-ref').textContent = `Room ${payload.room_id}`;
        } else {
            document.getElementById('method-room-charge').classList.add('hidden');
        }

        updateModalView();

        const wrapper = document.getElementById('payment-modal-wrapper');
        wrapper.classList.remove('hidden');
        setTimeout(() => wrapper.classList.remove('opacity-0'), 10);
    }

    function closePaymentModal(wasSuccessful = false) {
        const wrapper = document.getElementById('payment-modal-wrapper');
        wrapper.classList.add('opacity-0');
        setTimeout(() => {
            wrapper.classList.add('hidden');
            if(wasSuccessful && typeof finalizeCheckoutSuccess === 'function') {
                finalizeCheckoutSuccess();
            }
        }, 300);
    }

    function highlightMethod(radio) {
        document.querySelectorAll('.method-card').forEach(el => el.classList.remove('selected'));
        radio.closest('.method-card').classList.add('selected');
        pmSelectedMethod = radio.value;
    }

    function calculateChange() {
        const received = parseFloat(document.getElementById('cash-received').value) || 0;
        const change = received - pmTotalAmount;
        const el = document.getElementById('cash-change');
        if(change >= 0) {
            el.textContent = 'KSh ' + change.toLocaleString();
            el.classList.remove('text-red-500');
            el.classList.add('text-emerald-600');
        } else {
            el.textContent = 'Insufficient';
            el.classList.remove('text-emerald-600');
            el.classList.add('text-red-500');
        }
    }

    function pmGoBack() {
        if(pmCurrentStep === 2) {
            pmCurrentStep = 1;
            updateModalView();
        }
    }

    function pmNextStep() {
        if(pmCurrentStep === 1) {
            if(!pmSelectedMethod) {
                Swal.fire({icon: 'warning', title: 'Select Method', text: 'Please select a payment method.', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false});
                return;
            }
            pmCurrentStep = 2;
            updateModalView();
        } 
        else if (pmCurrentStep === 2) {
            // Validation per method
            if(pmSelectedMethod === 'Cash') {
                const rec = parseFloat(document.getElementById('cash-received').value) || 0;
                if(rec < pmTotalAmount) {
                    Swal.fire({icon: 'error', title: 'Insufficient Cash', text: 'Received amount is less than due.', toast: true, position: 'top-end'});
                    return;
                }
            } else if (pmSelectedMethod === 'M-Pesa') {
                const phone = document.getElementById('mpesa-phone').value;
                if(phone.length < 9) {
                    Swal.fire({icon: 'error', title: 'Invalid Phone', text: 'Enter a valid M-Pesa number.', toast: true, position: 'top-end'});
                    return;
                }
            }
            
            // Proceed to API Processing
            pmCurrentStep = 3;
            updateModalView();
            processPaymentApi();
        }
    }

    function updateModalView() {
        // Hide all steps
        document.querySelectorAll('.payment-step').forEach(el => el.classList.remove('active'));
        
        const title = document.getElementById('pm-title');
        const subtitle = document.getElementById('pm-subtitle');
        const footer = document.getElementById('pm-footer');
        const btnNext = document.getElementById('pm-btn-next');
        const btnBack = document.getElementById('pm-btn-back');

        if(pmCurrentStep === 1) {
            document.getElementById('pm-step-1').classList.add('active');
            title.textContent = 'Complete Payment';
            subtitle.textContent = 'Choose your preferred method';
            footer.classList.remove('hidden');
            btnBack.classList.add('hidden');
            btnNext.textContent = 'Continue';
            btnNext.className = 'bg-blue-600 text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:bg-blue-700 transition-colors';
        } 
        else if (pmCurrentStep === 2) {
            btnBack.classList.remove('hidden');
            footer.classList.remove('hidden');
            
            if(pmSelectedMethod === 'M-Pesa') {
                document.getElementById('pm-step-mpesa').classList.add('active');
                title.textContent = 'M-Pesa Payment';
                btnNext.textContent = 'Send STK Push';
                btnNext.className = 'bg-green-600 text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:bg-green-700 transition-colors';
            } else if (pmSelectedMethod === 'Card') {
                document.getElementById('pm-step-card').classList.add('active');
                title.textContent = 'Card Payment';
                btnNext.textContent = 'Pay Securely';
            } else if (pmSelectedMethod === 'Cash') {
                document.getElementById('pm-step-cash').classList.add('active');
                title.textContent = 'Cash Payment';
                btnNext.textContent = 'Complete Payment';
                btnNext.className = 'bg-emerald-600 text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:bg-emerald-700 transition-colors';
            } else if (pmSelectedMethod === 'Bank') {
                // For simplicity, fallback to processing
                pmCurrentStep = 3;
                updateModalView();
                processPaymentApi();
            } else if (pmSelectedMethod === 'ChargeToRoom') {
                document.getElementById('pm-step-room').classList.add('active');
                title.textContent = 'Charge to Room';
                btnNext.textContent = 'Confirm Charge';
                btnNext.className = 'bg-purple-600 text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:bg-purple-700 transition-colors';
            }
            subtitle.textContent = 'Step 2 of 3';
        }
        else if (pmCurrentStep === 3) {
            document.getElementById('pm-step-processing').classList.add('active');
            title.textContent = 'Processing';
            subtitle.textContent = 'Do not close this window';
            footer.classList.add('hidden');
        }
        else if (pmCurrentStep === 4) {
            document.getElementById('pm-step-success').classList.add('active');
            title.textContent = 'Receipt';
            subtitle.textContent = 'Payment Completed';
            footer.classList.add('hidden');
        }
    }

    async function processPaymentApi() {
        let finalStatus = (pmSelectedMethod === 'ChargeToRoom') ? 'ChargeToRoom' : 'Paid';

        try {
            // Simulated delay for M-Pesa STK or Card Auth
            await new Promise(r => setTimeout(r, 1500));

            // Call existing endpoint
            const res = await fetch('../api/restaurant.php', {
                method:'POST',
                headers:{'Content-Type':'application/json'},
                body:JSON.stringify(pmOrderPayload)
            });
            const json = await res.json();
            
            if(json.success) {
                // Update status to Paid or ChargeToRoom
                await fetch('../api/restaurant.php', {
                    method:'PUT',
                    headers:{'Content-Type':'application/json'},
                    body:JSON.stringify({ id: json.order_id, status: finalStatus, pay_method: pmSelectedMethod })
                });

                // Show Success Step
                pmCurrentStep = 4;
                document.getElementById('pm-txn-id').textContent = 'SKP-' + json.order_id + '-' + Math.floor(Math.random()*9000);
                document.getElementById('pm-success-method').textContent = pmSelectedMethod;
                document.getElementById('pm-success-amount').textContent = 'KSh ' + pmTotalAmount.toLocaleString();
                
                // Configure Receipt Button
                document.getElementById('btn-print-receipt').onclick = function() {
                    window.open('print_receipt.php?type=restaurant&id=' + json.order_id, '_blank');
                };

                updateModalView();

                // Trigger Confetti
                fireConfetti();
            } else {
                Swal.fire({icon:'error',title:'Transaction Failed',text:json.message});
                pmCurrentStep = 1;
                updateModalView();
            }
        } catch(e) { 
            Swal.fire({icon:'error',title:'Network Error',text:'Could not connect to payment gateway.'}); 
            pmCurrentStep = 1;
            updateModalView();
        }
    }

    function fireConfetti() {
        var duration = 3000;
        var end = Date.now() + duration;

        (function frame() {
            confetti({
                particleCount: 5,
                angle: 60,
                spread: 55,
                origin: { x: 0 },
                colors: ['#2563eb', '#22c55e', '#f59e0b']
            });
            confetti({
                particleCount: 5,
                angle: 120,
                spread: 55,
                origin: { x: 1 },
                colors: ['#2563eb', '#22c55e', '#f59e0b']
            });

            if (Date.now() < end) {
                requestAnimationFrame(frame);
            }
        }());
    }
</script>
