// public/js/contributions.js

document.addEventListener('DOMContentLoaded', function() {
    // Payment method selector
    const paymentMethod = document.getElementById('payment_method');
    if (paymentMethod) {
        paymentMethod.addEventListener('change', handlePaymentMethodChange);
    }
    
    // Amount input
    const amountInput = document.getElementById('amount');
    if (amountInput) {
        amountInput.addEventListener('input', validateAmount);
    }
});

// Handle payment method change
function handlePaymentMethodChange(e) {
    const method = e.target.value;
    const phoneField = document.getElementById('phoneField');
    const transactionField = document.getElementById('transactionField');
    
    switch(method) {
        case 'mpesa':
        case 'wave':
            phoneField.style.display = 'block';
            transactionField.style.display = 'block';
            break;
        case 'paypal':
            phoneField.style.display = 'none';
            transactionField.style.display = 'none';
            // Redirect to diaspora page
            window.location.href = '/diaspora?method=paypal';
            break;
        default:
            phoneField.style.display = 'none';
            transactionField.style.display = 'block';
    }
}

// Validate amount
function validateAmount(e) {
    const amount = parseFloat(e.target.value);
    const errorDiv = document.getElementById('amountError');
    
    if (amount < 1) {
        errorDiv.textContent = 'Amount must be at least 1';
        errorDiv.style.display = 'block';
    } else {
        errorDiv.style.display = 'none';
    }
}