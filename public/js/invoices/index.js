Vue.createApp({
    data() {
        return {
            invoices: [],
            loading: false,
            errorMessage: '',
            successMessage: '',
            showModal: false,
            selectedInvoice: {}
        };
    },

    methods: {
        async loadInvoices() {
            this.loading = true;

            try {
                const res = await axios.get('/api/invoices');

                if (res.data.success) {
                    this.invoices = res.data.data;
                } else {
                    this.errorMessage = 'Failed to load invoices';
                }
            } catch (err) {
                this.errorMessage = 'Error loading invoices';
            } finally {
                this.loading = false;
            }
        },

        viewInvoice(id) {
            const invoice = this.invoices.find(item => Number(item.id) === Number(id));

            if (invoice) {
                this.selectedInvoice = invoice;
                this.showModal = true;
            }
        },

        editInvoice(id) {
            window.location.href = `/invoices/edit/${id}`;
        },

        async deleteInvoice(id) {
            if (!confirm('Delete this invoice?')) return;

            try {
                const res = await axios.delete(`/api/invoices/${id}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (res.data.success) {
                    this.successMessage = 'Invoice deleted';
                    this.loadInvoices();

                    setTimeout(() => {
                        this.successMessage = '';
                    }, 3000);
                } else {
                    this.errorMessage = res.data.message;
                }
            } catch (err) {
                this.errorMessage = 'Delete failed';
            }
        },

        closeModal() {
            this.showModal = false;
            this.selectedInvoice = {};
        },

        formatDate(date) {
            return date ? new Date(date).toLocaleDateString() : '-';
        },

        formatDateTime(date) {
            return date ? new Date(date).toLocaleString() : '-';
        },

        formatMoney(value) {
            if (value === null || value === undefined || value === '') return '-';
            return Number(value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    },

    mounted() {
        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.get('success')) {
            this.successMessage = urlParams.get('success');
        }

        if (urlParams.get('error')) {
            this.errorMessage = urlParams.get('error');
        }

        this.loadInvoices();
    }
}).mount('#invoice-vue');
