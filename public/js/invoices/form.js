function cloneSchema(schema) {
    return JSON.parse(JSON.stringify(schema || {}));
}

function initializeField(field) {
    if (!field || typeof field !== 'object') return field;

    if (field.type === 'group') {
        field.fields = (field.fields || []).map(initializeField);
        return field;
    }

    if (field.type === 'array') {
        field.items = Array.isArray(field.items) && field.items.length > 0
            ? field.items.map(item => item.map(initializeField))
            : [(field.fields || []).map(child => initializeField(cloneSchema(child)))];
        return field;
    }

    if (field.value === undefined) {
        field.value = field.type === 'select' && (field.options || []).length === 1
            ? field.options[0].value
            : '';
    }

    return field;
}

function initialSchema() {
    const savedData = window.initialInvoiceData?.invoice_data;
    let schema = null;

    if (savedData) {
        try {
            schema = typeof savedData === 'string' ? JSON.parse(savedData) : cloneSchema(savedData);
        } catch (e) {
            schema = null;
        }
    }

    if (!schema) {
        schema = cloneSchema(window.invoiceSchema);
    }

    schema.form.sections = (schema.form?.sections || []).map(section => {
        section.fields = (section.fields || []).map(initializeField);
        return section;
    });

    return schema;
}

const InvoiceField = {
    name: 'InvoiceField',
    props: {
        field: { type: Object, required: true },
        level: { type: Number, default: 0 }
    },
    template: `
        <div v-if="isVisible(field)">
            <div v-if="field.type === 'group'" class="invoice-group" :class="{ 'invoice-subsection': level > 0 }">
                <div class="invoice-group-title">
                    {{ field.label }}<span v-if="field.required" class="invoice-required"> *</span>
                </div>
                <div class="invoice-fields-grid" :style="gridStyle(field)">
                    <invoice-field
                        v-for="child in field.fields || []"
                        :key="child.id"
                        :field="child"
                        :level="level + 1">
                    </invoice-field>
                </div>
            </div>

            <div v-else-if="field.type === 'array'" class="invoice-array">
                <div class="invoice-array-header">
                    <div class="invoice-group-title">
                        {{ field.label }}<span v-if="field.required" class="invoice-required"> *</span>
                    </div>
                    <button type="button" class="invoice-text-button" @click="addArrayItem(field)">+ Add</button>
                </div>

                <div v-for="(item, index) in field.items" :key="index" class="invoice-array-item">
                    <div class="invoice-array-item-header">
                        <span>{{ field.itemLabel || 'Item' }} {{ index + 1 }}</span>
                        <button v-if="field.items.length > 1" type="button" class="invoice-text-button" @click="removeArrayItem(field, index)">Remove</button>
                    </div>
                    <div class="invoice-fields-grid">
                        <invoice-field
                            v-for="child in item"
                            :key="child.id"
                            :field="child"
                            :level="level + 1">
                        </invoice-field>
                    </div>
                </div>
            </div>

            <div v-else class="invoice-field">
                <label class="invoice-label">
                    {{ field.label }}<span v-if="field.required" class="invoice-required"> *</span>
                </label>

                <select
                    v-if="field.type === 'select'"
                    v-model="field.value"
                    class="invoice-input"
                    :required="field.required">
                    <option value="">Select</option>
                    <option v-for="option in field.options || []" :key="option.value" :value="option.value">
                        {{ option.label }}
                    </option>
                </select>

                <textarea
                    v-else-if="field.type === 'textarea'"
                    v-model="field.value"
                    class="invoice-input invoice-textarea"
                    :placeholder="field.placeholder || ''"
                    :required="field.required"
                    :maxlength="field.validation?.maxLength || null">
                </textarea>

                <input
                    v-else
                    :type="inputType(field)"
                    v-model="field.value"
                    class="invoice-input"
                    :placeholder="field.placeholder || ''"
                    :required="field.required"
                    :maxlength="field.validation?.maxLength || null"
                    :pattern="field.validation?.pattern || null"
                    :step="field.type === 'number' ? 'any' : null">
            </div>
        </div>
    `,
    methods: {
        inputType(field) {
            return ['text', 'email', 'date', 'number'].includes(field.type) ? field.type : 'text';
        },

        gridStyle(field) {
            const columns = Math.max(1, Math.min(Number(field.columns || 2), 3));
            return { gridTemplateColumns: `repeat(${columns}, minmax(0, 1fr))` };
        },

        isVisible(field) {
            if (!field.visibility) return true;
            const current = this.findValueById(field.visibility.dependsOn);

            if (field.visibility.condition === 'equals') {
                return String(current) === String(field.visibility.value);
            }

            return true;
        },

        findValueById(id) {
            return findFieldValue(window.invoiceFormApp?.schema?.form?.sections || [], id);
        },

        addArrayItem(field) {
            field.items.push((field.fields || []).map(child => initializeField(cloneSchema(child))));
        },

        removeArrayItem(field, index) {
            field.items.splice(index, 1);
        }
    }
};

