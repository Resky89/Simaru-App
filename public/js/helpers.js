/* Global Helpers for Asset Monitoring App */

// Toast Notifications
window.showToast = function(message, type = 'success') {
    const notification = document.createElement('div');
    notification.id = type + 'Notification' + Date.now();
    notification.className = 'fixed top-4 right-4 p-4 rounded shadow-md z-50 animate-slide-in-right max-w-md overflow-y-auto max-h-[80vh]';
    notification.role = 'alert';

    const hasHTML = /<[a-z][\s\S]*>/i.test(message);

    if (type === 'success') {
        notification.classList.add('bg-green-100', 'border-l-4', 'border-green-500', 'text-green-700');
        notification.innerHTML = `
            <div class="flex items-start">
                <div class="py-1">
                    <svg class="h-6 w-6 text-green-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-bold">Berhasil!</p>
                    <div>${message}</div>
                </div>
                <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
            </div>
        `;
    } else {
        notification.classList.add('bg-red-100', 'border-l-4', 'border-red-500', 'text-red-700', 'overflow-auto');

        const wrapper = document.createElement('div');
        wrapper.className = 'flex items-start';

        const iconContainer = document.createElement('div');
        iconContainer.className = 'py-1 flex-shrink-0';
        iconContainer.innerHTML = `
            <svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        `;

        const contentContainer = document.createElement('div');
        contentContainer.className = 'flex-grow max-w-xs sm:max-w-sm md:max-w-md';

        const title = document.createElement('p');
        title.className = 'font-bold';
        title.textContent = 'Gagal!';
        contentContainer.appendChild(title);

        const messageContainer = document.createElement('div');
        messageContainer.className = 'error-message';

        if (hasHTML) {
            messageContainer.innerHTML = message;
        } else {
            messageContainer.textContent = message;
        }

        contentContainer.appendChild(messageContainer);

        const closeBtn = document.createElement('span');
        closeBtn.className = 'ml-4 cursor-pointer flex-shrink-0';
        closeBtn.textContent = '×';
        closeBtn.onclick = function () {
            notification.remove();
        };

        wrapper.appendChild(iconContainer);
        wrapper.appendChild(contentContainer);
        wrapper.appendChild(closeBtn);
        notification.appendChild(wrapper);
    }

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
        setTimeout(() => notification.remove(), 500);
    }, type === 'success' ? 5000 : 10000);
};

// Global CSS styles injection (if not exists)
if (!document.getElementById('global-dynamic-styles')) {
    document.head.insertAdjacentHTML('beforeend', `
        <style id="global-dynamic-styles">
            @keyframes slideInRight {
                from { transform: translateX(100%); }
                to { transform: translateX(0); }
            }
            .animate-slide-in-right {
                animation: slideInRight 0.3s ease-out forwards;
            }

            .error-message ul {
                margin-top: 0.5rem;
                padding-left: 1.5rem;
            }
            .error-message ul li {
                margin-bottom: 0.25rem;
            }
            .error-message ul li:last-child {
                margin-bottom: 0;
            }
        </style>
    `);
}

// Global UI Form & Modal helpers
window.openModal = function(modal, content) {
    if (!modal) return;
    modal.classList.remove('hidden');
    if (content) {
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
            content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
        }, 10);
    }
};

window.closeModal = function(modal, content) {
    if (!content) content = modal.querySelector('.transform');
    if (content) {
        content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
        content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
    }
    setTimeout(() => {
        if (modal) modal.classList.add('hidden');
        
        // Modal specific resets usually depend on the specific view logic.
        // It's safer to just handle styling centrally and let local script do the resets.
        // Or handle reset if we broadcast an event.
        const event = new CustomEvent('modalClosed', { detail: { modalId: modal?.id } });
        window.dispatchEvent(event);
    }, 300);
};

window.resetForm = function(formId) {
    const form = document.getElementById(formId);
    if (!form) return;
    form.reset();
    if (typeof window.clearFieldErrors === 'function') {
        window.clearFieldErrors(form);
    }
};

window.changePage = function(page) {
    const url = new URL(window.location.href);
    url.searchParams.set('page', page);
    if (typeof window.refreshTable === 'function') {
        window.refreshTable(url.toString());
    } else {
        window.location.href = url.toString();
    }
};

window.changeItemPerPage = function(limit) {
    const url = new URL(window.location.href);
    url.searchParams.set('limit', limit);
    url.searchParams.set('page', 1);
    if (typeof window.refreshTable === 'function') {
        window.refreshTable(url.toString());
    } else {
        window.location.href = url.toString();
    }
};
