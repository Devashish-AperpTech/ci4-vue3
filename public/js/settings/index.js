Vue.createApp({
    data() {
        return {
            loadingProfileSave: false,
            loadingPasswordSave: false,
            errorMessage: '',
            successMessage: '',
            profile: {
                name: '',
                email: '',
                vat_code: '',
                identifier_code: ''
            },
            password: {
                current_password: '',
                new_password: '',
                confirm_password: ''
            }
        };
    },

    methods: {
        async loadSettings() {
            this.errorMessage = '';

            try {
                const res = await axios.get('/settings/getData');
                if (res.data.success) {
                    const data = res.data.data || {};
                    this.profile.name = data.customer_name || '';
                    this.profile.email = data.customer_email || '';
                    this.profile.vat_code = data.customer_vat_code || '';
                    this.profile.identifier_code = data.customer_identifier_code || '';
                } else {
                    this.errorMessage = res.data.message || 'Failed to load settings';
                }
            } catch (err) {
                this.errorMessage = err?.response?.data?.message || 'Failed to load settings';
            }
        },

        async saveProfile() {
            this.loadingProfileSave = true;
            this.errorMessage = '';

            const payload = new FormData();
            payload.append('name', this.profile.name);
            payload.append('email', this.profile.email);
            payload.append('vat_code', this.profile.vat_code);

            try {
                const res = await axios.post('/settings/update-profile', payload, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (res.data.success) {
                    this.successMessage = res.data.message || 'Profile updated successfully';
                    setTimeout(() => {
                        this.successMessage = '';
                    }, 3500);
                } else {
                    this.errorMessage = res.data.message || 'Failed to update profile';
                }
            } catch (err) {
                this.errorMessage = err?.response?.data?.message || 'Failed to update profile';
            } finally {
                this.loadingProfileSave = false;
            }
        },

        async savePassword() {
            this.loadingPasswordSave = true;
            this.errorMessage = '';

            const payload = new FormData();
            payload.append('current_password', this.password.current_password);
            payload.append('new_password', this.password.new_password);
            payload.append('confirm_password', this.password.confirm_password);

            try {
                const res = await axios.post('/settings/update-password', payload, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (res.data.success) {
                    this.successMessage = res.data.message || 'Password updated successfully';
                    this.password.current_password = '';
                    this.password.new_password = '';
                    this.password.confirm_password = '';
                    setTimeout(() => {
                        this.successMessage = '';
                    }, 3500);
                } else {
                    this.errorMessage = res.data.message || 'Failed to update password';
                }
            } catch (err) {
                this.errorMessage = err?.response?.data?.message || 'Failed to update password';
            } finally {
                this.loadingPasswordSave = false;
            }
        }
    },

    mounted() {
        this.loadSettings();
    }
}).mount('#settings-vue');

