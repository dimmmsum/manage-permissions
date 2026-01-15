<!DOCTYPE html>
<html>
<head>
    <title>Manage User Permissions</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Manage User Permissions</h1>
    <form method="post" action="manage_permissions.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="host">Host:</label>
        <input type="text" id="host" name="host" value="localhost" required><br><br>

        <label for="permission">Permission:</label>
        <select id="permission" name="permission" required>
            <option value="SELECT">SELECT</option>
            <option value="INSERT">INSERT</option>
            <option value="UPDATE">UPDATE</option>
            <option value="DELETE">DELETE</option>
            <option value="ALL PRIVILEGES">ALL PRIVILEGES</option>
        </select><br><br>

        <label for="action">Action:</label>
        <select id="action" name="action" required>
            <option value="grant">Grant</option>
            <option value="revoke">Revoke</option>
        </select><br><br>

        <label for="database">Database:</label>
        <input type="text" id="database" name="database" required><br><br>

        <label for="table">Table:</label>
        <input type="text" id="table" name="table"><br><br>

        <input type="submit" name="submit" value="Submit">
        <input type="submit" name="show_grants" value="Show Grants">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $host = $_POST['host'];
        $database = $_POST['database'];
        $table = $_POST['table'];

        $servername = "localhost";
        $db_username = "root";
        $db_password = "";
        $dbname = "db_order";
        $conn = new mysqli($servername, $db_username, $db_password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        if (isset($_POST['show_grants'])) {
            $sql = "SHOW GRANTS FOR '$username'@'$host'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<h2>Granted Permissions for $username@$host:</h2>";
                echo "<ul>";
                while ($row = $result->fetch_assoc()) {
                    foreach ($row as $grant) {
                        echo "<li>$grant</li>";
                    }
                }
                echo "</ul>";
            } else {
                echo "No grants found for $username@$host.";
            }
        }

        if (isset($_POST['submit'])) {
            $permission = $_POST['permission'];
            $action = $_POST['action'];

            if ($action == "grant") {
                $sql = "GRANT $permission ON `$database`.`$table` TO '$username'@'$host'";
            } elseif ($action == "revoke") {
                $sql = "REVOKE $permission ON `$database`.`$table` FROM '$username'@'$host'";
            }

            if ($conn->query($sql) === TRUE) {
                echo "Permission successfully " . ($action == "grant" ? "granted" : "revoked") . ".";
            } else {
                echo "Error: " . $conn->error;
            }
        }

        $conn->close();
    }
    ?>
</body>
</html>