<?php


require_once 'db_config.php';

$title = 'Log in';
if($user->is_loggedin())
{
  $content = '
      <h2 >Login</h2>
      <p>You are already logged in! Wanna log out?</p>
      <a href="logout.php">Logout</a>';
  }else{
    $content =
      '
        <h2 >Login</h2>
      <form action="login.php" method="POST">
        <label>Username:</label> <input type="text" name="username" />
        <label>Password:</label> <input type="password" name="password" />
        <input type="submit" name="submit" value="Login">
      </form>
        <p class="loginP">Not having an account? <a href="register.php">Register</a></p>
      ';
  }
if(isset($_POST['submit']))
{

  if($user->login($_POST['username'],$_POST['password']))
  {
    $user->redirect('index.php');
  }
  else
  {
    $content =
      '
        <h2 >Login</h2>
        <p>Wrong Credentials! Try again</p>
      <form action="login.php" method="POST">
        <label>Username:</label> <input type="text" name="username" />
        <label>Password:</label> <input type="password" name="password" />
        <input type="submit" name="submit" value="Login">
      </form>
        <p class="loginP">Not having an account? <a href="register.php">Register</a></p>
      ';
  }
}

  require 'layout.php';
?>
