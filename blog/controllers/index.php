<?php

require ("./models/article.php");

$homeArticles = getArticles(3, null);

require ("./views/index.php");