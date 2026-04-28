<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div id="dashboard-vue">
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Forms</h3>
            <div class="value">{{ totalForms }}</div>
        </div>

        <div class="stat-card">
            <h3>Recent Activity</h3>
            <div class="value">{{ recentActivity }}</div>
        </div>
    </div>
    
    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th>Form Title</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="form in recentForms" :key="form.form_id">
                    <td>{{ form.title }}</td>
                    <td>{{ form.status }}</td>
                    <td>{{ formatDate(form.created_at) }}</td>
                    <td>
                        <button class="btn-info" @click="editForm(form.form_id)">Edit</button>
                    </td>
                </tr>

                <tr v-if="recentForms.length === 0">
                    <td colspan="4">No forms found</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script src="<?= base_url('js/dashboard.js') ?>"></script>
<?= $this->endSection() ?>