Vue.createApp({
    data() {
        return {
            forms: [],
            loading: false,
            errorMessage: '',
            successMessage: '',
            showModal: false,
            selectedForm: {}
        };
    },

    methods: {
        async loadForms() {
            this.loading = true;

            try {
                const res = await axios.get('/forms/getData');

                if (res.data.success) {
                    this.forms = res.data.data;
                } else {
                    this.errorMessage = 'Failed to load forms';
                }
            } catch (err) {
                this.errorMessage = 'Error loading forms';
            } finally {
                this.loading = false;
            }
        },

        viewForm(id) {
            const form = this.forms.find(f => f.form_id === id);

            if (form) {
                this.selectedForm = form;
                this.showModal = true;
            }
        },

        editForm(id) {
            window.location.href = `/forms/edit/${id}`;
        },

        async deleteForm(id) {
            if (!confirm('Delete this form?')) return;

            try {
                const res = await axios.delete(`/forms/delete/${id}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (res.data.success) {
                    this.successMessage = 'Form deleted';
                    this.loadForms();

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
            this.selectedForm = {};
        },

        formatDate(date) {
            return date ? new Date(date).toLocaleDateString() : '-';
        },

        formatJson(data) {
            if (!data) return '-';

            try {
                return JSON.stringify(
                    typeof data === 'string' ? JSON.parse(data) : data,
                    null,
                    2
                );
            } catch {
                return data;
            }
        }
    },

    mounted() {
        // Handle flash messages from URL
        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.get('success')) {
            this.successMessage = urlParams.get('success');
        }

        if (urlParams.get('error')) {
            this.errorMessage = urlParams.get('error');
        }

        this.loadForms();
    }

}).mount('#forms-vue');