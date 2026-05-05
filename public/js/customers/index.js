const customersApp = Vue.createApp({
    components: {
        GlobalPagination: window.GlobalPagination
    },
    data() {
        return {
            customers: [],
            loading: false,
            submitting: false,
            errorMessage: '',
            successMessage: '',
            searchTerm: '',
            searchDebounceTimer: null,
            page: 1,
            perPage: 10,
            pagination: {
                page: 1,
                perPage: 10,
                total: 0,
                totalPages: 1,
                hasPrev: false,
                hasNext: false
            },
            showViewModal: false,
            showCreateModal: false,
            showEditModal: false,
            modalError: '',
            selectedCustomer: {},
            form: {
                name: '',
                email: '',
                password: '',
                vat_code: '',
                customer_identifier_code: ''
            }
        };
    },

    computed: {
        filteredCustomers() {
            return this.customers;
        },
        shouldShowPagination() {
            return (this.pagination.total || 0) > (this.pagination.perPage || this.perPage);
        }
    },

    methods: {
        async loadCustomers() {
            this.loading = true;
            this.errorMessage = '';

            try {
                const res = await axios.get('/customers/getData', {
                    params: {
                        page: this.page,
                        perPage: this.perPage,
                        search: this.searchTerm
                    }
                });
                if (res.data.success) {
                    this.customers = res.data.data || [];
                    this.pagination = res.data.pagination || this.pagination;
                    this.page = this.pagination.page || this.page;
                } else {
                    this.errorMessage = res.data.message || 'Failed to load customers';
                }
            } catch (err) {
                this.errorMessage = err?.response?.data?.message || 'Failed to load customers';
            } finally {
                this.loading = false;
            }
        },

        openViewModal(customer) {
            this.selectedCustomer = { ...customer };
            this.modalError = '';
            this.showViewModal = true;
        },

        openCreateModal() {
            this.modalError = '';
            this.resetForm();
            this.showCreateModal = true;
        },

        openEditModal(customer) {
            this.modalError = '';
            this.selectedCustomer = { ...customer };
            this.form = {
                name: customer.customer_name || '',
                email: customer.customer_email || '',
                password: '',
                vat_code: customer.customer_vat_code || '',
                customer_identifier_code: customer.customer_identifier_code || ''
            };
            this.showEditModal = true;
        },

        closeViewModal() {
            this.showViewModal = false;
            this.selectedCustomer = {};
        },

        closeCreateModal(force = false) {
            if (this.submitting && !force) return;
            this.showCreateModal = false;
            this.modalError = '';
            this.resetForm();
        },

        closeEditModal(force = false) {
            if (this.submitting && !force) return;
            this.showEditModal = false;
            this.modalError = '';
            this.selectedCustomer = {};
            this.resetForm();
        },

        resetForm() {
            this.form = {
                name: '',
                email: '',
                password: '',
                vat_code: '',
                customer_identifier_code: ''
            };
        },

        async submitCreateCustomer() {
            this.submitting = true;
            this.modalError = '';

            const payload = new FormData();
            payload.append('name', this.form.name);
            payload.append('email', this.form.email);
            payload.append('password', this.form.password);
            payload.append('vat_code', this.form.vat_code);
            await this.saveCustomer('/customers/create', payload, true);
            this.submitting = false;
        },

        async submitEditCustomer() {
            this.submitting = true;
            this.modalError = '';

            const payload = new FormData();
            payload.append('name', this.form.name);
            payload.append('email', this.form.email);
            payload.append('password', this.form.password);
            payload.append('vat_code', this.form.vat_code);
            payload.append('customer_identifier_code', this.form.customer_identifier_code);
            await this.saveCustomer(`/customers/update/${this.selectedCustomer.customer_id}`, payload, false);
            this.submitting = false;
        },

        async saveCustomer(url, payload, isCreate) {
            try {
                const res = await axios.post(url, payload, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (res.data.success) {
                    this.successMessage = res.data.message || 'Saved successfully';
                    if (isCreate) {
                        this.closeCreateModal(true);
                        this.page = 1;
                    } else {
                        this.closeEditModal(true);
                    }
                    await this.loadCustomers();
                    setTimeout(() => {
                        this.successMessage = '';
                    }, 3500);
                } else {
                    this.modalError = res.data.message || 'Operation failed';
                }
            } catch (err) {
                this.modalError = err?.response?.data?.message || 'Operation failed';
            }
        },

        async deleteCustomer(customerId) {
            if (!confirm('Delete this customer? This action cannot be undone.')) return;
            this.errorMessage = '';

            try {
                const res = await axios.delete(`/customers/delete/${customerId}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (res.data.success) {
                    this.successMessage = res.data.message || 'Customer deleted';
                    if (this.customers.length === 1 && this.page > 1) {
                        this.page -= 1;
                    }
                    await this.loadCustomers();
                    setTimeout(() => {
                        this.successMessage = '';
                    }, 3500);
                } else {
                    this.errorMessage = res.data.message || 'Delete failed';
                }
            } catch (err) {
                this.errorMessage = err?.response?.data?.message || 'Delete failed';
            }
        },

        formatDate(date) {
            return date ? new Date(date).toLocaleDateString() : '-';
        },

        formatDateTime(date) {
            return date ? new Date(date).toLocaleString() : '-';
        },

        goToPage(targetPage) {
            if (this.loading) return;
            if (targetPage < 1 || targetPage > this.pagination.totalPages) return;
            this.page = targetPage;
            this.loadCustomers();
        },

        onPerPageChange(value) {
            this.perPage = Number(value) || 10;
            this.page = 1;
            this.loadCustomers();
        }
    },

    watch: {
        searchTerm() {
            this.page = 1;
            if (this.searchDebounceTimer) {
                clearTimeout(this.searchDebounceTimer);
            }
            this.searchDebounceTimer = setTimeout(() => {
                this.loadCustomers();
            }, 300);
        }
    },

    mounted() {
        this.loadCustomers();
    }
});

customersApp.mount('#customers-vue');
