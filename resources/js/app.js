import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);

const csrf = () => document.querySelector('meta[name="csrf-token"]').content;

async function postJson(url, body = {}) {
    const response = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf() },
        body: JSON.stringify(body),
    });

    if (response.status === 401) {
        window.location.href = '/login';
        return null;
    }

    if (!response.ok) {
        throw new Error('Request failed');
    }

    return response.json();
}

// Global toast notifications: window.dispatchEvent(new CustomEvent('toast', { detail: { message, type } }))
Alpine.data('toasts', () => ({
    items: [],
    add({ message, type = 'success' }) {
        const id = Date.now() + Math.random();
        this.items.push({ id, message, type });
        setTimeout(() => this.remove(id), 3500);
    },
    remove(id) {
        this.items = this.items.filter((t) => t.id !== id);
    },
}));

Alpine.store('cart', { count: 0 });

// "Add to cart" button that works without a page reload.
Alpine.data('addToCart', (url) => ({
    loading: false,
    async add(quantity = 1) {
        this.loading = true;
        try {
            const data = await postJson(url, { quantity });
            if (data) {
                Alpine.store('cart').count = data.count;
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message } }));
            }
        } catch {
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Could not add to cart.', type: 'error' } }));
        } finally {
            this.loading = false;
        }
    },
}));

Alpine.data('wishlist', (url, initial = false) => ({
    active: initial,
    async toggle() {
        try {
            const data = await postJson(url);
            if (data) {
                this.active = data.added;
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: data.added ? 'Added to wishlist' : 'Removed from wishlist' },
                }));
            }
        } catch {
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Something went wrong.', type: 'error' } }));
        }
    },
}));

window.Alpine = Alpine;
Alpine.start();
