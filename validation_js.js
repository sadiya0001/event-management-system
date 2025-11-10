// assets/js/validation.js - Client-side Form Validation

// Email validation
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Phone number validation (10 digits)
function validatePhone(phone) {
    const re = /^[0-9]{10}$/;
    return re.test(phone);
}

// Student ID validation (min 4 characters)
function validateStudentId(id) {
    return id.length >= 4;
}

// Password strength validation
function validatePassword(password) {
    return password.length >= 6;
}

// Real-time validation for registration form
document.addEventListener('DOMContentLoaded', function() {
    
    // Email field validation
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            if (!validateEmail(this.value)) {
                this.classList.add('is-invalid');
                showError(this, 'Please enter a valid email address');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                removeError(this);
            }
        });
    }
    
    // Phone number validation
    const contactInput = document.getElementById('contact');
    if (contactInput) {
        contactInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        
        contactInput.addEventListener('blur', function() {
            if (!validatePhone(this.value)) {
                this.classList.add('is-invalid');
                showError(this, 'Please enter a valid 10-digit phone number');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                removeError(this);
            }
        });
    }
    
    // Student ID validation
    const studentIdInput = document.getElementById('student_id');
    if (studentIdInput) {
        studentIdInput.addEventListener('blur', function() {
            if (!validateStudentId(this.value)) {
                this.classList.add('is-invalid');
                showError(this, 'Student ID must be at least 4 characters');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                removeError(this);
            }
        });
    }
    
    // Password validation
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('blur', function() {
            if (!validatePassword(this.value)) {
                this.classList.add('is-invalid');
                showError(this, 'Password must be at least 6 characters');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                removeError(this);
            }
        });
    }
    
    // Confirm password validation
    const confirmPasswordInput = document.getElementById('confirm_password');
    if (confirmPasswordInput && passwordInput) {
        confirmPasswordInput.addEventListener('blur', function() {
            if (this.value !== passwordInput.value) {
                this.classList.add('is-invalid');
                showError(this, 'Passwords do not match');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                removeError(this);
            }
        });
    }
    
    // Form submission validation
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            let isValid = true;
            const inputs = this.querySelectorAll('input[required]');
            
            inputs.forEach(function(input) {
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields correctly');
            }
        });
    }
    
    // Event registration form validation
    const registrationForm = document.getElementById('registrationForm');
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const studentId = document.getElementById('student_id').value.trim();
            const email = document.getElementById('email').value.trim();
            const contact = document.getElementById('contact').value.trim();
            
            let errors = [];
            
            if (!name) errors.push('Name is required');
            if (!validateStudentId(studentId)) errors.push('Valid Student ID is required');
            if (!validateEmail(email)) errors.push('Valid email is required');
            if (!validatePhone(contact)) errors.push('Valid 10-digit phone number is required');
            
            if (errors.length > 0) {
                e.preventDefault();
                alert('Please fix the following errors:\n' + errors.join('\n'));
                return false;
            }
        });
    }
});

// Helper function to show error message
function showError(input, message) {
    removeError(input);
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback';
    errorDiv.textContent = message;
    input.parentNode.appendChild(errorDiv);
}

// Helper function to remove error message
function removeError(input) {
    const errorDiv = input.parentNode.querySelector('.invalid-feedback');
    if (errorDiv) {
        errorDiv.remove();
    }
}

// Confirm delete action
function confirmDelete(eventName) {
    return confirm('Are you sure you want to delete the event "' + eventName + '"? This action cannot be undone.');
}

// Search functionality with debounce
let searchTimeout;
function debounceSearch(input, delay = 500) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        input.form.submit();
    }, delay);
}
