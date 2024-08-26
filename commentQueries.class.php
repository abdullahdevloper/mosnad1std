<?php


  class commentQueries {
 private $db;

      function __construct($DB_con)
      {
        $this->db = $DB_con;
      }
      public function getAllPendingComments(){
        $stmt = $this->db->prepare('SELECT * FROM comments
          WHERE comment_pending = "y"
          ORDER BY comment_created_at');

          $stmt->execute();
          $comments = $stmt->fetchAll();
          return $comments;
      }
     
      public function displayCommentOptions($user,$comment){
        $content = null;
        $articleQueries = new articleQueries($this->db);
        if ($user->is_loggedin()) {
          $content = '<a href="comment_reply.php?commentID='.$comment->getCommID(). '&articleID=' . $comment->getCommArticle().'">Reply</a><br>';
          if ($user->hasPermissions(1) || $comment->getCommAuthor() === $_SESSION['loggedin'] || $_SESSION['loggedin'] === $articleQueries->getArticleByID($comment->getCommArticle())->getAuthor()) {
            $content .= '<a href="deleteComment.php?commentID='.$comment->getCommID(). '&articleID=' . $comment->getCommArticle().'">Delete Comment</a><br>';
            if ($user->hasPermissions(1) || $_SESSION['loggedin'] === $articleQueries->getArticleByID($comment->getCommArticle())->getAuthor()) {
              if ($comment->isPending() === 'y') {
                $content .= '<a href="approveComments.php?commentID='.$comment->getCommID(). '&articleID=' . $comment->getCommArticle().'">Approve Comment</a>';
              }
            }
          }
        }
        return $content;
      }
         public function queryForCommentsThat($commentPending, $commentArticleID, $commentParentCommentID){

      $stmt = $this->db->prepare('SELECT c.*, u.user_name FROM comments c
        INNER JOIN users u ON c.comment_author = u.user_ID
        WHERE comment_pending = :pending
        AND comment_article = :articleID
        AND comment_parent = :parent
        ORDER BY comment_created_at DESC');

        $criteria = [
          'pending' => $commentPending,
          'articleID' => $commentArticleID,
          'parent' => $commentParentCommentID
        ];

        $stmt->execute($criteria);
        return $stmt;
    }

     public function displayComments($articleID, $user){

      $commentList = null;
      $stmt = $this->queryForCommentsThat("n", $articleID, 0);

      if ($stmt->rowCount() > 0) {
        $results = $stmt->fetchAll();
        foreach ($results as $commentStats) {
          $commentObj = new comment($commentStats['comment_ID'],$commentStats['comment_author'],$commentStats['comment_created_at'],$commentStats['comment_pending'],$commentStats['comment_content'],$commentStats['comment_article'],$commentStats['comment_parent']);

          $commentList = $commentList . $this->getCompleteComment($commentObj, $user);
        }
    }else {
        $commentList = '<p>No comments to display yet.</p>';
      }
        return $commentList;
    }

      public function displayCommentsByAuthor($authorID, $user){

      $commentList = null;
      $stmt = $this->getCommentsByAuthor($authorID);

      if ($stmt->rowCount() > 0) {
        $results = $stmt->fetchAll();
        foreach ($results as $commentStats) {
          
          $commentObj = new comment($commentStats['comment_ID'],$commentStats['comment_author'],$commentStats['comment_created_at'],$commentStats['comment_pending'],$commentStats['comment_content'],$commentStats['comment_article'],$commentStats['comment_parent']);

          $commentList = $commentList . $this->getCompleteComment($commentObj, $user);
        }
    }else {
        $commentList = '<p>No comments to display yet.</p>';
      }
        return $commentList;
    }

    public function getCompleteComment($commentObj, $user){
      $completeComment = '
      <li class="comment">
        <p>At: </p>
        <p>'.$commentObj->getCommDate().' </p>';


        if ($user->isDeleted($commentObj->getCommAuthor())) {
          $completeComment .= '<p>Author: <del>'.$user->getUserNameByID($commentObj->getCommAuthor()).'</del></p>';
        }else {
          $completeComment .= '<p> <a href="userProfile.php?userID=' . $commentObj->getCommAuthor() .  '">'.$user->getUserNameByID($commentObj->getCommAuthor()).'</a> wrote: </p>';
        }

        $completeComment .= '<p>'.$commentObj->getCommContent().'</p><br>
        '.$this->displayCommentOptions($user,$commentObj).'
        ';

        $queryResults = $this->queryForCommentsThat('n',$commentObj->getCommArticle(),$commentObj->getCommID());

      if ($queryResults->rowCount() > 0) {
        $comments = $queryResults->fetchAll();
        $completeComment .= '<ul>';

        foreach ($comments as $comment) {
          $commentObj = new comment($comment['comment_ID'],$comment['comment_author'],$comment['comment_created_at'],$comment['comment_pending'],$comment['comment_content'],$comment['comment_article'],$comment['comment_parent']);
          $completeComment .= $this->getCompleteComment($commentObj, $user);
        }
        $completeComment .= '</ul>';
      }
      $completeComment .= '</li>';
      return $completeComment;
}

  public function getSingleComment($commentID){
    $stmt = $this->db->prepare('SELECT c.*, u.user_name FROM comments c
      INNER JOIN users u ON c.comment_author = u.user_ID
      WHERE comment_ID = :id
      ORDER BY comment_created_at DESC');

      $criteria = [
        'id' => $commentID
      ];

      $stmt->execute($criteria);
      $commentInfo = $stmt->fetchAll();

      foreach ($commentInfo as $comment) {
          $commentObj = new comment($comment['comment_ID'],$comment['comment_author'],$comment['comment_created_at'],$comment['comment_pending'],$comment['comment_content'],$comment['comment_article'],$comment['comment_parent']);
      }
      return $commentObj;
  }
 
  public function getCommentsByAuthor($userID){
    $stmt = $this->db->prepare('SELECT c.*, u.user_name FROM comments c
      INNER JOIN users u ON c.comment_author = u.user_ID
      WHERE comment_author = :id
      ORDER BY comment_created_at DESC');

      $criteria = [
        'id' => $userID
      ];

      $stmt->execute($criteria);
      return $stmt;
  }

    
  public function addNewComment($commentObj, $approve){
    $stmtNotApprove = $this->db->prepare('INSERT INTO comments(comment_author,comment_article,comment_parent,comment_content) VALUES(:author,:article,:parent,:content)');
    $stmtApprove = $this->db->prepare('INSERT INTO comments(comment_author,comment_article,comment_parent,comment_content,comment_pending) VALUES(:author,:article,:parent,:content,"n")');

    if (is_null($commentObj->getCommParent())) {
      $commentObj->setCommParent(0);
    }

    $criteria = [
      'author' => $commentObj->getCommAuthor(),
      'article' => $commentObj->getCommArticle(),
      'parent' => $commentObj->getCommParent(),
      'content' => $commentObj->getCommContent()
    ];

    if ($approve) {
      $stmtApprove->execute($criteria);
    }else {
      $stmtNotApprove->execute($criteria);
    }
  }
 
    public function deleteComment($commentID){
      $stmt = $this->db->prepare('DELETE FROM comments WHERE comment_ID = :id LIMIT 1');

      $criteria = [
        'id' => $commentID
      ];

      $stmt->execute($criteria);
    }
   //
    public function approveComment($commentID){
      $stmt = $this->db->prepare('UPDATE comments SET comment_pending = "n" WHERE comment_ID= :id');

      $criteria = [
        'id' => $commentID
      ];

      $stmt->execute($criteria);
    }
    }
