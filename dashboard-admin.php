<?php
session_start();
include 'db.php';

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['name']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Fetch clients from the database
$clients = [];
$query = "SELECT * FROM clients";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $clients[] = $row;
    }
}

// Handle client deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_client_id'])) {
    $clientId = $_POST['delete_client_id'];
    $deleteQuery = "DELETE FROM clients WHERE id = $clientId";
    $conn->query($deleteQuery);
    header("Location: dashboard-admin.php"); // Refresh the page after deletion
    exit;
}

// Handle adding a new client
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_client'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    $insertQuery = "INSERT INTO clients (name, email, password) VALUES ('$name', '$email', '$password')";
    $conn->query($insertQuery);
    header("Location: dashboard-admin.php"); // Refresh the page after adding a client
    exit;
}

// Fetch client queries from the database
$queries = [];
$query = "SELECT id, client_name, query FROM queries ORDER BY created_at DESC";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $queries[] = $row;
    }
}

// Handle update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $updateContent = $conn->real_escape_string($_POST['update']);
    $insertUpdate = "INSERT INTO updates (content, created_at) VALUES ('$updateContent', NOW())";
    $conn->query($insertUpdate);
    header("Location: dashboard-admin.php");
    exit;
}

// Handle query deletion (responding to queries)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_query_id'])) {
    $queryId = $_POST['delete_query_id'];
    $deleteQuery = "DELETE FROM queries WHERE id = $queryId";
    $conn->query($deleteQuery);
    header("Location: dashboard-admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Windmill Advertising</title>
    <link rel="icon" type="image/x-icon" href="logo.jpeg">
    <style>
    body {
        font-family: 'Arial', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
        color: #333;
        opacity: 0;
        transition: opacity 0.5s ease;
    }

    body.loaded {
        opacity: 1;
    }

    header {
        background-color: #8B0000;
        padding: 20px;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    header .logo img {
        width: 150px;
        vertical-align: middle;
    }

    footer {
        background-color: #8B0000;
        color: white;
        text-align: center;
        padding: 15px;
        font-size: 14px;
    }

    .container {
        display: flex;
        min-height: 80vh;
        margin-top: 20px;
    }

    .sidebar {
        width: 250px;
        background-color: #333;
        color: white;
        padding: 30px 20px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar h2 {
        font-size: 24px;
        margin-bottom: 30px;
        text-transform: uppercase;
        font-weight: bold;
        letter-spacing: 1px;
    }

    .sidebar ul {
        list-style: none;
        padding: 0;
    }

    .sidebar ul li {
        margin: 20px 0;
    }

    .sidebar ul li a {
        color: #8B0000;
        font-size: 18px;
        text-decoration: none;
        transition: color 0.3s;
    }

    .sidebar ul li a:hover {
        color: black;
    }

    .dashboard-content {
        flex: 1;
        padding: 30px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        margin-left: 20px;
    }

    section {
        margin-bottom: 40px;
    }

    section h3 {
        font-size: 24px;
        color: #333;
        font-weight: bold;
        border-bottom: 2px solid #8B0000;
        padding-bottom: 5px;
        margin-bottom: 20px;
    }

    textarea {
        width: 100%;
        height: 150px;
        padding: 15px;
        border: 2px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        resize: none;
    }

    textarea:focus {
        border-color: #8B0000;
        outline: none;
    }

    ul {
        list-style: none;
        padding: 0;
    }

    ul li {
        background-color: #f9f9f9;
        margin: 15px 0;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    button {
        background-color: #8B0000;
        color: white;
        padding: 12px 20px;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    button:hover {
        background-color: #a83232;
        transform: scale(1.05);
    }

    li {
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    li:hover {
        background-color: #f0f0f0;
        transform: translateX(10px);
    }

    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: white;
        padding: 85px;
        border-radius: 8px;
        width: 400px;
        max-width: 90%;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        display: flex;
        flex-direction: column;
        gap: 15px;
        position: relative;
    }

    .modal-content h3 {
        margin-top: 0;
        font-size: 20px;
        text-align: center;
        color: #8B0000;
    }

    .modal-content form input {
        width: 100%;
        padding: 12px;
        font-size: 14px;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: border-color 0.3s;
    }

    .modal-content form input:focus {
        border-color: #8B0000;
        outline: none;
    }

    .modal-content form button {
        padding: 12px;
        font-size: 16px;
        font-weight: bold;
        background-color: #8B0000;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-top: 10px;
    }

    .modal-content form button:hover {
        background-color: #a30000;
    }

    .modal-content form .btn-cancel {
        background-color: #ccc;
        color: #333;
    }

    .modal-content form .btn-cancel:hover {
        background-color: #bbb;
    }
    </style>
</head>

<body>
    <header>
        <div class="user-info" style="float: left; color: white; font-size: 25px; font-weight: bold;">
            Welcome, <?= htmlspecialchars($_SESSION['name']); ?>
        </div>
        <form action="logout.php" method="POST" style="float: right;">
            <button type="submit"
                style="background-color: #8B0000; color: white; border: solid 2px white; padding: 10px 15px; cursor: pointer;">Logout</button>
        </form>
        <div class="logo">
            <img src="logo.jpeg" alt="Windmill Advertising Limited">
        </div>
    </header>

    <div class="container">
        <aside class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="#">Manage Clients</a></li>
                <li><a href="#">Post Updates</a></li>
                <li><a href="#">View Queries</a></li>
            </ul>
        </aside>

        <main class="dashboard-content">
            <h2>Admin Dashboard</h2>

            <section class="client-management">
                <h3>Manage Clients</h3>
                <button onclick="document.getElementById('addClientModal').style.display = 'flex';">Add Client</button>
                <ul>
                    <?php foreach ($clients as $client) : ?>
                    <li style="display: flex; justify-content: space-between; align-items: center;">
                        <span><?= htmlspecialchars($client['name']); ?>
                            (<?= htmlspecialchars($client['email']); ?>)</span>
                        <form action="" method="POST">
                            <input type="hidden" name="delete_client_id" value="<?= $client['id']; ?>">
                            <button type="submit"
                                style="background-color: #dc3545; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 5px;">
                                Delete
                            </button>
                        </form>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <section>
                <h3>Post an Update</h3>
                <form action="" method="POST">
                    <textarea name="update" placeholder="Write your update here..." required></textarea>
                    <button type="submit">Post Update</button>
                </form>
            </section>

            <section>
                <h3>Client Queries</h3>
                <ul>
                    <?php foreach ($queries as $query): ?>
                    <li style="display: flex; justify-content: space-between; align-items: center;">
                        <span><?= htmlspecialchars($query['client_name']); ?>:
                            <?= htmlspecialchars($query['query']); ?></span>
                        <form action="" method="POST">
                            <input type="hidden" name="delete_query_id" value="<?= $query['id']; ?>">
                            <button type="submit"
                                style="background-color: #dc3545; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 5px;">
                                Delete
                            </button>
                        </form>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </section>

        </main>
    </div>

    <footer>
        <p>&copy; 2024 Windmill Advertising Limited</p>
    </footer>

    <div id="addClientModal" class="modal">
        <div class="modal-content">
            <h3>Add Client</h3>
            <form action="" method="POST">
                <input type="hidden" name="add_client" value="1">
                <input type="text" name="name" placeholder="Client Name" required>
                <input type="email" name="email" placeholder="Client Email" required>
                <input type="password" name="password" placeholder="Client Password" required>
                <button type="submit">Save</button>
                <button type="button"
                    onclick="document.getElementById('addClientModal').style.display = 'none';">Cancel</button>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.body.classList.add("loaded");
    });

    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
    </script>
</body>

</html>