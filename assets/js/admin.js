/**
 * Admin Interface JavaScript
 * Handles interactive functionality for the admin dashboard
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize admin functionality
    initializeAdminFeatures();
    initializeFormValidation();
    initializeConfirmDialogs();
    initializeTooltips();
    initializeAutoSave();
});

/**
 * Initialize admin-specific features
 */
function initializeAdminFeatures() {
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert.classList.contains('show')) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    });

    // Add fade-in animation to cards
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('fade-in');
    });

    // Initialize character counters
    initializeCharacterCounters();

    // Initialize search functionality
    initializeSearch();
}

/**
 * Initialize form validation
 */
function initializeFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // Real-time validation for specific fields
    const titleInputs = document.querySelectorAll('input[name="title"]');
    titleInputs.forEach(input => {
        input.addEventListener('input', function() {
            validateTitle(this);
        });
    });

    const contentTextareas = document.querySelectorAll('textarea[name="content"]');
    contentTextareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            validateContent(this);
        });
    });
}

/**
 * Initialize confirmation dialogs
 */
function initializeConfirmDialogs() {
    const deleteLinks = document.querySelectorAll('a[href*="delete"]');
    
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            
            const confirmMessage = this.getAttribute('data-confirm') || 
                'Are you sure you want to delete this item? This action cannot be undone.';
            
            if (confirm(confirmMessage)) {
                // Create a form to submit the delete request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = this.href;
                
                const confirmInput = document.createElement('input');
                confirmInput.type = 'hidden';
                confirmInput.name = 'confirm_delete';
                confirmInput.value = '1';
                
                form.appendChild(confirmInput);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
}

/**
 * Initialize tooltips
 */
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Initialize auto-save functionality
 */
function initializeAutoSave() {
    const autoSaveForms = document.querySelectorAll('form[data-autosave]');
    
    autoSaveForms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea, select');
        let autoSaveTimeout;
        
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(autoSaveTimeout);
                autoSaveTimeout = setTimeout(() => {
                    saveFormData(form);
                }, 2000); // Auto-save after 2 seconds of inactivity
            });
        });
    });
}

/**
 * Initialize character counters
 */
function initializeCharacterCounters() {
    const titleInputs = document.querySelectorAll('input[name="title"]');
    titleInputs.forEach(input => {
        addCharacterCounter(input, 255);
    });
}

/**
 * Initialize search functionality
 */
function initializeSearch() {
    const searchInputs = document.querySelectorAll('input[name="search"]');
    
    searchInputs.forEach(input => {
        // Add search suggestions (if needed)
        input.addEventListener('input', function() {
            const query = this.value.trim();
            if (query.length >= 2) {
                // Could implement search suggestions here
                console.log('Search query:', query);
            }
        });
    });
}

/**
 * Validate title field
 */
function validateTitle(input) {
    const value = input.value.trim();
    const maxLength = 255;
    
    if (value.length === 0) {
        setFieldError(input, 'Title is required.');
        return false;
    } else if (value.length > maxLength) {
        setFieldError(input, `Title must be less than ${maxLength} characters.`);
        return false;
    } else {
        setFieldValid(input);
        return true;
    }
}

/**
 * Validate content field
 */
function validateContent(textarea) {
    const value = textarea.value.trim();
    
    if (value.length === 0) {
        setFieldError(textarea, 'Content is required.');
        return false;
    } else {
        setFieldValid(textarea);
        return true;
    }
}

/**
 * Set field error state
 */
function setFieldError(field, message) {
    field.classList.add('is-invalid');
    field.classList.remove('is-valid');
    
    let feedback = field.parentNode.querySelector('.invalid-feedback');
    if (!feedback) {
        feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        field.parentNode.appendChild(feedback);
    }
    feedback.textContent = message;
}

/**
 * Set field valid state
 */
function setFieldValid(field) {
    field.classList.add('is-valid');
    field.classList.remove('is-invalid');
    
    const feedback = field.parentNode.querySelector('.invalid-feedback');
    if (feedback) {
        feedback.remove();
    }
}

/**
 * Add character counter to input field
 */
function addCharacterCounter(input, maxLength) {
    const counter = document.createElement('div');
    counter.className = 'form-text character-counter';
    counter.innerHTML = `<span class="current">0</span> / ${maxLength} characters`;
    
    input.parentNode.appendChild(counter);
    
    input.addEventListener('input', function() {
        const currentLength = this.value.length;
        const currentSpan = counter.querySelector('.current');
        currentSpan.textContent = currentLength;
        
        if (currentLength > maxLength * 0.9) {
            counter.classList.add('text-warning');
        } else {
            counter.classList.remove('text-warning');
        }
        
        if (currentLength > maxLength) {
            counter.classList.add('text-danger');
            counter.classList.remove('text-warning');
        } else {
            counter.classList.remove('text-danger');
        }
    });
    
    // Trigger initial count
    input.dispatchEvent(new Event('input'));
}

/**
 * Save form data to localStorage
 */
function saveFormData(form) {
    const formData = new FormData(form);
    const data = {};
    
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    
    const formId = form.id || 'autosave_form';
    localStorage.setItem(`autosave_${formId}`, JSON.stringify(data));
    
    // Show auto-save indicator
    showAutoSaveIndicator();
}

/**
 * Load form data from localStorage
 */
function loadFormData(form) {
    const formId = form.id || 'autosave_form';
    const savedData = localStorage.getItem(`autosave_${formId}`);
    
    if (savedData) {
        const data = JSON.parse(savedData);
        
        for (let [key, value] of Object.entries(data)) {
            const field = form.querySelector(`[name="${key}"]`);
            if (field) {
                field.value = value;
                if (field.type === 'checkbox') {
                    field.checked = value === '1';
                }
            }
        }
    }
}

/**
 * Show auto-save indicator
 */
function showAutoSaveIndicator() {
    let indicator = document.querySelector('.autosave-indicator');
    
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.className = 'autosave-indicator position-fixed bottom-0 end-0 m-3 alert alert-success alert-sm';
        indicator.innerHTML = '<i class="fas fa-check"></i> Auto-saved';
        document.body.appendChild(indicator);
    }
    
    indicator.style.display = 'block';
    indicator.classList.add('fade-in');
    
    setTimeout(() => {
        indicator.style.display = 'none';
    }, 2000);
}

/**
 * Utility function to format dates
 */
function formatDate(date) {
    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(new Date(date));
}

/**
 * Utility function to truncate text
 */
function truncateText(text, length = 100) {
    if (text.length <= length) return text;
    return text.substring(0, length) + '...';
}

/**
 * Show loading state on buttons
 */
function showButtonLoading(button, text = 'Loading...') {
    button.disabled = true;
    button.innerHTML = `<span class="loading"></span> ${text}`;
}

/**
 * Hide loading state on buttons
 */
function hideButtonLoading(button, originalText) {
    button.disabled = false;
    button.innerHTML = originalText;
}

