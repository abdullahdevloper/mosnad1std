<?php


  class categoryQueries {
    //this for  specific content (aticals , products)
    private $db;
    /////////////////////////////////////////////////////////////////////////////////////////////////////////

    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
       function getName($id){
      $categoryName = $this->db->prepare('SELECT category_name FROM categories WHERE category_ID = :cate_id');

      $criteria = [
         'cate_id' => $id
       ];

       $categoryName ->execute($criteria);
       $buff = $categoryName->fetch();
       return $buff['category_name'];
    }
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
      function dropdownAllCategories(){
      $content = null;
      $categories = $this->getAllCategories();

      $content = '
      <select name="categories">';
      foreach ($categories as $row) {
      $content = $content . '<option value="'.$row['category_ID'].'">' . $row['category_name'] . '</option>'; 
      }
      $content = $content . '</select>';

      return $content;
    }
////////
function dropdownAllCategoriesExcept($catID){
  $content = null;
  $categories = $this->getAllCategories();

  $content = '
  <select name="categories">';
  foreach ($categories as $row) {
    if ($row['category_ID'] === $catID) {
      unset($row);  
    }else {
      $content = $content . '<option value="'.$row['category_ID'].'">' . $row['category_name'] . '</option>'; 
    }
  }
  $content = $content . '</select>';

  return $content;
}

    function setName($id, $name){
      $criteria = [
         'cate_id' => $id,
         'name' => $name
       ];

      $updateCatName = $this->db->prepare('UPDATE categories SET category_name = :name WHERE category_ID= :cate_id');
      $updateCatName->execute($criteria);
    }

    function getItsArticles($id){
      $criteria = [
         'cate_id' => $_GET['categoryID']
       ];
      $queryForArticles = $this->db->prepare('SELECT a.*, c.* FROM articles a
        INNER JOIN categories c ON c.category_ID = a.article_category
        WHERE a.article_category = :cate_id AND a.article_is_visible = "y"' );

       $queryForArticles->execute($criteria);
       return $queryForArticles->fetchAll();
    }

    
    function getAllCategories(){
      $results = $this->db->prepare('SELECT * FROM categories');
      $results->execute();
      return $results;
    }
   
    function insertNewCategory($catName){
      $stmt = $this->db->prepare('INSERT INTO categories (category_name) VALUES (:name)');

      $criteria = [
        'name' => $catName
      ];
      $stmt->execute($criteria);
    }

    function hasSameName($newCatName){

      $categories = $this->getAllCategories();

      foreach ($categories as $category) {
        if ($category['category_name'] === $newCatName) {
          return true;
        }
      }
      return false;
    }
 
      function deleteCategory($categoryID){
        $stmt = $this->db->prepare('DELETE FROM categories WHERE category_ID = :id LIMIT 1');

        $criteria = [
          'id' => $categoryID
        ];
        $stmt->execute($criteria);
      }

      function editCategoryName($categoryID, $catNewName){
        $stmt = $this->db->prepare('UPDATE categories SET category_name= :name WHERE category_ID= :id');

        $criteria = [
          'id' => $categoryID,
          'name' => $catNewName
        ];
        $stmt->execute($criteria);
      }
  }
 ?>
