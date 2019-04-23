<?php


require ("./models/article.php");

// appel de la fonction

$articles = getArticles(null, null);

if (isset($_GET['category_id'])){

    $articles = getArticles(null, $_GET['category_id']);

    $selectedCategory = getCategories($_GET['category_id']);

}



require ("./views/article_list.php");