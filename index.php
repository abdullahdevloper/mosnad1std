<?php


require_once 'db_config.php';
require 'article.class.php';


$title = 'Latest content';
$content = '<h2>Latest content</h2><hr>';

$articles = $articleQueries->getAllLatest();
    foreach ($articles as $article) {
      $content .= $articleQueries->displayArticle($articleQueries->getArticleByID($article['article_ID']), true);
    }


require 'layout.php';
 ?>
