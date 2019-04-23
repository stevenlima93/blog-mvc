<?php

require ("./models/login-register.php");

// appel de la fonction


$loginError = login(isset($_POST['login']));

$registerError = register(isset($_POST['register']));

require ("./views/login-register.php");