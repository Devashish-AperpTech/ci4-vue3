<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div id="forms-vue">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>My Forms</h2>
        <a href="<?= base_url('forms/create') ?>" class="btn-primary" style="text-decoration: none;">+ Create New Form</a>
    </div>

    <div v-if="errorMessage" class="error-message">{{ errorMessage }}</div>
    <div v-if="successMessage" class="success-message" style="background:#d4edda;color:#28a745;padding:10px;border-radius:6px;margin-bottom:20px;">
        {{ successMessage }}
    </div>

    <div v-if="loading" style="text-align:center;padding:40px;">Loading...</div>

    <div v-else-if="forms.length === 0" style="text-align:center;padding:40px;color:#666;">
        No forms created yet. Click "Create New Form" to get started!
    </div>

    <div v-else class="data-table">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="form in forms" :key="form.form_id">
                    <td>{{ form.form_id }}</td>
                    <td>{{ form.title }}</td>
                    <td>{{ form.description || '-' }}</td>
                    <td>{{ form.status }}</td>
                    <td>{{ formatDate(form.created_at) }}</td>
                    <td>
                        <button class="btn-info" @click="viewForm(form.form_id)">View</button>
                        <button class="btn-warning" @click="editForm(form.form_id)">Edit</button>
                        <button class="btn-danger" @click="deleteForm(form.form_id)">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal-overlay" :class="{ active: showModal }" @click.self="closeModal">
        <div class="modal-container">
            <h3>{{ selectedForm.title }}</h3>
            <p><strong>Description:</strong> {{ selectedForm.description || '-' }}</p>
            <p><strong>Status:</strong> {{ selectedForm.status }}</p>
            <p><strong>Created:</strong> {{ formatDate(selectedForm.created_at) }}</p>

            <pre v-if="selectedForm.form_data">{{ formatJson(selectedForm.form_data) }}</pre>

            <button class="btn-secondary" @click="closeModal" style="margin-top:15px;">Close</button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script src="<?= base_url('js/forms/index.js') ?>"></script>
<?= $this->endSection() ?>


<style>
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;

    background: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-overlay.active {
    display: flex;
}

.btn-secondary {
    background: #6c757d;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
}
</style>