document.addEventListener('DOMContentLoaded', function() {
    initEditProfile();
    initScrollAnimations();
    initTooltips();
    initTouchFeedback();
    preventDoubleTapZoom();
});

function initEditProfile() {
    const editProfileBtn = document.getElementById('editProfileBtn');
    const saveProfileBtn = document.getElementById('saveProfileBtn');
    const editProfileModal = new bootstrap.Modal(document.getElementById('editProfileModal'));

    if (editProfileBtn) {
        editProfileBtn.addEventListener('click', function() {
            editProfileModal.show();
        });
    }

    if (saveProfileBtn) {
        saveProfileBtn.addEventListener('click', async function() {
            await saveProfileChanges(editProfileModal);
        });
    }
}

async function saveProfileChanges(modal) {
    const firstName = document.getElementById('firstName').value.trim();
    const lastName = document.getElementById('lastName').value.trim();
    const weight = parseFloat(document.getElementById('weight').value);
    const height = parseFloat(document.getElementById('height').value);
    const age = parseInt(document.getElementById('age').value);
    const activityLevel = document.getElementById('activityLevel').value;
    const goal = document.getElementById('goal').value;

    if (!firstName || !lastName) {
        showNotification('Please enter your full name', 'error');
        return;
    }

    if (weight < 20 || weight > 300) {
        showNotification('Weight must be between 20-300 kg', 'error');
        return;
    }

    if (height < 50 || height > 250) {
        showNotification('Height must be between 50-250 cm', 'error');
        return;
    }

    if (age < 13 || age > 120) {
        showNotification('Age must be between 13-120 years', 'error');
        return;
    }

    const saveBtn = document.getElementById('saveProfileBtn');
    const originalText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

    try {
        const response = await fetch('api/update_profile.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                firstName,
                lastName,
                weight,
                height,
                age,
                activityLevel,
                goal
            })
        });

        const data = await response.json();

        if (data.success) {
            showNotification('Profile updated successfully!', 'success');
            modal.hide();
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showNotification(data.message || 'Failed to update profile', 'error');
        }
    } catch (error) {
        showNotification('An error occurred while updating profile. Please try again.', 'error');
    } finally {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
    }
}

function showNotification(message, type = 'info') {
    const existing = document.querySelectorAll('.profile-notification');
    existing.forEach(el => el.remove());

    const notification = document.createElement('div');
    notification.className = `profile-notification ${type}`;
    
    const icon = type === 'success' ? 'fa-check-circle' : 
                 type === 'error' ? 'fa-exclamation-circle' : 
                 'fa-info-circle';
    
    notification.innerHTML = `<i class="fa-solid ${icon}"></i><span>${message}</span>`;

    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        padding: 1rem 1.5rem;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 600;
        z-index: 10000;
        animation: slideInRight 0.3s ease;
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -30px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, observerOptions);

    const dynamicElements = document.querySelectorAll('.dynamic-content');
    dynamicElements.forEach(el => observer.observe(el));
}

function initTouchFeedback() {
    const touchElements = document.querySelectorAll('.stat-card, .macro-card, .btn-profile-action, .activity-item');
    
    touchElements.forEach(element => {
        element.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.98)';
        }, { passive: true });
        
        element.addEventListener('touchend', function() {
            this.style.transform = '';
        }, { passive: true });
    });
}

function preventDoubleTapZoom() {
    let lastTouchEnd = 0;
    
    document.addEventListener('touchend', function(event) {
        const now = Date.now();
        if (now - lastTouchEnd <= 300) {
            if (event.target.matches('button, a, .stat-card, .macro-card')) {
                event.preventDefault();
            }
        }
        lastTouchEnd = now;
    }, { passive: false });
}

function initTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function(e) {
            showTooltip(e.target, e.target.getAttribute('data-tooltip'));
        });
        
        element.addEventListener('mouseleave', function() {
            hideTooltip();
        });
    });
}

let tooltipElement = null;

function showTooltip(target, text) {
    hideTooltip();
    
    tooltipElement = document.createElement('div');
    tooltipElement.className = 'custom-tooltip';
    tooltipElement.textContent = text;
    tooltipElement.style.cssText = `
        position: absolute;
        background: rgba(0, 0, 0, 0.9);
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        font-size: 0.85rem;
        white-space: nowrap;
        z-index: 10000;
        pointer-events: none;
        animation: fadeIn 0.2s ease;
    `;
    
    document.body.appendChild(tooltipElement);
    
    const rect = target.getBoundingClientRect();
    const tooltipRect = tooltipElement.getBoundingClientRect();
    
    tooltipElement.style.left = `${rect.left + (rect.width / 2) - (tooltipRect.width / 2)}px`;
    tooltipElement.style.top = `${rect.top - tooltipRect.height - 8}px`;
}

function hideTooltip() {
    if (tooltipElement) {
        tooltipElement.remove();
        tooltipElement = null;
    }
}

function smoothScrollTo(targetId) {
    const target = document.getElementById(targetId);
    if (target) {
        const offset = 100;
        const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;
        
        window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
        });
    }
}

function formatNumber(num, decimals = 1) {
    if (num === null || isNaN(num)) return '0';
    return parseFloat(num).toFixed(decimals).replace(/\.0+$/, '');
}

function animateCounters() {
    const counters = document.querySelectorAll('.stat-value, .macro-value, .weekly-value');
    
    counters.forEach(counter => {
        const text = counter.textContent.trim();
        const match = text.match(/^([\d.]+)/);
        
        if (match) {
            const target = parseFloat(match[1]);
            const duration = 1000;
            const steps = 30;
            const increment = target / steps;
            let current = 0;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                
                const newText = text.replace(/^[\d.]+/, formatNumber(current, text.includes('.') ? 1 : 0));
                counter.textContent = newText;
            }, duration / steps);
        }
    });
}

function animateProgressBars() {
    const progressBars = document.querySelectorAll('.progress-fill');
    
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        
        setTimeout(() => {
            bar.style.transition = 'width 0.8s ease';
            bar.style.width = width;
        }, 100);
    });
}

document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
        e.preventDefault();
        const editBtn = document.getElementById('editProfileBtn');
        if (editBtn) editBtn.click();
    }
});

const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    .profile-notification {
        animation: slideInRight 0.3s ease;
    }
    
    .custom-tooltip {
        animation: fadeIn 0.2s ease;
    }
`;
document.head.appendChild(style);

let resizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        adjustResponsiveElements();
    }, 250);
});

function adjustResponsiveElements() {
    const isMobile = window.innerWidth < 768;
}

window.addEventListener('load', function() {
    setTimeout(animateCounters, 300);
    setTimeout(animateProgressBars, 500);
    adjustResponsiveElements();
});

window.ProfilePage = {
    showNotification,
    smoothScrollTo,
    formatNumber
};