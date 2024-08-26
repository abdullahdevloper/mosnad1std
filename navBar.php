
<nav>
  <ul>
      <ul>

<?php

require_once 'db_config.php';



foreach ($results as $key => $row) {
echo '<li><a href="category.php?categoryID='. $row['category_ID'] .'">' . $row['category_name'] . '</a></li>';
}
  ?>
      </ul>
    </li>
    <li><a href=""></a></li>
  </ul>

  <?php if(isset($_SESSION['loggedin'])){
    echo'<a href="logout.php">Logout</a>';
  }else {
    echo'<a href="login.php">Login</a>';
  } ?>
</nav>
