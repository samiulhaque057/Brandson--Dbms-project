<?php
// Connect to Database
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "brandson"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $measure = $_POST['measure'] ?? '';
    $improvement = $_POST['improvement'] ?? '';

    if (!empty($measure) && !empty($improvement)) {
        $stmt = $conn->prepare("INSERT INTO preventative_measures (measure, improvement) VALUES (?, ?)");
        $stmt->bind_param("ss", $measure, $improvement);
        $stmt->execute();
        echo "<script>alert('Preventative Measure added successfully!'); window.location.href='preventative_measures.php';</script>";
    } else {
        echo "<script>alert('Please fill out all fields.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Preventative Measures and Improvements</title>

    <style>
        /* Full CSS in the same file */

        body {
            font-family: Arial, sans-serif;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
        }

        /* Header Styles */
        .header {
            background-color: #000000; /* Black background */
            padding: 20px 0;
            color: #ffffff; /* White text */
        }

        .container-header {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
        }

        .nav-links a {
            color: #ffffff; /* White text for links */
            text-decoration: none;
            font-weight: bold;
        }

        .nav-links a:hover {
            text-decoration: underline;
        }

        /* Main Content */
        .main-content {
            padding: 20px 0;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        .form {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 20px;
        }

        .form label {
            font-weight: bold;
            color: #555;
        }

        .form textarea {
            resize: vertical;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .form button {
            width: 150px;
            padding: 10px;
            background: #4CAF50;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            align-self: center;
        }

        .form button:hover {
            background: #45a049;
        }

        .table-wrapper {
            margin-top: 40px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #4CAF50;
            color: white;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        /* Footer Styles */
        .footer {
            background-color: #000000; /* Black background */
            padding: 15px 0;
            color: #ffffff; /* White text */
            text-align: center;
            margin-top: 40px;
        }
    </style>

</head>
<body>

<!-- HEADER -->
<header class="header">
    <div class="container-header">
        <h1>Perishable Meat-Based Product Management</h1>
        <nav>
            <ul class="nav-links">
                <li><a href="preventative_measures.php">Home</a></li>
                <li><a href="#">Products</a></li>
                <li><a href="#">Reports</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </nav>
    </div>
</header>

<!-- MAIN -->
<main class="main-content">
    <div class="container">
        <h2>Implementation of Preventative Measures</h2>

        <form action="preventative_measures.php" method="post" class="form">
            <label for="measure">Preventative Measure:</label>
            <textarea id="measure" name="measure" required></textarea>

            <label for="improvement">Tracking Improvement:</label>
            <textarea id="improvement" name="improvement" required></textarea>

            <button type="submit">Submit</button>
        </form>

        <h2>Previous Entries</h2>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Measure</th>
                        <th>Improvement</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM preventative_measures ORDER BY id DESC");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['measure']}</td>
                                <td>{$row['improvement']}</td>
                                <td>{$row['created_at']}</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>
</main>

<!-- FOOTER -->
<footer class="footer">
    <div class="container-footer">
        <p>&copy; <?php echo date('Y'); ?> Perishable Meat Management. All rights reserved.</p>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        console.log("Preventative Measures Page Loaded Successfully!");
    });
</script>

</body>
</html>
