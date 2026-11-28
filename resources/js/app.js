import './bootstrap';

import Alpine from 'alpinejs';
import flatpickr from 'flatpickr';
import { Indonesian } from 'flatpickr/dist/l10n/id.js';
import 'flatpickr/dist/flatpickr.css';

window.Alpine = Alpine;
window.flatpickr = flatpickr;

flatpickr.l10ns.id = Indonesian;

const toIsoDate = (date) => {
    if (! date) return '';

    return [
        date.getFullYear(),
        String(date.getMonth() + 1).padStart(2, '0'),
        String(date.getDate()).padStart(2, '0'),
    ].join('-');
};

const baseFlatpickrOptions = (config = {}) => {
    const options = {
        dateFormat: 'd M Y',
        locale: flatpickr.l10ns.id,
        allowInput: true,
        disableMobile: true,
        defaultDate: config.value || null,
    };

    if (config.minDate) options.minDate = config.minDate;
    if (config.maxDate) options.maxDate = config.maxDate;

    return options;
};

Alpine.data('datePicker', (config = {}) => ({
    picker: null,
    value: config.value || '',

    init() {
        this.picker = flatpickr(this.$refs.display, {
            ...baseFlatpickrOptions(config),
            onChange: (dates) => {
                this.value = dates[0] ? toIsoDate(dates[0]) : '';
                this.$refs.hidden.value = this.value;
                this.$refs.hidden.dispatchEvent(new Event('input', { bubbles: true }));
                this.$refs.hidden.dispatchEvent(new Event('change', { bubbles: true }));
            },
        });
    },

    toggle() {
        this.picker?.toggle();
    },

    destroy() {
        this.picker?.destroy();
    },
}));

Alpine.data('dateRangePicker', (config = {}) => ({
    picker: null,
    valueFrom: config.valueFrom || '',
    valueTo: config.valueTo || '',

    init() {
        const defaultDates = [];

        if (config.valueFrom) defaultDates.push(config.valueFrom);
        if (config.valueTo) defaultDates.push(config.valueTo);

        this.picker = flatpickr(this.$refs.display, {
            ...baseFlatpickrOptions(),
            mode: 'range',
            defaultDate: defaultDates.length ? defaultDates : null,
            onChange: (dates) => {
                if (! dates.length) {
                    this.valueFrom = '';
                    this.valueTo = '';
                } else if (dates.length === 1) {
                    this.valueFrom = toIsoDate(dates[0]);
                    this.valueTo = toIsoDate(dates[0]);
                } else {
                    this.valueFrom = toIsoDate(dates[0]);
                    this.valueTo = toIsoDate(dates[1]);
                }

                this.$refs.hiddenFrom.value = this.valueFrom;
                this.$refs.hiddenTo.value = this.valueTo;
                this.$refs.hiddenFrom.dispatchEvent(new Event('input', { bubbles: true }));
                this.$refs.hiddenTo.dispatchEvent(new Event('input', { bubbles: true }));
                this.$refs.hiddenFrom.dispatchEvent(new Event('change', { bubbles: true }));
                this.$refs.hiddenTo.dispatchEvent(new Event('change', { bubbles: true }));
            },
        });
    },

    toggle() {
        this.picker?.toggle();
    },

    destroy() {
        this.picker?.destroy();
    },
}));

Alpine.data('notificationBell', (config = {}) => ({
    open: false,
    loading: false,
    items: [],
    unreadCount: Number(config.initialCount || 0),
    panelStyle: {},
    timer: null,

    init() {
        this._onDocumentPointer = (event) => {
            if (! this.open) return;
            if (this.$refs.button?.contains(event.target) || this.$refs.panel?.contains(event.target)) return;
            this.open = false;
        };

        this._onVisibilityChange = () => {
            if (document.visibilityState === 'visible') this.fetchNotifications();
        };

        document.addEventListener('mousedown', this._onDocumentPointer);
        document.addEventListener('visibilitychange', this._onVisibilityChange);
        this.timer = window.setInterval(() => {
            if (document.visibilityState === 'visible' && this.$el.offsetParent !== null) {
                this.fetchNotifications();
            }
        }, 60000);

        if (this.$el.offsetParent !== null) this.fetchNotifications();
    },

    destroy() {
        document.removeEventListener('mousedown', this._onDocumentPointer);
        document.removeEventListener('visibilitychange', this._onVisibilityChange);
        if (this.timer) window.clearInterval(this.timer);
    },

    toggle() {
        this.open = ! this.open;
        if (this.open) {
            this.$nextTick(() => {
                this.position();
                this.fetchNotifications();
            });
        }
    },

    position() {
        if (! this.$refs.button) return;

        const rect = this.$refs.button.getBoundingClientRect();
        const width = Math.min(368, window.innerWidth - 32);
        const left = Math.min(Math.max(16, rect.right - width), window.innerWidth - width - 16);
        const spaceBelow = window.innerHeight - rect.bottom;
        const spaceAbove = rect.top;
        const openUp = spaceBelow < 420 && spaceAbove > spaceBelow;

        this.panelStyle = {
            left: `${left}px`,
            width: `${width}px`,
            ...(openUp
                ? { bottom: `${window.innerHeight - rect.top + 8}px`, top: 'auto' }
                : { top: `${rect.bottom + 8}px`, bottom: 'auto' }),
        };
    },

    async fetchNotifications() {
        this.loading = true;

        try {
            const response = await fetch('/notifications', {
                headers: { Accept: 'application/json' },
            });

            if (! response.ok) return;

            const data = await response.json();
            this.items = data.notifications || [];
            this.unreadCount = Number(data.unread_count || 0);
        } catch (error) {
            // Keep the previous list when the network is unavailable.
        } finally {
            this.loading = false;
        }
    },

    async markRead(item) {
        if (! item || item.read_at) return;

        try {
            const response = await fetch(`/notifications/${item.id}/read`, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                },
            });

            if (response.ok) {
                const data = await response.json();
                item.read_at = new Date().toISOString();
                this.unreadCount = Number(data.unread_count || 0);
            }
        } catch (error) {
            // Navigation should still work when the read receipt fails.
        }
    },

    async openItem(item) {
        await this.markRead(item);

        if (item.url) {
            window.location.href = item.url;
        }
    },

    async markAllRead() {
        try {
            const response = await fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                },
            });

            if (response.ok) {
                const data = await response.json();
                this.items = this.items.map((item) => ({ ...item, read_at: item.read_at || new Date().toISOString() }));
                this.unreadCount = Number(data.unread_count || 0);
            }
        } catch (error) {
            // Ignore transient failures; the next poll will refresh the list.
        }
    },

    toneClass(tone) {
        return {
            income: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300',
            expense: 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300',
            warning: 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300',
            neutral: 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300',
        }[tone] || 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300';
    },
}));

