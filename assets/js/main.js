// Theme Toggle
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;
    const savedTheme = getCookie('theme') || 'light';
    html.setAttribute('data-bs-theme', savedTheme);
    updateThemeIcon(savedTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const current = html.getAttribute('data-bs-theme');
            const newTheme = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-bs-theme', newTheme);
            setCookie('theme', newTheme, 365);
            updateThemeIcon(newTheme);
        });
    }
});

function updateThemeIcon(theme) {
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        const icon = themeToggle.querySelector('i');
        if (icon) icon.className = theme === 'dark' ? 'bi bi-sun' : 'bi bi-moon';
    }
}

function setCookie(name, value, days) {
    const expires = new Date();
    expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
    document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/`;
}

function getCookie(name) {
    const nameEQ = name + "=";
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1);
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length);
    }
    return null;
}

// File Upload Preview
function previewFile(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.size > 5 * 1024 * 1024) {
            alert('File size exceeds 5MB limit.');
            input.value = '';
            return;
        }
        const allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        const ext = file.name.split('.').pop().toLowerCase();
        if (!allowed.includes(ext)) {
            alert('File type not allowed.');
            input.value = '';
            return;
        }
        if (preview) preview.innerHTML = `<i class="bi bi-file-earmark-check text-success"></i> ${file.name}`;
    }
}

// Password Validation
function validatePassword(input) {
    const password = input.value;
    if (password.length < 8) input.setCustomValidity('Password must be at least 8 characters.');
    else if (!/[A-Z]/.test(password)) input.setCustomValidity('Password must contain an uppercase letter.');
    else if (!/[a-z]/.test(password)) input.setCustomValidity('Password must contain a lowercase letter.');
    else if (!/[0-9]/.test(password)) input.setCustomValidity('Password must contain a number.');
    else input.setCustomValidity('');
}

function confirmPassword(confirmInput, passwordInput) {
    if (confirmInput.value !== passwordInput.value) confirmInput.setCustomValidity('Passwords do not match.');
    else confirmInput.setCustomValidity('');
}

// Application Form
function validateApplicationForm() {
    const ps = document.getElementById('personal_statement');
    const fn = document.getElementById('financial_need');
    if (ps && ps.value.length < 100) { alert('Personal statement must be at least 100 characters.'); return false; }
    if (fn && fn.value.length < 50) { alert('Financial need statement must be at least 50 characters.'); return false; }
    return true;
}

// Auto-dismiss alerts
setTimeout(function() {
    document.querySelectorAll('.alert-dismissible').forEach(alert => {
        try { new bootstrap.Alert(alert).close(); } catch(e) {}
    });
}, 5000);