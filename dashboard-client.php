<?php
session_start();
include 'db.php';

// Check if the user is logged in and has the 'client' role
if (!isset($_SESSION['name']) || $_SESSION['role'] !== 'client') {
    header("Location: index.php");
    exit;
}

// Fetch the latest 3 updates from the database
$updates = [];
$updateQuery = "SELECT content FROM updates ORDER BY created_at DESC LIMIT 3";
$result = $conn->query($updateQuery);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $updates[] = $row['content'];
    }
}

// Handle query submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['query'])) {
    $clientName = $_SESSION['name'];
    $queryContent = $conn->real_escape_string($_POST['query']);
    $insertQuery = "INSERT INTO queries (client_name, query, created_at) VALUES ('$clientName', '$queryContent', NOW())";
    $conn->query($insertQuery);
    header("Location: dashboard-client.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard - Windmill Advertising</title>
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
        /* Dark Red */
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
        box-sizing: border-box;
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
        box-sizing: border-box;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        margin-left: 20px;
    }

    .dashboard-content h2 {
        color: #8B0000;
        /* Dark Red */
        font-size: 32px;
        font-weight: bold;
        margin-bottom: 30px;
    }

    section {
        margin-bottom: 40px;
    }

    section h3 {
        font-size: 24px;
        color: #333;
        font-weight: bold;
        border-bottom: 2px solid #8B0000;
        /* Dark Red underline */
        padding-bottom: 5px;
        margin-bottom: 20px;
    }

    button {
        background-color: #8B0000;
        /* Dark Red */
        color: white;
        padding: 12px 20px;
        border: none;
        cursor: pointer;
        font-size: 16px;
        border-radius: 5px;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    button:hover {
        background-color: #a83232;
        transform: scale(1.05);
    }

    textarea {
        width: 100%;
        height: 150px;
        padding: 15px;
        border: 2px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        resize: none;
        box-sizing: border-box;
        transition: border-color 0.3s ease;
    }

    textarea:focus {
        border-color: #8B0000;
        /* Dark Red */
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
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    ul li button {
        background-color: #28a745;
        /* Green */
        padding: 8px 12px;
        font-size: 14px;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    ul li button:hover {
        background-color: #218838;
        /* Darker green */
    }

    li {
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    li:hover {
        background-color: #f0f0f0;
        transform: translateX(10px);
    }


    @media (max-width: 768px) {
        .container {
            flex-direction: column;
            margin-top: 10px;
        }

        .sidebar {
            width: 100%;
            padding: 20px;
        }

        .dashboard-content {
            padding: 20px;
        }

        button {
            width: 100%;
            padding: 15px;
        }

        textarea {
            width: 100%;
            padding: 10px;
        }
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
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <h2>Client Panel</h2>
            <ul>
                <li><a href="#">View Updates</a></li>
                <li><a href="#">Post Query</a></li>
            </ul>
        </aside>

        <!-- Main Dashboard Content -->
        <main class="dashboard-content">
            <h2>Client Dashboard</h2>

            <section class="updates">
                <h3>Latest Updates</h3>
                <ul>
                    <?php foreach ($updates as $update): ?>
                    <li><?= htmlspecialchars($update); ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <section class="post-query">
                <h3>Post a Query</h3>
                <form action="" method="POST">
                    <textarea name="query" placeholder="Leave your comment or question" required></textarea>
                    <button type="submit">Submit Query</button>
                </form>
            </section>
        </main>
    </div>

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