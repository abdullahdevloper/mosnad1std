<?php


  class articleQueries {
      private $db;

      function __construct($DB_con)
      {
        $this->db = $DB_con;
      }
      public function getAllArticles(){
          $queryForArticles = $this->db->prepare('SELECT * FROM articles');
          $queryForArticles->execute();
          $articles = $queryForArticles->fetchAll();
          return $articles;
      }
      public function getArticleByID($id){
        $queryForArticle = $this->db->prepare('SELECT * FROM articles WHERE article_ID = :id');

        $criteria = [
          'id' => $id
        ];
        $queryForArticle->execute($criteria);
        $article = $queryForArticle->fetch();
        $articleObj = new Article($article['article_ID'],$article['article_title'],$article['article_author'],$article['article_is_visible'],$article['article_date'],$article['article_content'],$article['article_category'],$article['articlePicture']);
        return $articleObj;
      }
      public function getAllLatest(){
        $stmt = $this->db->prepare('SELECT * FROM articles
          WHERE article_is_visible = "y"
          ORDER BY article_date DESC');

        $stmt->execute();
        $articles = $stmt->fetchAll();
        return $articles;
      }
      public function getArticlesByAuthor($authorID){
        $stmt = $this->db->prepare('SELECT * FROM articles
          WHERE article_author = :id
          ORDER BY article_date DESC');

          $criteria = [
            'id' => $authorID
          ];

        $stmt->execute($criteria);
        $articles = $stmt->fetchAll();
        return $articles;
      }
         public function displayArticle($articleObject,$truncateContent){

        $articleContent = $articleObject->getContent();
        $userFunctions = new User($this->db);
        $categoryFunctions = new categoryQueries($this->db);
        $contentToReturn;

        if ($truncateContent === true) {
          $articleContent = $articleObject->truncateArticle();
        }

        $category_name = $categoryFunctions->getName($articleObject->getCategory());
        $author_name = $userFunctions->getUserNameByID($articleObject->getAuthor());


          $contentToReturn = '
          <div id="latestCustom">
          <img class="articleImg" src="data:image/jpeg;base64,'. $articleObject->getPicture().'"/>
          <a href="article.php?articleID='.$articleObject->getID().'"><h2>'. $articleObject->getTitle() .  '</h2></a>
          <p>Date: ' . $articleObject->getDate() .  '</p>';

          if ($userFunctions->isDeleted($articleObject->getAuthor())) {
            $contentToReturn .= '<p>Author: <del>'.$author_name.'</del></p>';
          }else {
            $contentToReturn .= '<p>Author: <a href="userProfile.php?userID=' . $articleObject->getAuthor() .  '">'.$author_name.'</a></p>';
          }

          $contentToReturn .= '
          <p>Category: <a href="category.php?categoryID=' . $articleObject->getCategory() .  '">'.$category_name.'</a></p>
          <p>' . $articleContent .  '</p>
          <hr>
          <br>
          </div>
          ';
          return $contentToReturn;
      }
      public function displayArticleOptions($article){
        $content = $this->displaySocialButtons($article->getID());
        $user = new User($this->db);
        $categoryQueries = new categoryQueries($this->db);
        if ($user->is_loggedin()) {
          $content .= '<a href="commentAnArticle.php?articleID='.$article->getID().'">Add new Comment</a><br>';
          if ($user->hasPermissions(1) || $article->getAuthor() === $_SESSION['loggedin']) {
            $content .= '<a href="deleteArticle.php?articleID='.$article->getID().'">Delete Article</a><br>';
            $content .= '
            <form action="article.php?articleID='.$article->getID().'" method="POST">
            <p>Name can have up to 40 characters maximum.</p>
              <label>New Article Name:</label> <input type="text" name="new_article_name" />
              <label>Change Category:</label>';
              $content .= $categoryQueries->dropdownAllCategories();
              $content .= '
              <label>Edit Article Content</label> <textarea name="amend_article_content">'.$article->getContent().'</textarea>
              <input type="submit" name="edit_article" value="Save Changes">
            </form>';
          }
        }else{
          $content .= '<p>You need to be logged in to post a comment.</p>';
        }

        return $content;
      }

        function deleteArticle($articleID){
          $stmt = $this->db->prepare('DELETE FROM articles WHERE article_ID = :id LIMIT 1');

          $criteria = [
            'id' => $articleID
          ];
          $stmt->execute($criteria);
        }

        function editArticle($articleObject){
            $stmt = $this->db->prepare('UPDATE articles SET article_title= :title, article_category =:category, article_content= :content WHERE article_ID= :id');

          $criteria = [
            'id' => $articleObject->getID(),
            'title' => $articleObject->getTitle(),
            'category' => $articleObject->getCategory(),
            'content' => $articleObject->getContent()
          ];
          $stmt->execute($criteria);
        }

        
        function insertNewArticle($articleObject){
          $stmt = $this->db->prepare('INSERT INTO articles (article_title, article_content, article_author, article_is_visible, article_category, articlePicture)
          VALUES(:title, :content, :author, :isVisible, :category, :picture)');

        $criteria = [
          'title' => $articleObject->getTitle(),
          'category' => $articleObject->getCategory(),
          'content' => $articleObject->getContent(),
          'author' => $articleObject->getAuthor(),
          'isVisible' => $articleObject->isVisible(),
          'picture' => $articleObject->getCodedPicture()

        ];
        $stmt->execute($criteria);
        }
               function hasSameName($articleName){
          $articles = $this->getAllArticles();

          foreach ($articles as $article) {
            if ($article['article_title'] === $articleName) {
              return true;
            }
          }
          return false;
        }

    
        function doesArticleExists($articleID){
          $stmt = $this->db->prepare('SELECT * FROM articles WHERE article_ID = :id');

          $criteria = [
            'id' => $articleID
          ];

          $stmt->execute($criteria);
          $result=$stmt->fetch();
          if (!empty($result)) {
            return true;
          }else {
            return false;
          }
        }
    
        function displaySocialButtons($articleID){
          $content = '<div id="share-buttons">';
   

          $content .= '</div>';

          return $content;
        }
      
        function searchArticles($query){
          $stmt = $this->db->prepare('SELECT * FROM articles WHERE article_title like "%'.$query.'%"');

          $stmt->execute();
          return $stmt->fetchAll();
        }
}
 ?>
