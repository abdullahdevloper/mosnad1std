
<nav>
  <ul>
<?php
require_once 'db_config.php';
   function populateSideBar($perms)
  {
    if (!is_null($perms)) {
      foreach ($perms as $perm) {
        echo '<li><a href="'. $perm['link'] .'">'.$perm['name'].'</a></li>';
      }
    }
  }
 ?>


  </ul>
</nav>
