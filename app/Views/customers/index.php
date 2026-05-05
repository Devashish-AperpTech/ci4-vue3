<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div id="customers-vue">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:20px;flex-wrap:wrap;">
        <h2>Customer Management</h2>
        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            <input
                v-model.trim="searchTerm"
                type="text"
                placeholder="Search by name, email, VAT, identifier"
                style="padding:10px 12px;border:1px solid #d7dbe3;border-radius:8px;min-width:320px;"
            />
            <button class="btn-primary" @click="openCreateModal">+ Create Customer</button>
        </div>
    </div>

    <div v-if="errorMessage" class="error-message">{{ errorMessage }}</div>
    <div v-if="successMessage" class="success-message">{{ successMessage }}</div>

    <div class="data-table">
        <div style="overflow-x:auto;">
        <table style="min-width:980px;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>VAT</th>
                    <th>Identifier</th>
                    <th>Parent</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="loading">
                    <td colspan="8" style="text-align:center;color:#666;padding:28px;">Loading customers...</td>
                </tr>
                <tr v-else-if="filteredCustomers.length === 0">
                    <td colspan="8" style="text-align:center;color:#666;padding:28px;">No customers found.</td>
                </tr>
                <tr v-for="(customer, index) in filteredCustomers" :key="customer.customer_id">
                    <td>{{ (pagination.page - 1) * pagination.perPage + (index+1) }}</td>
                    <td>{{ customer.customer_name }}</td>
                    <td>{{ customer.customer_email }}</td>
                    <td>{{ customer.customer_vat_code || '-' }}</td>
                    <td>{{ customer.customer_identifier_code || 'NULL' }}</td>
                    <td>{{ customer.parent_name || customer.parent_id || '-' }}</td>
                    <td>{{ formatDate(customer.created_at) }}</td>
                    <td>
                        <button class="btn-info" @click="openViewModal(customer)">View</button>
                        <button class="btn-warning" @click="openEditModal(customer)">Edit</button>
                        <button class="btn-danger" @click="deleteCustomer(customer.customer_id)">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
        </div>
    </div>

    <global-pagination v-if="shouldShowPagination" :pagination="pagination" :loading="loading" :per-page="perPage" :per-page-options="[10, 25, 50]" @change-page="goToPage" @change-per-page="onPerPageChange"></global-pagination>

    <div class="modal-overlay" :class="{ active: showViewModal }" @click.self="closeViewModal">
        <div class="modal-container">
            <h3 style="margin-bottom:16px;">Customer Profile</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div style="padding:10px;background:#f8fafc;border-radius:8px;"><strong>ID:</strong> {{ selectedCustomer.customer_id }}</div>
                <div style="padding:10px;background:#f8fafc;border-radius:8px;"><strong>Parent:</strong> {{ selectedCustomer.parent_name || selectedCustomer.parent_id || '-' }}</div>
                <div style="padding:10px;background:#f8fafc;border-radius:8px;"><strong>Name:</strong> {{ selectedCustomer.customer_name }}</div>
                <div style="padding:10px;background:#f8fafc;border-radius:8px;"><strong>Email:</strong> {{ selectedCustomer.customer_email }}</div>
                <div style="padding:10px;background:#f8fafc;border-radius:8px;"><strong>VAT:</strong> {{ selectedCustomer.customer_vat_code || '-' }}</div>
                <div style="padding:10px;background:#f8fafc;border-radius:8px;"><strong>Identifier:</strong> {{ selectedCustomer.customer_identifier_code || 'NULL' }}</div>
            </div>
            <div style="margin-top:12px;padding:10px;background:#f8fafc;border-radius:8px;"><strong>Created:</strong> {{ formatDateTime(selectedCustomer.created_at) }}</div>
            <button class="btn-primary" @click="closeViewModal" style="margin-top:14px;">Close</button>
        </div>
    </div>

    <div class="modal-overlay" :class="{ active: showCreateModal }" @click.self="closeCreateModal">
        <div class="modal-container">
            <h3 style="margin-bottom:16px;">Create Customer</h3>
            <form @submit.prevent="submitCreateCustomer">
                <div v-if="modalError" class="error-message">{{ modalError }}</div>

                <div class="form-group" style="margin-bottom:12px;">
                    <label>Name *</label>
                    <input v-model.trim="form.name" type="text" required style="width:100%;padding:10px;border:1px solid #d7dbe3;border-radius:6px;">
                </div>
                <div class="form-group" style="margin-bottom:12px;">
                    <label>Email *</label>
                    <input v-model.trim="form.email" type="email" required style="width:100%;padding:10px;border:1px solid #d7dbe3;border-radius:6px;">
                </div>
                <div class="form-group" style="margin-bottom:12px;">
                    <label>Password * (same as email for 1st time)</label>
                    <input v-model="form.email" type="password" required style="width:100%;padding:10px;border:1px solid #d7dbe3;border-radius:6px;" disabled>
                </div>
                <div class="form-group" style="margin-bottom:12px;">
                    <label>VAT Code</label>
                    <input v-model.trim="form.vat_code" type="text" style="width:100%;padding:10px;border:1px solid #d7dbe3;border-radius:6px;">
                </div>

                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button type="button" class="btn-warning" @click="closeCreateModal">Cancel</button>
                    <button type="submit" class="btn-primary" :disabled="submitting">
                        {{ submitting ? 'Saving...' : 'Create Customer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" :class="{ active: showEditModal }" @click.self="closeEditModal">
        <div class="modal-container">
            <h3 style="margin-bottom:16px;">Edit Customer</h3>
            <form @submit.prevent="submitEditCustomer">
                <div v-if="modalError" class="success-message" style="background:#fee2e2;color:#991b1b;">{{ modalError }}</div>

                <div class="form-group" style="margin-bottom:12px;">
                    <label>Name *</label>
                    <input v-model.trim="form.name" type="text" required style="width:100%;padding:10px;border:1px solid #d7dbe3;border-radius:6px;">
                </div>
                <div class="form-group" style="margin-bottom:12px;">
                    <label>Email *</label>
                    <input v-model.trim="form.email" type="email" required style="width:100%;padding:10px;border:1px solid #d7dbe3;border-radius:6px;">
                </div>
                <div class="form-group" style="margin-bottom:12px;">
                    <label>Password (leave blank to keep old)</label>
                    <input v-model="form.password" type="password" style="width:100%;padding:10px;border:1px solid #d7dbe3;border-radius:6px;">
                </div>
                <div class="form-group" style="margin-bottom:12px;">
                    <label>VAT Code</label>
                    <input v-model.trim="form.vat_code" type="text" style="width:100%;padding:10px;border:1px solid #d7dbe3;border-radius:6px;">
                </div>
                <div class="form-group" style="margin-bottom:12px;">
                    <label>Identifier Code</label>
                    <input v-model.trim="form.customer_identifier_code" type="text" style="width:100%;padding:10px;border:1px solid #d7dbe3;border-radius:6px;">
                </div>

                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button type="button" class="btn-warning" @click="closeEditModal">Cancel</button>
                    <button type="submit" class="btn-primary" :disabled="submitting">
                        {{ submitting ? 'Saving...' : 'Update Customer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/customers/index.js') ?>"></script>
<?= $this->endSection() ?>
