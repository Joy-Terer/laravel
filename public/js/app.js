// public/js/app.js - Main JavaScript for all pages

// Wait for DOM to load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Smart Chama App Loaded');
    
    // Initialize tooltips
    initTooltips();
    
    // Initialize popovers
    initPopovers();
    
    // Auto-hide alerts after 5 seconds
    autoHideAlerts();
    
    // Handle sidebar toggle on mobile
    initSidebarToggle();
});

// Bootstrap tooltips
function initTooltips() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Bootstrap popovers
function initPopovers() {
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
}

// Auto-hide alerts
function autoHideAlerts() {
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
}

// Sidebar toggle for mobile
function initSidebarToggle() {
    var sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });
    }
}

// Format currency
function formatCurrency(amount, currency = 'KES') {
    return currency + ' ' + parseFloat(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// Show loading spinner
function showLoading(button) {
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
}

// Hide loading spinner
function hideLoading(button, originalText) {
    button.disabled = false;
    button.innerHTML = originalText;
}