<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div id="form-vue">
    <div class="form-container">
        <h2><?= $mode === 'create' ? 'Create New Form' : 'Edit Form' ?></h2>
        
        <div v-if="error" class="error-message">{{ error }}</div>
        <div v-if="success" class="success-message" style="background:#d4edda;color:#28a745;padding:10px;border-radius:6px;margin-bottom:20px;">
            {{ success }}
        </div>
        
        <form @submit.prevent="submitForm">
            <div class="form-group" style="margin-bottom:20px;">
                <label>Form Title *</label>
                <input type="text" v-model="form.title" class="form-control"
                       style="width:100%;padding:12px;border:1px solid #ddd;border-radius:6px;" required>
            </div>
            
            <div class="form-group" style="margin-bottom:20px;">
                <label>Description</label>
                <textarea v-model="form.description" rows="3"
                          style="width:100%;padding:12px;border:1px solid #ddd;border-radius:6px;"></textarea>
            </div>
            
            <div class="form-group" style="margin-bottom:20px;">
                <label>Additional Data (JSON)</label>
                <textarea v-model="form.formData" rows="6"
                          style="width:100%;padding:12px;font-family:monospace;border:1px solid #ddd;border-radius:6px;"
                          placeholder='{"key": "value"}'></textarea>
                <small style="color:#666;">Enter valid JSON format</small>
            </div>
            
            <div style="margin-top:30px;">
                <button type="submit" class="btn-primary" :disabled="submitting">
                    {{ submitting ? 'Saving...' : 'Save Form' }}
                </button>

                <a href="<?= base_url('forms') ?>" class="btn-secondary"
                   style="background:#6c757d;color:white;padding:10px 20px;border-radius:6px;text-decoration:none;margin-left:10px;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<script>
window.initialData = <?= json_encode([
    'title' => $formData['title'] ?? '',
    'description' => $formData['description'] ?? '',
    'formData' => $formData['form_data'] ?? '{}'
]) ?>;

window.formMode = "<?= $mode ?>";
window.formId = "<?= $formId ?? '' ?>";
</script>

<script src="<?= base_url('js/forms/form.js') ?>"></script>

<?= $this->endSection() ?>