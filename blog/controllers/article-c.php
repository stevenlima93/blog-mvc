<?php


require ("./models/article.php");

// appel de la fonction

$article = getOneArticle($_GET['article_id']);

require ("./views/article.php");