<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div id="form-app" class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3><?= $mode === 'create' ? 'Create New Form' : 'Edit Form' ?></h3>
                </div>
                <div class="card-body">
                    <div v-if="error" class="alert alert-danger">{{ error }}</div>
                    <div v-if="success" class="alert alert-success">{{ success }}</div>
                    
                    <form @submit.prevent="submitForm">
                        <div class="mb-3">
                            <label class="form-label">Form Title *</label>
                            <input type="text" class="form-control" v-model="formData.title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" v-model="formData.description" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Additional Data (JSON)</label>
                            <textarea class="form-control font-monospace" v-model="formData.formData" rows="6" placeholder='{"key": "value"}'></textarea>
                            <small class="text-muted">Enter valid JSON format</small>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" :disabled="submitting">
                                {{ submitting ? 'Saving...' : 'Save Form' }}
                            </button>
                            <a href="/forms" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function() {
    const { createApp, ref } = Vue;
    
    const app = createApp({
        setup() {
            const formData = ref({
                title: <?= json_encode($formData['title'] ?? '') ?>,
                description: <?= json_encode($formData['description'] ?? '') ?>,
                formData: <?= json_encode($formData['form_data'] ?? '{}') ?>
            });
            const submitting = ref(false);
            const error = ref('');
            const success = ref('');
            
            const submitForm = async () => {
                error.value = '';
                success.value = '';
                
                if (!formData.value.title.trim()) {
                    error.value = 'Title is required';
                    return;
                }
                
                // Validate JSON
                if (formData.value.formData && formData.value.formData.trim()) {
                    try {
                        JSON.parse(formData.value.formData);
                    } catch (e) {
                        error.value = 'Invalid JSON: ' + e.message;
                        return;
                    }
                }
                
                submitting.value = true;
                
                try {
                    const fd = new FormData();
                    fd.append('title', formData.value.title);
                    fd.append('description', formData.value.description);
                    fd.append('form_data', formData.value.formData);
                    
                    let url = '/forms/save';
                    <?php if ($mode === 'edit'): ?>
                    url = '/forms/update/<?= $formId ?>';
                    <?php endif; ?>
                    
                    const response = await axios.post(url, fd, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    
                    if (response.data.success) {
                        success.value = response.data.message || 'Form saved';
                        setTimeout(() => {
                            window.location.href = response.data.redirect || '/forms';
                        }, 1000);
                    } else {
                        error.value = response.data.errors?.join(', ') || response.data.message || 'Save failed';
                    }
                } catch (err) {
                    error.value = 'Error saving form';
                } finally {
                    submitting.value = false;
                }
            };
            
            return { formData, submitting, error, success, submitForm };
        }
    });
    
    app.mount('#form-app');
})();
</script>
<?= $this->endSection() ?>