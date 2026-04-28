<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body { font-family: Arial; margin: 0; padding: 0; }
        nav { background: #010b35; color: white; padding: 5px 15px; display: flex; align-items: center; justify-content: space-between; }
        .container { padding: 30px; }
        button { width: 100px; height: 40px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-top: 20px; }
    </style>
</head>
<body>
    <div id="app">
        <nav>
            <h2>CodeIgniter 4 + VUE3</h2>
            <button @click="logout">Logout</button>
        </nav>
        <div class="container">
            <div style="display: flex; justify-content: space-between;">
                <h1>Welcome, <?= $name ?>!</h1>
                <div style="margin-top: 20px;">
                    <a href="/forms" style="background: #010b35; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Manage Forms ➪</a>
                </div>
            </div>
            <div class="card">
                <p><strong>Email:</strong> <?= $email ?></p>
                <p><strong>Status:</strong> Logged in successfully</p>
                <p><strong>System:</strong> CodeIgniter 4 + Vue 3</p>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script>
        const { createApp } = Vue;
        createApp({
            setup() {
                const logout = () => window.location.href = '/logout';
                return { logout };
            }
        }).mount('#app');
    </script>
</body>
</html>