<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CI4-VUE3</title>

    <style>
        /* keep your same CSS */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #000728 0%, #140127 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .register-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
            padding: 40px;
        }
        .register-container h2 { text-align: center; margin-bottom: 30px; color: #333; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; color: #555; font-weight: 500; }
        .form-group input {
            width: 100%; padding: 12px; border: 1px solid #ddd;
            border-radius: 5px; font-size: 16px;
        }
        button {
            width: 100%; padding: 12px;
            background: linear-gradient(135deg, #020b33 0%, #16002b 100%);
            color: white; border: none; border-radius: 5px;
            font-size: 16px; cursor: pointer;
        }
        .error-message {
            background: #fee; color: #c33;
            padding: 10px; border-radius: 5px; margin-bottom: 20px;
        }
        .login-link { text-align: center; margin-top: 20px; }
    </style>

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>

<body>
<div id="app">
    <div class="register-container">
        <h2>Create Account</h2>

        <div v-if="errors.length" class="error-message">
            <ul>
                <li v-for="err in errors" :key="err">{{ err }}</li>
            </ul>
        </div>

        <form @submit.prevent="handleRegister">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" v-model="form.name" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" v-model="form.email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" v-model="form.password" required>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" v-model="form.confirm_password" required>
            </div>

            <button type="submit" :disabled="loading">
                {{ loading ? 'Creating Account...' : 'Register' }}
            </button>
        </form>

        <div class="login-link">
            Already have an account?
            <a href="<?= site_url('/login') ?>">Login here</a>
        </div>
    </div>
</div>

<!-- ✅ External JS -->
<script src="/js/auth/register.js"></script>
</body>
</html>