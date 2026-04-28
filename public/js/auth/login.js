const { createApp } = Vue;

createApp({
    data() {
        return {
            form: {
                email: '',
                password: ''
            },
            loading: false,
            error: ''
        };
    },

    methods: {
        async handleLogin() {
            this.loading = true;
            this.error = '';

            if (!this.form.email || !this.form.password) {
                this.error = 'Please fill in all fields';
                this.loading = false;
                return;
            }

            try {
                const formData = new FormData();
                formData.append('email', this.form.email);
                formData.append('password', this.form.password);

                const response = await axios.post('/login', formData, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.data.success) {
                    window.location.href = response.data.redirect;
                } else {
                    this.error = response.data.message;
                }
            } catch (err) {
                this.error = 'Login failed. Please try again.';
                console.error(err);
            } finally {
                this.loading = false;
            }
        }
    }
}).mount('#app');