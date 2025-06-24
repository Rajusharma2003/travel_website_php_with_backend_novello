<?php
session_start();

// Check if the user is logged in, if not redirect to login page
function check_login(){
    if(!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true){
        header("location: login.php");
        exit;
    }
}
?>