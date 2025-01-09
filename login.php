<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Windmill Advertising</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        header {
            background-color: #8B0000; /* Dark Red */
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        header .logo img {
            width: 150px;
            vertical-align: middle;
        }

        main {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px;
        }

        .login-options {
            margin-bottom: 20px;
        }

        .login-options button {
            background-color: #8B0000;
            color: white;
            padding: 12px 20px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            margin: 5px;
            transition: background-color 0.3s ease;
        }

        .login-options button:hover {
            background-color: #a30000;
        }

        .login-form {
            display: none;
            flex-direction: column;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-form h2 {
            color: #8B0000;
            margin-bottom: 20px;
        }

        .login-form label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        .login-form input {
            padding: 10px;
            width: 100%;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .login-form button {
            background-color: #8B0000;
            color: white;
            padding: 12px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .login-form button:hover {
            background-color: #a30000;
        }

        footer {
            background-color: #8B0000;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 14px;
            margin-top: 20px;
        }
    </style>
    <script>
        function showLoginForm(type) {
            document.querySelectorAll('.login-form').forEach(form => form.style.display = 'none');
            document.getElementById(`${type}-login`).style.display = 'flex';
            document.querySelector('.login-options').style.display = 'none';
        }
    </script>
</head>
<body>
    <header>
        <div class="logo">
            <img src="logo.png" alt="Windmill Advertising Limited">
        </div>
    </header>
    <main>
        <div class="login-options">
            <button onclick="showLoginForm('admin')">Login as Admin</button>
            <button onclick="showLoginForm('client')">Login as Client</button>
        </div>

        <form id="admin-login" class="login-form" action="php/admin_login.php" method="POST">
            <h2>Admin Login</h2>
            <label for="admin-username">Username</label>
            <input type="text" id="admin-username" name="username" placeholder="Enter your username" required>
            <label for="admin-password">Password</label>
            <input type="password" id="admin-password" name="password" placeholder="Enter your password" required>
            <button type="submit">Login</button>
        </form>

        <form id="client-login" class="login-form" action="php/client_login.php" method="POST">
            <h2>Client Login</h2>
            <label for="client-username">Username</label>
            <input type="text" id="client-username" name="username" placeholder="Enter your username" required>
            <label for="client-password">Password</label>
            <input type="password" id="client-password" name="password" placeholder="Enter your password" required>
            <button type="submit">Login</button>
        </form>
    </main>
    <footer>
        <p>&copy; 2024 Windmill Advertising Limited</p>
    </footer>
</body>
</html>
