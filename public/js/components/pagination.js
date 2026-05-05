(function () {
    window.GlobalPagination = {
        name: 'GlobalPagination',
        props: {
            pagination: {
                type: Object,
                required: true
            },
            loading: {
                type: Boolean,
                default: false
            },
            perPage: {
                type: Number,
                default: 10
            },
            perPageOptions: {
                type: Array,
                default: function () {
                    return [10, 25, 50];
                }
            }
        },
        emits: ['change-page', 'change-per-page'],
        computed: {
            pageButtons() {
                const total = this.pagination.totalPages || 1;
                const current = this.pagination.page || 1;

                if (total <= 7) {
                    return Array.from({ length: total }, (_, i) => ({
                        key: i + 1,
                        label: String(i + 1),
                        value: i + 1
                    }));
                }

                const buttons = [];
                const pushPage = (p) => buttons.push({ key: `p-${p}`, label: String(p), value: p });
                const pushDots = (k) => buttons.push({ key: `d-${k}`, label: '...', value: null });

                pushPage(1);
                let start = Math.max(2, current - 1);
                let end = Math.min(total - 1, current + 1);

                if (current <= 3) {
                    start = 2;
                    end = 4;
                }
                if (current >= total - 2) {
                    start = total - 3;
                    end = total - 1;
                }

                if (start > 2) pushDots('left');
                for (let p = start; p <= end; p += 1) pushPage(p);
                if (end < total - 1) pushDots('right');
                pushPage(total);

                return buttons;
            }
        },
        template: `
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:14px;gap:8px;flex-wrap:wrap;">
                <div style="color:#58606f;">
                    Showing page {{ pagination.page }} of {{ pagination.totalPages }} | Total records: {{ pagination.total }}
                </div>
                <div style="display:flex;gap:8px;align-items:center;">
                    <select
                        :value="perPage"
                        @change="$emit('change-per-page', Number($event.target.value))"
                        style="padding:8px 10px;border:1px solid #d7dbe3;border-radius:8px;"
                    >
                        <option v-for="option in perPageOptions" :key="option" :value="option">{{ option }} / page</option>
                    </select>
                    <button class="btn-info" :disabled="loading || !pagination.hasPrev" @click="$emit('change-page', pagination.page - 1)">Prev</button>
                    <button
                        v-for="btn in pageButtons"
                        :key="'pg-' + btn.key"
                        class="btn-info"
                        :disabled="loading || btn.value === null"
                        :style="btn.value === pagination.page ? 'background:#011428;font-weight:700;' : ''"
                        @click="btn.value !== null && $emit('change-page', btn.value)"
                    >
                        {{ btn.label }}
                    </button>
                    <button class="btn-info" :disabled="loading || !pagination.hasNext" @click="$emit('change-page', pagination.page + 1)">Next</button>
                </div>
            </div>
        `
    };
})();

