<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<style>
.invoice-form-container {
    max-width: 1080px;
    padding: 0;
    overflow: hidden;
}

.invoice-form-header {
    padding: 24px 28px;
    border-bottom: 1px solid #e5e7eb;
    background: #ffffff;
}

.invoice-form-header h2 {
    color: #1f2937;
    font-size: 24px;
    line-height: 1.2;
    margin: 0;
}

.invoice-form-header p {
    color: #6b7280;
    margin-top: 6px;
    font-size: 14px;
}

.invoice-form-body {
    padding: 28px;
}

.invoice-primary-field {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 18px;
    margin-bottom: 22px;
}

.invoice-label {
    display: block;
    color: #374151;
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 8px;
}

.invoice-input {
    width: 100%;
    min-height: 42px;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    color: #111827;
    background: #ffffff;
    font-size: 14px;
}

.invoice-input:focus {
    outline: none;
    border-color: #6b7280;
    box-shadow: 0 0 0 3px rgba(107, 114, 128, 0.12);
}

.invoice-schema {
    display: grid;
    gap: 14px;
}

.invoice-accordion {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #ffffff;
    overflow: hidden;
}

.invoice-accordion[open] {
    border-color: #d1d5db;
}

.invoice-accordion summary {
    cursor: pointer;
    padding: 15px 18px;
    font-weight: 700;
    color: #1f2937;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    background: #f9fafb;
    list-style: none;
}

.invoice-accordion summary::-webkit-details-marker {
    display: none;
}

.invoice-accordion summary::after {
    content: '+';
    color: #4b5563;
    font-size: 22px;
    line-height: 1;
    font-weight: 400;
}

.invoice-accordion[open] > summary::after {
    content: '-';
}

.invoice-section-title {
    display: flex;
    flex-direction: column;
    gap: 3px;
    min-width: 0;
}

.invoice-section-name {
    letter-spacing: 0.04em;
    overflow-wrap: anywhere;
}



.invoice-accordion-body {
    padding: 18px;
    display: grid;
    gap: 14px;
}

.invoice-subsection {
    margin-left: 14px;
}

.invoice-fields-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}

.invoice-field {
    min-width: 0;
}

.invoice-required {
    color: #b42318;
    font-weight: 700;
}

.invoice-group {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 16px;
    background: #ffffff;
}

.invoice-group-title,
.invoice-array-item-header {
    color: #374151;
    font-weight: 700;
    font-size: 14px;
    margin-bottom: 14px;
}

.invoice-array {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 16px;
    background: #ffffff;
}

.invoice-array-header,
.invoice-array-item-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.invoice-array-item {
    border-top: 1px solid #e5e7eb;
    padding-top: 14px;
    margin-top: 14px;
}

.invoice-text-button {
    background: transparent;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    color: #374151;
    cursor: pointer;
    padding: 6px 10px;
}

.invoice-textarea {
    min-height: 84px;
    resize: vertical;
}

.invoice-actions {
    display: flex;
    gap: 10px;
    align-items: center;
    margin-top: 28px;
    padding-top: 22px;
    border-top: 1px solid #e5e7eb;
}

.invoice-cancel-link {
    background: #6c757d;
    color: white;
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
}

@media (max-width: 820px) {
    .invoice-form-body,
    .invoice-form-header {
        padding: 20px;
    }

    .invoice-fields-grid {
        grid-template-columns: 1fr;
    }

    .invoice-subsection {
        margin-left: 0;
    }

    .invoice-actions {
        flex-direction: column;
        align-items: stretch;
    }

    .invoice-actions .btn-primary,
    .invoice-cancel-link {
        width: 100%;
        text-align: center;
        margin-left: 0 !important;
    }
}
</style>

<div id="invoice-form-vue">
    <div class="form-container invoice-form-container">
        <div class="invoice-form-header">
            <h2><?= $mode === 'create' ? 'Create New Invoice' : 'Edit Invoice' ?></h2>
            <p>Start with the invoice number, then complete the enabled electronic invoice sections below.</p>
        </div>

        <div class="invoice-form-body">
            <div v-if="error" class="error-message">{{ error }}</div>
            <div v-if="success" class="success-message">
                {{ success }}
            </div>

            <form @submit.prevent="submitInvoice">
                <div class="invoice-primary-field">
                    <label class="invoice-label">Invoice Number <span class="invoice-required">*</span></label>
                    <input type="text" v-model="invoice.InvoiceNumber" class="invoice-input" required>
                </div>

                <div class="invoice-schema">
                    <details v-for="section in schema.form.sections" :key="section.id" class="invoice-accordion" open>
                        <summary>
                            <span class="invoice-section-title">
                                <span class="invoice-section-name">{{ section.title }}<span v-if="section.required" class="invoice-required"> *</span></span>
                            </span>
                        </summary>
                        <div class="invoice-accordion-body">
                            <invoice-field
                                v-for="field in section.fields"
                                :key="field.id"
                                :field="field">
                            </invoice-field>
                        </div>
                    </details>
                </div>

                <div class="invoice-actions">
                    <button type="submit" class="btn-primary" :disabled="submitting">
                        {{ submitting ? 'Saving...' : 'Save Invoice' }}
                    </button>

                    <a href="<?= base_url('invoices') ?>" class="invoice-cancel-link">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
window.initialInvoiceData = <?= json_encode($invoiceData) ?>;
window.invoiceSchema = <?= json_encode($invoiceSchema) ?>;
window.invoiceMode = "<?= $mode ?>";
window.invoiceId = "<?= $invoiceId ?? '' ?>";
</script>
<script src="<?= base_url('js/invoices/form.js') ?>"></script>
<?= $this->endSection() ?>



