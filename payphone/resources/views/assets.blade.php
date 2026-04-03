{{-- Payphone Payment Box Assets --}}
<link rel="stylesheet" href="https://cdn.payphonetodoesposible.com/box/v1.1/payphone-payment-box.css">
<script type="module" src="https://cdn.payphonetodoesposible.com/box/v1.1/payphone-payment-box.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if Payphone payment method is selected
        const payphoneRadio = document.querySelector('input[name="payment_method"][value="payphone"]');
        
        if (payphoneRadio) {
            payphoneRadio.addEventListener('change', function() {
                if (this.checked) {
                    initPayphonePaymentBox();
                }
            });
        }

        function initPayphonePaymentBox() {
            const configDiv = document.getElementById('payphone-config');
            
            if (!configDiv) {
                console.error('Payphone config not found');
                return;
            }

            const token = configDiv.getAttribute('data-token');
            const storeId = configDiv.getAttribute('data-store-id');
            const amount = parseInt(configDiv.getAttribute('data-amount')) || 0;
            const currency = configDiv.getAttribute('data-currency') || 'USD';
            const reference = configDiv.getAttribute('data-reference') || 'Order Payment';
            
            // Get transaction ID from backend
            const transactionIdDiv = document.getElementById('payphone-transaction-id');
            const clientTransactionId = transactionIdDiv ? transactionIdDiv.getAttribute('data-value') : 'ORDER-' + Date.now();

            if (!token || !storeId) {
                console.error('Payphone credentials not configured');
                return;
            }

            // Initialize Payphone Payment Box
            if (typeof PPaymentButtonBox !== 'undefined') {
                const ppb = new PPaymentButtonBox({
                    token: token,
                    clientTransactionId: clientTransactionId,
                    amount: amount,
                    amountWithoutTax: amount, // Adjust based on your tax configuration
                    tax: 0,
                    currency: currency,
                    storeId: storeId,
                    reference: reference,
                    lang: 'es',
                    defaultMethod: 'card',
                    timeZone: -5,
                    // Optional: Customer information can be added here
                    // phoneNumber: '+593999999999',
                    // email: 'customer@example.com',
                    // documentId: '1234567890',
                    // identificationType: 1
                }).render('pp-button');
            } else {
                console.error('Payphone Payment Box SDK not loaded');
            }
        }
    });
</script>

