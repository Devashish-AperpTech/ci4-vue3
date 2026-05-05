<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div id="settings-vue">
    <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
        <div>
            <h2 style="margin-bottom:6px;">Account Settings</h2>
            <div style="color:#58606f;">Manage your profile and security preferences</div>
        </div>
        <div style="padding:8px 12px;background:#e6eefc;color:#123a7a;border-radius:999px;font-size:12px;font-weight:600;">
            Secure Workspace
        </div>
    </div>

    <div v-if="errorMessage" class="error-message">{{ errorMessage }}</div>
    <div v-if="successMessage" class="success-message">{{ successMessage }}</div>

    <div style="background:linear-gradient(135deg,#ffffff 0%,#f7faff 100%);border:1px solid #e4ebf7;border-radius:16px;box-shadow:0 10px 24px rgba(2,50,123,0.08);overflow:hidden;">
        <div style="padding:20px 24px;border-bottom:1px solid #e9eef8;background:linear-gradient(90deg,#f4f8ff 0%,#ffffff 100%);">
            <h3 style="margin-bottom:4px;">Profile & Security</h3>
            <div style="color:#5e697d;font-size:14px;">Keep your account information up to date and protect your login credentials.</div>
        </div>

        <div style="padding:24px;">
            <form @submit.prevent="saveProfile">
                <h4 style="margin-bottom:14px;color:#1d2a42;">Profile Information</h4>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:14px;">
                    <div>
                        <label style="display:block;margin-bottom:6px;font-weight:600;color:#35425a;">Full Name *</label>
                        <input v-model.trim="profile.name" type="text" required style="width:100%;padding:11px 12px;border:1px solid #d7dbe3;border-radius:8px;background:#fff;">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:6px;font-weight:600;color:#35425a;">Email *</label>
                        <input v-model.trim="profile.email" type="email" required style="width:100%;padding:11px 12px;border:1px solid #d7dbe3;border-radius:8px;background:#fff;">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:6px;font-weight:600;color:#35425a;">VAT Code</label>
                        <input v-model.trim="profile.vat_code" type="text" style="width:100%;padding:11px 12px;border:1px solid #d7dbe3;border-radius:8px;background:#fff;">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:6px;font-weight:600;color:#35425a;">Identifier Code</label>
                        <input :value="profile.identifier_code || 'NULL'" disabled style="width:100%;padding:11px 12px;border:1px solid #e4e7ee;background:#f5f7fb;border-radius:8px;color:#5d6a7f;">
                    </div>
                </div>
                <div style="margin-top:16px;">
                    <button type="submit" class="btn-primary" :disabled="loadingProfileSave">
                        {{ loadingProfileSave ? 'Saving...' : 'Update Profile' }}
                    </button>
                </div>
            </form>

            <div style="height:1px;background:#e9eef8;margin:24px 0;"></div>

            <form @submit.prevent="savePassword">
                <h4 style="margin-bottom:14px;color:#1d2a42;">Password & Security</h4>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:14px;">
                    <div>
                        <label style="display:block;margin-bottom:6px;font-weight:600;color:#35425a;">Current Password *</label>
                        <input v-model="password.current_password" type="password" required style="width:100%;padding:11px 12px;border:1px solid #d7dbe3;border-radius:8px;background:#fff;">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:6px;font-weight:600;color:#35425a;">New Password *</label>
                        <input v-model="password.new_password" type="password" required style="width:100%;padding:11px 12px;border:1px solid #d7dbe3;border-radius:8px;background:#fff;">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:6px;font-weight:600;color:#35425a;">Confirm Password *</label>
                        <input v-model="password.confirm_password" type="password" required style="width:100%;padding:11px 12px;border:1px solid #d7dbe3;border-radius:8px;background:#fff;">
                    </div>
                </div>
                <div style="margin-top:16px;">
                    <button type="submit" class="btn-primary" :disabled="loadingPasswordSave">
                        {{ loadingPasswordSave ? 'Updating...' : 'Update Password' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/settings/index.js') ?>"></script>
<?= $this->endSection() ?>
