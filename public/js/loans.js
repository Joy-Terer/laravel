// public/js/loans.js

document.addEventListener('DOMContentLoaded', function() {
    // Loan calculator
    const amountInput = document.getElementById('amount');
    const periodSelect = document.getElementById('repayment_period');
    
    if (amountInput && periodSelect) {
        amountInput.addEventListener('input', calculateLoan);
        periodSelect.addEventListener('change', calculateLoan);
    }
});

// Calculate loan payments
function calculateLoan() {
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    const months = parseInt(document.getElementById('repayment_period').value) || 1;
    const interestRate = 0.05; // 5%
    
    const totalInterest = amount * interestRate;
    const totalAmount = amount + totalInterest;
    const monthlyPayment = totalAmount / months;
    
    // Update display
    document.getElementById('monthlyPayment').textContent = 
        formatCurrency(monthlyPayment, 'KES');
    document.getElementById('totalInterest').textContent = 
        formatCurrency(totalInterest, 'KES');
    document.getElementById('totalAmount').textContent = 
        formatCurrency(totalAmount, 'KES');
}

// Confirm loan approval
function confirmApprove(loanId) {
    if (confirm('Are you sure you want to approve this loan?')) {
        document.getElementById('approveForm_' + loanId).submit();
    }
}

// Confirm loan decline with reason
function confirmDecline(loanId) {
    const reason = prompt('Please enter reason for declining:');
    if (reason) {
        const form = document.getElementById('declineForm_' + loanId);
        const reasonInput = document.createElement('input');
        reasonInput.type = 'hidden';
        reasonInput.name = 'reason';
        reasonInput.value = reason;
        form.appendChild(reasonInput);
        form.submit();
    }
}