<?php 
session_start();
include 'db.php'; 

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($role === 'client') {
        $query = "SELECT * FROM clients WHERE email = '$email' AND password = '$password'";
    } elseif ($role === 'admin') {
        $query = "SELECT * FROM admins WHERE email = '$email' AND password = '$password'";
    }

    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $role;

        if ($role === 'client') {
            header("Location: dashboard-client.php");
        } else {
            header("Location: dashboard-admin.php");
        }
        exit;
    } else {
        $loginError = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Windmill Advertising</title>
    <link rel="icon" type="image/x-icon" href="logo.jpeg">
    <style>
    body,
    html {
        margin: 0;
        padding: 0;
        height: 100%;
        font-family: Arial, sans-serif;
    }

    body {
        display: flex;
        flex-direction: column;
        opacity: 0;
        transition: opacity 0.5s ease;
    }

    body.loaded {
        opacity: 1;
    }

    main {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background-color: #f4f4f4;
    }

    header {
        background-color: #8B0000;
        padding: 20px;
        text-align: center;
        color: white;
    }

    header .logo img {
        width: 150px;
    }

    .login-options {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }

    .login-options button {
        background-color: #8B0000;
        color: white;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .login-options button:hover {
        background-color: #a30000;
    }

    .login-form {
        display: none;
        flex-direction: column;
        align-items: center;
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
    }

    .login-form h2 {
        margin-bottom: 20px;
        color: #8B0000;
    }

    .login-form label {
        align-self: flex-start;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .login-form input {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .login-form button {
        background-color: #8B0000;
        color: white;
        border: none;
        padding: 10px 20px;
        cursor: pointer;
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
    }

    .error {
        color: red;
        margin-bottom: 10px;
    }

    button {
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    button:hover {
        background-color: #a83232;
        transform: scale(1.05);
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
            <img src="logo.jpeg" alt="Windmill Advertising Limited">
        </div>
    </header>

    <main>
        <div class="login-options">
            <button onclick="showLoginForm('admin')">Login as Admin</button>
            <button onclick="showLoginForm('client')">Login as Client</button>
        </div>

        <?php if (!empty($loginError)) : ?>
        <div class="error"><?= $loginError ?></div>
        <?php endif; ?>

        <!-- Admin Login Form -->
        <form id="admin-login" class="login-form" method="POST">
            <h2>Admin Login</h2>
            <input type="hidden" name="role" value="admin">
            <label for="admin-email">Email</label>
            <input type="email" id="admin-email" name="email" placeholder="Enter your email" required>

            <label for="admin-password">Password</label>
            <input type="password" id="admin-password" name="password" placeholder="Enter your password" required>

            <button type="submit">Login</button>
        </form>

        <!-- Client Login Form -->
        <form id="client-login" class="login-form" method="POST">
            <h2>Client Login</h2>
            <input type="hidden" name="role" value="client">
            <label for="client-email">Email</label>
            <input type="email" id="client-email" name="email" placeholder="Enter your email" required>

            <label for="client-password">Password</label>
            <input type="password" id="client-password" name="password" placeholder="Enter your password" required>

            <button type="submit">Login</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 Windmill Advertising Limited</p>
    </footer>
</body>
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.body.classList.add("loaded");
});
</script>

</html>