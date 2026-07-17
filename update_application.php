<?php
session_start();
require_once 'connect.php';

$id = $_POST['id'];
$company = $_POST['company_name'];
$role = $_POST['job_role'];
$date = $_POST['date_applied'];
$status = $_POST['status'];

$query = "UPDATE applications 
          SET company_name=$1, job_role=$2, date_applied=$3, status=$4 
          WHERE id=$5 AND user_id=$6";

pg_query_params($conn, $query, array($company, $role, $date, $status, $id, $_SESSION['user_id']));

$_SESSION['success'] = "Application updated successfully!";
header("Location: dashboard.php");
exit();
