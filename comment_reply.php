<?php


require_once 'db_config.php';
require 'comment.class.php';

$content =null;
$title ='Reply to Comment';

if ($user->is_loggedin() && isset($_GET['commentID']) && isset($_GET['articleID'])) {
  if (!isset($_POST['post_comment'])) {
      $commentObj = $commentQueries->getSingleComment($_GET['commentID']);
      $content .=  '
      <h2>Reply to Comment</h2>
      <div id="wrapper">
      <ul>
      <li class="comment">
      <p>At: </p>
      <p>'.$commentObj->getCommDate().' </p>
      <p> <a href="userProfile.php?userID=' . $commentObj->getCommAuthor() .  '">'.$user->getUserNameByID($commentObj->getCommAuthor()).'</a> wrote: </p>
      <p>'.$commentObj->getCommContent().'</p><br></li></ul></div>';
      $content.=  '
      <form action="comment_reply.php?commentID='.$_GET['commentID']. '&articleID=' . $_GET['articleID'] . '" method="POST">
      <label>Reply</label> <textarea name="comment_body"></textarea>
      <input type="submit" name="post_comment" value="Post">
    </form>';
}else { 
  if (strlen($_POST['comment_body']) < 1) {
    $content .= 'Sorry, need to write something in the text area.';
  }else {
    $newCommentObj = new comment(null,$_SESSION['loggedin'],null,null,$_POST['comment_body'],$_GET['articleID'],$_GET['commentID']);
    if ($user->hasPermissions(2)) {
      $commentQueries->addNewComment($newCommentObj,true);
      $content .= '<p>Comment Posted!</p><a href="article.php?articleID='.$_GET['articleID'].'">Back to article</a>';
    }else { 
      $commentQueries->addNewComment($newCommentObj,false);
      $content .= '<p>Comment posted! It will be  approve it. Thank you.</p><a href="article.php?articleID='.$_GET['articleID'].'">Back to article</a>';
    }
  }

}

}elseif(!$user->is_loggedin()){
  $content = '<h1>You need to be logged in to post a comment!</h1>';
}else {
    $content = '<h1>Something went wrong, please try again.</h1>';
}


require 'layout.php';
 ?>
