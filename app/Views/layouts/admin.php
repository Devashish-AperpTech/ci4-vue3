<!DOCTYPE html>
<html lang="en">
<head>
    <!-- app\Views\layouts\admin.php -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'CI4-VUE3 Admin' ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; overflow: hidden; }
        
        /* Sidebar */
        .sidebar {
            position: fixed; left: 0; top: 0; width: 280px; height: 100%;
            background: linear-gradient(135deg, #0a0e2a 0%, #1a1f3e 100%);
            color: white; overflow-y: auto; z-index: 100;
        }
        .sidebar-header { padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); text-align: center; }
        .sidebar-header h2 { font-size: 22px; margin-bottom: 5px; }
        .sidebar-header p { font-size: 12px; opacity: 0.7; }
        .sidebar-menu { padding: 20px 0; }
        .menu-item {
            padding: 12px 25px; display: flex; align-items: center; gap: 12px;
            color: rgba(255,255,255,0.8); text-decoration: none; transition: all 0.3s ease;
        }
        .menu-item:hover { background: rgba(255,255,255,0.1); color: white; }
        .menu-item.active { background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); }
        .menu-item .icon { width: 24px; font-size: 20px; }
        .menu-item .text { font-size: 14px; font-weight: 500; }
        
        /* Main Content */
        .main-content { margin-left: 280px; height: 100vh; display: flex; flex-direction: column; }
        .top-bar { background: white; padding: 15px 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .page-title h1 { font-size: 24px; color: #333; }
        .user-menu { display: flex; align-items: center; gap: 20px; }
        .user-name { color: #555; }
        .logout-btn { background: #dc3545; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; }
        .content-area { flex: 1; overflow-y: auto; padding: 30px; }
        
        /* Cards & Tables */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .stat-card h3 { font-size: 14px; color: #666; margin-bottom: 10px; }
        .stat-card .value { font-size: 32px; font-weight: bold; color: #333; }
        .form-container { background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); max-width: 800px; margin: 0 auto; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #333; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; }
        .data-table { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .data-table table { width: 100%; border-collapse: collapse; }
        .data-table th { background: #f8f9fa; padding: 15px; text-align: left; font-weight: 600; border-bottom: 1px solid #e0e0e0; }
        .data-table td { padding: 15px; border-bottom: 1px solid #f0f0f0; }
        .data-table tr:hover { background: #f8f9fa; }
        
        /* Buttons */
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; }
        .btn-danger { background: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; }
        .btn-warning { background: #ffc107; color: #333; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; }
        .btn-info { background: #17a2b8; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; }
        .btn-secondary { background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; margin-left: 10px; }
        
        .error-message { background: #fee; color: #dc3545; padding: 10px; border-radius: 6px; margin-bottom: 20px; }
        .success-message { background: #d4edda; color: #28a745; padding: 10px; border-radius: 6px; margin-bottom: 20px; }
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000; }
        .modal-overlay.active { display: flex; }
        .modal-container { background: white; padding: 30px; border-radius: 12px; width: 500px; max-width: 90%; }
    </style>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>
<body>
    <div id="app">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>CI4-VUE3</h2>
                <p>Admin Panel</p>
            </div>
            <div class="sidebar-menu">
                <a href="/dashboard" class="menu-item" :class="{ active: currentPage === 'dashboard' }">
                    <span class="icon">📊</span><span class="text">Dashboard</span>
                </a>
                <a href="/forms" class="menu-item" :class="{ active: currentPage === 'forms' }">
                    <span class="icon">📝</span><span class="text">Forms</span>
                </a>
                <a href="/invoices" class="menu-item" :class="{ active: currentPage === 'invoices' }">
                    <span class="icon">🧾</span><span class="text">Invoices</span>
                </a>
                <a href="/customers" class="menu-item" :class="{ active: currentPage === 'customers' }">
                    <span class="icon">👥</span><span class="text">Customers</span>
                </a>
                <a href="/settings" class="menu-item" :class="{ active: currentPage === 'settings' }">
                    <span class="icon">⚙️</span><span class="text">Settings</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="top-bar">
                <div class="page-title"><h1>{{ pageTitle }}</h1></div>
                <div class="user-menu">
                    <span class="user-name">Welcome, <?= session()->get('customer_name') ?></span>
                    <button class="logout-btn" @click="logout">Logout</button>
                </div>
            </div>
            <div class="content-area" id="page-content">
                <!-- Content loaded dynamically -->
                <div style="text-align: center; padding: 40px;">Loading...</div>
            </div>
        </div>
    </div>

    <script>
        const { createApp, ref, onMounted } = Vue;
        
        // Main Vue App for sidebar only
        const app = createApp({
            setup() {
                const currentPage = ref('<?= $page ?? 'dashboard' ?>');
                const pageTitle = ref('<?= $title ?? 'Dashboard' ?>');
                const logout = () => window.location.href = '/logout';
                return { currentPage, pageTitle, logout };
            }
        }).mount('#app');
        
        // Load page-specific content based on URL
        const loadContent = async () => {
            const path = window.location.pathname;
            const contentDiv = document.getElementById('page-content');
            
            if (path === '/dashboard') {
                await loadDashboard(contentDiv);
            } else if (path === '/forms') {
                await loadFormsList(contentDiv);
            } else if (path === '/forms/create') {
                loadFormCreate(contentDiv);
            } else if (path.startsWith('/forms/edit/')) {
                const formId = path.split('/').pop();
                loadFormEdit(contentDiv, formId);
            } else {
                contentDiv.innerHTML = '<div class="form-container"><h2>Page Under Construction</h2><p>This section is coming soon.</p></div>';
            }
        };
        
        const loadDashboard = async (container) => {
            try {
                const response = await axios.get('/api/dashboard-data');
                const data = response.data.data || { totalForms: 0, recentActivity: 0, recentForms: [] };
                container.innerHTML = `
                    <div class="stats-grid">
                        <div class="stat-card"><h3>Total Forms</h3><div class="value">${data.totalForms || 0}</div></div>
                        <div class="stat-card"><h3>Recent Activity</h3><div class="value">${data.recentActivity || 0}</div></div>
                    </div>
                    <div class="data-table">
                        <table>
                            <thead>
                                <tr><th>Form Title</th><th>Status</th><th>Created</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                ${(data.recentForms || []).map(f => `
                                    <tr>
                                        <td>${f.title || '-'}</td>
                                        <td>${f.status || '-'}</td>
                                        <td>${f.created_at ? new Date(f.created_at).toLocaleDateString() : '-'}</td>
                                        <td>
                                            <button class="btn-info" onclick="window.location.href='/forms/edit/${f.form_id || f.id}'">View</button>
                                        </td>
                                    </tr>
                                `).join('') || '<tr><td colspan="4">No forms found</td><td style="display:none"></td><td style="display:none"></td><td style="display:none"></td></tr>'}
                            </tbody>
                        </table>
                    </div>
                `;
            } catch (err) {
                console.error('Dashboard error:', err);
                container.innerHTML = '<div class="error-message">Failed to load dashboard: ' + (err.message || 'Unknown error') + '</div>';
            }
        };
        const loadFormsList = async (container) => {
            try {
                const response = await axios.get('/forms/getData');
                const forms = response.data.data || [];  // ← FIXED: handle empty data
                container.innerHTML = `
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h2>My Forms</h2>
                        <a href="/forms/create" class="btn-primary" style="text-decoration: none;">+ Create New Form</a>
                    </div>
                    <div class="data-table">
                        <table>
                            <thead>
                                <tr><th>ID</th><th>Title</th><th>Description</th><th>Status</th><th>Created</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                ${forms.map(f => `
                                    <tr>
                                        <td>${f.form_id || f.id || '-'}</td>
                                        <td>${f.title || '-'}</td>
                                        <td>${f.description || '-'}</td>
                                        <td>${f.status || '-'}</td>
                                        <td>${f.created_at ? new Date(f.created_at).toLocaleDateString() : '-'}</td>
                                        <td>
                                            <button class="btn-info" onclick="viewForm(${f.form_id || f.id})">View</button>
                                            <button class="btn-warning" onclick="window.location.href='/forms/edit/${f.form_id || f.id}'">Edit</button>
                                            <button class="btn-danger" onclick="deleteForm(${f.form_id || f.id})">Delete</button>
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                    <div id="modal" class="modal-overlay" onclick="closeModal()">
                        <div class="modal-container" onclick="event.stopPropagation()">
                            <div id="modal-content"></div>
                            <button class="btn-secondary" onclick="closeModal()">Close</button>
                        </div>
                    </div>
                `;
                
                // Define the viewForm function
                window.viewForm = async (id) => {
                    try {
                        const res = await axios.get('/forms/getData');
                        const formsList = res.data.data || [];
                        const form = formsList.find(f => (f.form_id || f.id) === id);
                        if (form) {
                            document.getElementById('modal-content').innerHTML = `
                                <h3>${form.title}</h3>
                                <p><strong>Description:</strong> ${form.description || '-'}</p>
                                <p><strong>Status:</strong> ${form.status}</p>
                                <p><strong>Created:</strong> ${new Date(form.created_at).toLocaleString()}</p>
                                <pre>${JSON.stringify(form.form_data, null, 2)}</pre>
                            `;
                            document.getElementById('modal').classList.add('active');
                        } else {
                            alert('Form not found');
                        }
                    } catch (err) {
                        console.error('Error loading form details:', err);
                        alert('Error loading form details');
                    }
                };
                
                window.closeModal = () => document.getElementById('modal').classList.remove('active');
                
                window.deleteForm = async (id) => {
                    if(confirm('Delete this form?')) {
                        try {
                            await axios.delete('/forms/delete/' + id, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                            loadFormsList(container);
                        } catch (err) {
                            alert('Error deleting form');
                        }
                    }
                };
            } catch (err) {
                console.error('Error loading forms list:', err);
                container.innerHTML = '<div class="error-message">Failed to load forms. Error: ' + (err.message || 'Unknown error') + '</div>';
            }
        };
                
        const loadFormCreate = (container) => {
            container.innerHTML = `
                <div class="form-container">
                    <h2>Create New Form</h2>
                    <div id="form-error" class="error-message" style="display:none"></div>
                    <div id="form-success" class="success-message" style="display:none"></div>
                    <form id="form-submit">
                        <div class="form-group"><label>Form Title *</label><input type="text" id="title" required></div>
                        <div class="form-group"><label>Description</label><textarea id="description" rows="3"></textarea></div>
                        <div class="form-group"><label>Additional Data (JSON)</label><textarea id="form_data" rows="6" placeholder='{"key":"value"}' style="font-family:monospace"></textarea></div>
                        <button type="submit" class="btn-primary">Save Form</button>
                        <a href="/forms" class="btn-secondary" style="text-decoration:none">Cancel</a>
                    </form>
                </div>
            `;
            document.getElementById('form-submit').onsubmit = async (e) => {
                e.preventDefault();
                const formData = new FormData();
                formData.append('title', document.getElementById('title').value);
                formData.append('description', document.getElementById('description').value);
                formData.append('form_data', document.getElementById('form_data').value);
                try {
                    const res = await axios.post('/forms/save', formData, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    if(res.data.success) window.location.href = '/forms';
                    else document.getElementById('form-error').innerText = res.data.errors?.join(', ') || 'Failed';
                } catch(err) { document.getElementById('form-error').innerText = 'Error saving form'; }
            };
        };
        
        const loadFormEdit = async (container, formId) => {
            try {
                const res = await axios.get('/forms/getData');
                const form = res.data.data.find(f => f.form_id == formId);
                if(!form) { container.innerHTML = '<div class="error-message">Form not found</div>'; return; }
                container.innerHTML = `
                    <div class="form-container">
                        <h2>Edit Form</h2>
                        <div id="form-error" class="error-message" style="display:none"></div>
                        <div id="form-success" class="success-message" style="display:none"></div>
                        <form id="form-submit">
                            <div class="form-group"><label>Form Title *</label><input type="text" id="title" value="${escapeHtml(form.title)}" required></div>
                            <div class="form-group"><label>Description</label><textarea id="description" rows="3">${escapeHtml(form.description || '')}</textarea></div>
                            <div class="form-group"><label>Additional Data (JSON)</label><textarea id="form_data" rows="6" style="font-family:monospace">${JSON.stringify(form.form_data, null, 2)}</textarea></div>
                            <button type="submit" class="btn-primary">Update Form</button>
                            <a href="/forms" class="btn-secondary" style="text-decoration:none">Cancel</a>
                        </form>
                    </div>
                `;
                document.getElementById('form-submit').onsubmit = async (e) => {
                    e.preventDefault();
                    const formData = new FormData();
                    formData.append('title', document.getElementById('title').value);
                    formData.append('description', document.getElementById('description').value);
                    formData.append('form_data', document.getElementById('form_data').value);
                    try {
                        const res = await axios.post('/forms/update/' + formId, formData, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                        if(res.data.success) window.location.href = '/forms';
                        else document.getElementById('form-error').innerText = res.data.errors?.join(', ') || 'Failed';
                    } catch(err) { document.getElementById('form-error').innerText = 'Error updating form'; }
                };
            } catch(err) { container.innerHTML = '<div class="error-message">Failed to load form</div>'; }
        };
        
        function escapeHtml(str) { if(!str) return ''; return str.replace(/[&<>]/g, function(m) { if(m === '&') return '&amp;'; if(m === '<') return '&lt;'; if(m === '>') return '&gt;'; return m; }); }
        
        loadContent();
    </script>
</body>
</html>