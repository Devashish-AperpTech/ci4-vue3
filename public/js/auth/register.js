const { createApp } = Vue;

createApp({
    data() {
        return {
            form: {
                name: '',
                email: '',
                password: '',
                confirm_password: ''
            },
            loading: false,
            errors: []
        };
    },

    methods: {
        async handleRegister() {
            this.loading = true;
            this.errors = [];

            // Validation
            if (!this.form.name || !this.form.email || !this.form.password) {
                this.errors.push('Please fill in all fields');
                this.loading = false;
                return;
            }

            if (this.form.password.length < 6) {
                this.errors.push('Password must be at least 6 characters');
                this.loading = false;
                return;
            }

            if (this.form.password !== this.form.confirm_password) {
                this.errors.push('Passwords do not match');
                this.loading = false;
                return;
            }

            try {
                const formData = new FormData();
                formData.append('name', this.form.name);
                formData.append('email', this.form.email);
                formData.append('password', this.form.password);
                formData.append('confirm_password', this.form.confirm_password);

                const response = await axios.post('/register', formData, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.data.success) {
                    window.location.href = response.data.redirect;
                } else {
                    if (response.data.errors) {
                        this.errors = response.data.errors;
                    } else {
                        this.errors = ['Registration failed. Please try again.'];
                    }
                }
            } catch (err) {
                this.errors = ['Registration failed. Please try again.'];
                console.error(err);
            } finally {
                this.loading = false;
            }
        }
    }
}).mount('#app');