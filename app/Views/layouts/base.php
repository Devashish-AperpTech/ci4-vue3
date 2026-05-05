<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'CI4-VUE3 Admin' ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; overflow: hidden; }
        
        .sidebar {
            position: fixed; left: 0; top: 0; width: 280px; height: 100%;
            background-color: #000418;
            color: white; overflow-y: auto;
        }
        .sidebar-header { padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); text-align: center; }
        .sidebar-header h2 { font-size: 22px; }
        .sidebar-menu { padding: 20px 0; }
        .menu-item {
            padding: 12px 25px; display: flex; align-items: center; gap: 12px;
            color: rgba(255,255,255,0.8); text-decoration: none; transition: all 0.3s;
        }
        .menu-item:hover, .menu-item.active { background: rgba(255,255,255,0.1); color: white; }
        .menu-item.active { background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); }
        
        .main-content { margin-left: 280px; height: 100vh; display: flex; flex-direction: column; }
        .top-bar {
            background: white; padding: 15px 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex; justify-content: space-between; align-items: center;
        }
        .page-title h1 { font-size: 24px; color: #333; }
        .user-menu { display: flex; align-items: center; gap: 20px; }
        .logout-btn { background: #dc3545; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; }
        .content-area { flex: 1; overflow-y: auto; padding: 30px; }
        
        .form-container { background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); max-width: 800px; margin: 0 auto; }
        .data-table { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .data-table table { width: 100%; border-collapse: collapse; }
        .data-table th { background: #f8f9fa; padding: 15px; text-align: left; }
        .data-table td { padding: 15px; border-bottom: 1px solid #f0f0f0; }
        .btn-primary { background-color: #011428; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; margin-left: 4px; }
        .btn-danger { background: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; margin-left: 4px; }
        .btn-warning { background: #ffc107; color: #333; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; margin-left: 4px; }
        .btn-info { background: #02327b; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; margin-left: 4px; }
        .error-message { background: #fee; color: #dc3545; padding: 10px; border-radius: 6px; margin-bottom: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .success-message { background: #d4edda; color: #28a745; padding: 10px; border-radius: 6px; margin-bottom: 20px; }
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000; }
        .modal-overlay.active { display: flex; }
        .modal-container { background: white; padding: 30px; border-radius: 12px; width: 500px; max-width: 90%; max-height: 80%; overflow-y: auto; }
    </style>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="<?= base_url('js/components/pagination.js') ?>"></script>
</head>
<body>
    <div id="app">
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>CI4-VUE3</h2>
                <p>Admin Panel</p>
            </div>
            <div class="sidebar-menu">
                <a href="/dashboard" class="menu-item" :class="{ active: currentPage === 'dashboard' }">📊 Dashboard</a>
                <a href="/forms" class="menu-item" :class="{ active: currentPage === 'forms' }">📝 Forms</a>
                <a href="/invoices" class="menu-item" :class="{ active: currentPage === 'invoices' }">🧾 Invoices</a>
                <a href="/customers" class="menu-item" :class="{ active: currentPage === 'customers' }">👥 Customers</a>
                <a href="/settings" class="menu-item" :class="{ active: currentPage === 'settings' }">⚙️ Settings</a>
            </div>
        </div>

        <div class="main-content">
            <div class="top-bar">
                <div class="page-title"><h1><?= $title ?? 'Dashboard' ?></h1></div>
                <div class="user-menu">
                    <span>Welcome, <?= session()->get('customer_name') ?></span>
                    <button class="logout-btn" onclick="window.location.href='/logout'">
                        Logout
                    </button>
                </div>
            </div>
            <div class="content-area">
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>

    <script>
        // Global Vue App for sidebar only
        // const { createApp, ref } = Vue;
        // const globalApp = createApp({
        //     setup() {
        //         const currentPage = ref('<?= $activePage ?? 'dashboard' ?>');
        //         const logout = () => window.location.href = '/logout';
        //         return { currentPage, logout };
        //     }
        // }).mount('#app');
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>
