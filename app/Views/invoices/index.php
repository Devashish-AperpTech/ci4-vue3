<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div id="invoice-vue">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>My Invoices</h2>
        <a href="<?= base_url('invoices/create') ?>" class="btn-primary" style="text-decoration: none;">+ Create New Invoice</a>
    </div>

    <div v-if="errorMessage" class="error-message">{{ errorMessage }}</div>
    <div v-if="successMessage" class="success-message" style="background:#d4edda;color:#28a745;padding:10px;border-radius:6px;margin-bottom:20px;">
        {{ successMessage }}
    </div>

    <div v-if="loading" style="text-align:center;padding:40px;">Loading...</div>

    <div v-else-if="invoices.length === 0" style="text-align:center;padding:40px;color:#666;">
        No invoices created yet. Click "Create New Invoice" to get started!
    </div>

    <div v-else class="data-table">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Invoice #</th>
                    <th>Issue Date</th>
                    <th>Year</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Payment Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="invoice in invoices" :key="invoice.id">
                    <td>{{ invoice.id }}</td>
                    <td>{{ invoice.InvoiceNumber }}</td>
                    <td>{{ formatDate(invoice.IssueDate) }}</td>
                    <td>{{ invoice.Year || '-' }}</td>
                    <td>{{ invoice.Status || '-' }}</td>
                    <td>{{ formatMoney(invoice.TotalAmount) }}</td>
                    <td>{{ invoice.Paid || '-' }}</td>
                    <td>{{ invoice.PaymentStatus || '-' }}</td>
                    <td>
                        <button class="btn-info" @click="viewInvoice(invoice.id)">View</button>
                        <button class="btn-warning" @click="editInvoice(invoice.id)">Edit</button>
                        <button class="btn-danger" @click="deleteInvoice(invoice.id)">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="modal-overlay" :class="{ active: showModal }" @click.self="closeModal">
        <div class="modal-container">
            <h3>{{ selectedInvoice.InvoiceNumber }}</h3>
            <p><strong>Issue Date:</strong> {{ formatDate(selectedInvoice.IssueDate) }}</p>
            <p><strong>Creation Date:</strong> {{ formatDateTime(selectedInvoice.CreationDate) }}</p>
            <p><strong>Expiration Date:</strong> {{ formatDate(selectedInvoice.ExpirationDate) }}</p>
            <p><strong>Custom Expiration:</strong> {{ formatDate(selectedInvoice.CustomExpirationDate) }}</p>
            <p><strong>Year:</strong> {{ selectedInvoice.Year || '-' }}</p>
            <p><strong>Status:</strong> {{ selectedInvoice.Status || '-' }}</p>
            <p><strong>Total Amount:</strong> {{ formatMoney(selectedInvoice.TotalAmount) }}</p>
            <p><strong>Tax Amount:</strong> {{ formatMoney(selectedInvoice.TaxAmount) }}</p>
            <p><strong>Paid:</strong> {{ selectedInvoice.Paid || '-' }}</p>
            <p><strong>Payment Status:</strong> {{ selectedInvoice.PaymentStatus || '-' }}</p>
            <p><strong>Payment Mean:</strong> {{ selectedInvoice.PaymentMean || '-' }}</p>
            <p><strong>Custom Payment Mean:</strong> {{ selectedInvoice.CustomPaymentMean || '-' }}</p>

            <button class="btn-secondary" @click="closeModal" style="margin-top:15px;">Close</button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/invoices/index.js') ?>"></script>
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
