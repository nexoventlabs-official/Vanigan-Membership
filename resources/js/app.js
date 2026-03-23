import './bootstrap';
import '../css/app.css';

// Tamil Nadu Vanigargalin Sangamam - Main Application JS

// Global app configuration
window.App = {
    baseUrl: document.querySelector('meta[name="app-url"]')?.getAttribute('content') || '',
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
    apiPrefix: '/api/vanigam'
};

// Utility functions
window.Utils = {
    // Show loading state
    showLoading(element) {
        if (element) {
            element.innerHTML = '<span class="loading"></span> Loading...';
            element.disabled = true;
        }
    },

    // Hide loading state
    hideLoading(element, originalText = 'Submit') {
        if (element) {
            element.innerHTML = originalText;
            element.disabled = false;
        }
    },

    // Format phone number
    formatPhone(phone) {
        return phone.replace(/\D/g, '').slice(-10);
    },

    // Validate Indian mobile number
    isValidMobile(mobile) {
        const pattern = /^[6-9]\d{9}$/;
        return pattern.test(mobile);
    },

    // Show toast message
    showToast(message, type = 'info') {
        // Simple toast implementation
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'error' ? '#dc3545' : type === 'success' ? '#28a745' : '#007bff'};
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            z-index: 9999;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            animation: slideIn 0.3s ease;
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    },

    // API request helper
    async apiRequest(endpoint, options = {}) {
        const url = `${window.App.baseUrl}${window.App.apiPrefix}${endpoint}`;
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.App.csrfToken,
                'Accept': 'application/json'
            }
        };

        try {
            const response = await fetch(url, { ...defaultOptions, ...options });
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Request failed');
            }
            
            return data;
        } catch (error) {
            console.error('API Request Error:', error);
            throw error;
        }
    }
};

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);

// Initialize app when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Tamil Nadu Vanigargalin Sangamam - App Initialized');
    
    // Initialize any global components here
    initializeGlobalComponents();
});

function initializeGlobalComponents() {
    // Auto-format phone inputs
    const phoneInputs = document.querySelectorAll('input[type="tel"], input[name*="mobile"], input[name*="phone"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 10) value = value.slice(0, 10);
            e.target.value = value;
        });
    });

    // Auto-uppercase EPIC inputs
    const epicInputs = document.querySelectorAll('input[name*="epic"]');
    epicInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });
    });

    // Form validation helpers
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const mobileInputs = form.querySelectorAll('input[type="tel"], input[name*="mobile"]');
            mobileInputs.forEach(input => {
                if (input.value && !Utils.isValidMobile(input.value)) {
                    e.preventDefault();
                    Utils.showToast('Please enter a valid 10-digit mobile number', 'error');
                    input.focus();
                    return false;
                }
            });
        });
    });
}

// Export for global use
window.initializeGlobalComponents = initializeGlobalComponents;