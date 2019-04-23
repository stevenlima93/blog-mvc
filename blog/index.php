<?php

function dbConnect()
{
    setlocale(LC_ALL, "fr_FR");


    try{
        return $db = new PDO('mysql:host=localhost;dbname=blog;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }
    catch (Exception $exception)
    {
        die( 'Erreur : ' . $exception->getMessage() );
    }

}

$db = dbConnect();

session_start();
if (isset($_GET['logout']) && isset($_SESSION['user'])) {
    //la fonction unset() détruit une variable ou une partie de tableau. ici on détruit la session user
    unset($_SESSION["user"]);
}

    if (isset($_GET['page'])){

        switch ($_GET['page']){

            case "article" :
                require ("./controllers/article-c.php");
                break;
            case "article_list" :
                require ("./controllers/article_list-c.php");
                break;
            case "login-register" :
                require ("./controllers/login-register.php");
                break;
            case "user-profile" :
                require ("./controllers/user-profile.php");
                break;
            default :
                require ("./controllers/index.php");
                break;
        }
    }else{
        require ("./controllers/index.php");
    }
