Vue.createApp({
    data() {
        return {
            totalForms: 0,
            recentActivity: 0,
            recentForms: []
        };
    },

    methods: {
        async loadData() {
            try {
                const res = await axios.get('/api/dashboard-data');

                this.totalForms = res.data.data.totalForms || 0;
                this.recentActivity = res.data.data.recentActivity || 0;
                this.recentForms = res.data.data.recentForms || [];

            } catch (err) {
                console.error('Dashboard load error:', err);
            }
        },

        formatDate(date) {
            return date ? new Date(date).toLocaleDateString() : '-';
        },

        editForm(id) {
            window.location.href = `/forms/edit/${id}`;
        }
    },

    mounted() {
        this.loadData();
    }

}).mount('#dashboard-vue');