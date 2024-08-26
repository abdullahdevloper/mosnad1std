<?php

require_once 'db_config.php';
require 'comment.class.php';
require 'article.class.php';

$content =null;
$title ='Delete Comment';

if ($user->is_loggedin() && isset($_GET['commentID']) && isset($_GET['articleID']) && $articleQueries->doesArticleExists($_GET['articleID'])) {
  $commentObj = $commentQueries->getSingleComment($_GET['commentID']);
  $articleObj = $articleQueries->getArticleByID($_GET['articleID']);
  if ($user->hasPermissions(1) || $commentObj->getCommAuthor() === $_SESSION['loggedin'] || $articleObj->getAuthor() === $_SESSION['loggedin']) {
    if (isset($_POST['confirm_deletion'])) {
      $commentQueries->deleteComment($_GET['commentID']);
      $content = $content . '<p>Comment Deleted!</p><a href="article.php?articleID='.$_GET['articleID'].'">Back to article</a>';
    }elseif (isset($_POST['cancel_deletion'])) {
      $user->redirect('article.php?articleID='.$_GET['articleID']);
    }else {
      $content = $content . '
      <h2>Are you certain? This action is irrevocable. </h2>
      <form action="deleteComment.php?commentID='.$commentObj->getCommID(). '&articleID=' . $articleObj->getID().'" method="POST">
      <input type="submit" name="confirm_deletion" value="Yes, delete.">
      <input type="submit" name="cancel_deletion" value="No, abort.">
    </form>';
    }
  }else {
    $content = '<h1>You are not allowed to view this page!<h1>';
  }
}elseif (!$user->is_loggedin()) {
  $content = '<h1>You are not allowed to view this page!<h1>';
}else {
  $content = '<h1>Either a technical problem occured or the requested article does not exist.</h1>';
}


require 'layout.php';
 ?>
