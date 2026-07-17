<?php
session_start();
require_once 'connect.php';

if (!isset($_GET['id'])) {
    die("Invalid request");
}

$id = $_GET['id'];
echo $id;
// delete query
$query = "DELETE FROM applications WHERE id = $1 AND user_id = $2";
$result = pg_query_params($conn, $query, array($id, $_SESSION['user_id']));

// redirect back
header("Location: application.php?msg=deleted");
exit();
