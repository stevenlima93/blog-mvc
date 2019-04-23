<?php
if(!isset($_SESSION['user']) OR $_SESSION['user']['is_admin'] == 1){
header('location:index.php');
exit;
}
require ("./models/user-profile.php");

$message = modifProfile(isset($_POST['update']));

$user = recupUser();

require ("./views/user-profile.php");