const realtimeState = new WeakMap();

const setRealtimeStatus = (form, active) => {
    const status = form.querySelector('[data-realtime-status]');
    if (! status) return;

    status.classList.toggle('hidden', ! active);
    status.classList.toggle('inline-flex', active);
};

const replaceRealtimeRegion = (selector, incomingDocument) => {
    const current = document.querySelector(selector);
    const next = incomingDocument.querySelector(selector);

    if (! current || ! next) return false;

    current.replaceWith(document.importNode(next, true));

    return true;
};

const loadRealtime = async (form, url, historyUrl = url) => {
    const endpoint = form.dataset.realtimeEndpoint;
    if (! endpoint) return;

    const state = realtimeState.get(form) || { controller: null, timer: null };
    state.controller?.abort();
    const controller = new AbortController();
    state.controller = controller;
    realtimeState.set(form, state);

    setRealtimeStatus(form, true);

    try {
        const response = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            signal: controller.signal,
        });

        if (! response.ok) throw new Error(`Realtime request failed: ${response.status}`);
        if (response.redirected) {
            window.location.href = response.url;
            return;
        }

        const html = await response.text();
        const incoming = new DOMParser().parseFromString(html, 'text/html');
        const regions = ['[data-realtime-summary]', '[data-realtime-results]', '[data-report-header]', '[data-report-summary]', '[data-report-table]'];
        const replaced = regions.some((selector) => replaceRealtimeRegion(selector, incoming));

        if (! replaced) throw new Error('Realtime response did not include a known region.');

        window.history.replaceState({}, '', historyUrl);
    } catch (error) {
        if (error.name === 'AbortError') return;

        window.location.href = historyUrl;
    } finally {
        if (state.controller === controller) setRealtimeStatus(form, false);
    }
};

const submitRealtimeForm = (form) => {
    const endpoint = form.dataset.realtimeEndpoint;
    if (! endpoint) return;

    const params = new URLSearchParams(new FormData(form));

    [...params.entries()].forEach(([key, value]) => {
        if (value === '') params.delete(key);
    });

    const query = params.toString();
    const target = `${endpoint}${query ? `?${query}` : ''}`;
    const pageUrl = `${form.action}${query ? `?${query}` : ''}`;

    loadRealtime(form, target, pageUrl);
};

const scheduleRealtimeForm = (form, delay = 250) => {
    const state = realtimeState.get(form) || { controller: null, timer: null };

    if (state.timer) window.clearTimeout(state.timer);
    state.timer = window.setTimeout(() => submitRealtimeForm(form), delay);
    realtimeState.set(form, state);
};

const initRealtimeFilters = () => {
    document.querySelectorAll('[data-realtime-form]').forEach((form) => {
        if (form.dataset.realtimeBound) return;
        form.dataset.realtimeBound = '1';

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            submitRealtimeForm(form);
        });

        form.addEventListener('input', (event) => {
            if (event.target.matches('input[type="search"], input[name="search"]')) {
                scheduleRealtimeForm(form, 300);
                return;
            }

            scheduleRealtimeForm(form, 180);
        });

        form.addEventListener('change', () => {
            scheduleRealtimeForm(form, 0);
        });
    });
};

document.addEventListener('click', (event) => {
    const link = event.target.closest('a[href]');
    if (! link) return;

    const region = link.closest('[data-realtime-results], [data-report-table]');
    const form = document.querySelector('[data-realtime-form]');
    if (! region || ! form || ! form.dataset.realtimeEndpoint) return;

    const url = new URL(link.href, window.location.origin);
    if (url.pathname !== window.location.pathname || ! url.searchParams.has('page')) return;

    event.preventDefault();
    loadRealtime(form, `${form.dataset.realtimeEndpoint}?${url.searchParams.toString()}`, url.toString());
});

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initRealtimeFilters);
} else {
    initRealtimeFilters();
}

Alpine.start();

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {
            // PWA registration is optional; the app remains fully usable without it.
        });
    });
}
