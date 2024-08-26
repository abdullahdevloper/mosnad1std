<?php



  class Article
  {
    private $id;
    private $title;
    private $authorID;
    private $isVisible;
    private $date;
    private $content;
    private $category;
    private $picture;
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function __construct($id, $title, $authorID,$isVisible, $date, $content, $category, $picture)
    {
      $this->id = $id;
      $this->title = $title;
      $this->authorID = $authorID;
      $this->isVisible = $isVisible;
      $this->date = $date;
      $this->content = $content;
      $this->category = $category;
      $this->picture = $picture;
    }

    function truncateArticle()
    {
      if (strlen($this->content) > 100) {

          $stringCut = substr($this->content, 0, 100);

          $truncatedString = substr($stringCut, 0, strrpos($stringCut, ' ')).'... <a href="article.php?articleID='. $this->id .'">Read More</a>';
        }else{$truncatedString = $this->content;}
        return $truncatedString;
    }




    function getID()
    {
      return $this->id;
    }
    function getTitle()
    {
      return $this->title;
    }
    function getAuthor()
    {
      return $this->authorID;
    }
    function isVisible(){
      return $this->isVisible;
    }
    function getDate()
    {
      return $this->date;
    }
    function getContent()
    {
      return $this->content;
    }
    function getCategory()
    {
      return $this->category;
    }
    function getPicture()
    {
      return base64_encode($this->picture);
    }

    function getCodedPicture(){
      return $this->picture;
    }



function setID($id)
{
  $this->id = $id;
}

function setTitle($title)
{
  $this->title = $title;
}

function setAuthor($authorID)
{
  $this->authorID = $authorID;
}

function setVisible($isVisible)
{
  $this->isVisible = $isVisible;
}

function setDate($date)
{
  $this->date = $date;
}

function setContent($content)
{
  $this->content = $content;
}

function setCategory($category)
{
  $this->category = $category;
}

function setPicture($picture)
{
  $this->picture = $picture;
}

}
 ?>
