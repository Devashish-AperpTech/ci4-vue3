Vue.createApp({
    data() {
        return {
            form: { ...window.initialData },
            submitting: false,
            error: '',
            success: ''
        };
    },

    methods: {
        async submitForm() {
            this.error = '';
            this.success = '';

            if (!this.form.title.trim()) {
                this.error = 'Title is required';
                return;
            }

            // Validate JSON
            if (this.form.formData && this.form.formData.trim()) {
                try {
                    JSON.parse(this.form.formData);
                } catch (e) {
                    this.error = 'Invalid JSON: ' + e.message;
                    return;
                }
            }

            this.submitting = true;

            try {
                const fd = new FormData();
                fd.append('title', this.form.title);
                fd.append('description', this.form.description);
                fd.append('form_data', this.form.formData);

                const url = window.formMode === 'create'
                    ? '/forms/save'
                    : `/forms/update/${window.formId}`;

                const response = await axios.post(url, fd, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (response.data.success) {
                    this.success = response.data.message || 'Form saved';

                    setTimeout(() => {
                        window.location.href = response.data.redirect || '/forms';
                    }, 1000);

                } else {
                    this.error =
                        response.data.errors?.join(', ') ||
                        response.data.message ||
                        'Save failed';
                }

            } catch (err) {
                this.error = 'Error saving form';
            } finally {
                this.submitting = false;
            }
        }
    }

}).mount('#form-vue');