function findFieldValue(nodes, id) {
    for (const node of nodes || []) {
        const fields = node.fields || [];
        for (const field of fields) {
            if (field.id === id) return field.value;
            if (field.type === 'group') {
                const value = findFieldValue([{ fields: field.fields || [] }], id);
                if (value !== undefined) return value;
            }
            if (field.type === 'array') {
                for (const item of field.items || []) {
                    const value = findFieldValue([{ fields: item }], id);
                    if (value !== undefined) return value;
                }
            }
        }
    }
    return undefined;
}

window.invoiceFormApp = Vue.createApp({
    components: {
        InvoiceField
    },

    data() {
        return {
            invoice: { ...window.initialInvoiceData },
            schema: initialSchema(),
            submitting: false,
            error: '',
            success: ''
        };
    },

    methods: {
        async submitInvoice() {
            this.error = '';
            this.success = '';

            if (!String(this.invoice.InvoiceNumber || '').trim()) {
                this.error = 'Invoice number is required';
                return;
            }

            const missing = this.findMissingRequiredFields(this.schema.form.sections || []);
            if (missing.length > 0) {
                this.error = `${missing[0]} is required`;
                return;
            }

            this.submitting = true;

            try {
                const fd = new FormData();
                fd.append('InvoiceNumber', this.invoice.InvoiceNumber);
                fd.append('invoice_data', JSON.stringify(this.schema));

                const url = window.invoiceMode === 'create'
                    ? '/api/invoices'
                    : `/api/invoices/update/${window.invoiceId}`;

                const response = await axios.post(url, fd, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (response.data.success) {
                    this.success = response.data.message || 'Invoice saved';

                    setTimeout(() => {
                        window.location.href = response.data.redirect || '/invoices';
                    }, 1000);
                } else {
                    this.error =
                        response.data.errors?.join(', ') ||
                        response.data.message ||
                        'Save failed';
                }
            } catch (err) {
                this.error = 'Error saving invoice';
            } finally {
                this.submitting = false;
            }
        },

        findMissingRequiredFields(nodes) {
            const missing = [];

            for (const node of nodes || []) {
                for (const field of node.fields || []) {
                    missing.push(...this.missingForField(field));
                }
            }

            return missing;
        },

        missingForField(field) {
            if (field.visibility && !this.isVisible(field)) return [];

            if (field.type === 'group') {
                return this.findMissingRequiredFields([{ fields: field.fields || [] }]);
            }

            if (field.type === 'array') {
                return (field.items || []).flatMap(item => this.findMissingRequiredFields([{ fields: item }]));
            }

            return field.required && !String(field.value ?? '').trim() ? [field.label || field.id] : [];
        },

        isVisible(field) {
            if (!field.visibility) return true;
            const current = findFieldValue(this.schema.form.sections || [], field.visibility.dependsOn);

            if (field.visibility.condition === 'equals') {
                return String(current) === String(field.visibility.value);
            }

            return true;
        }
    }
}).mount('#invoice-form-vue');
