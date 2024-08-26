<?php




    require 'db_config.php';

  $title = 'Register';
  $error = '';
if (!$user->is_loggedin()) {
  if(isset($_POST['submit'])){
    foreach ($_POST as $key => $value) {
      if (empty($_POST[$key])) {
        $error .='<li>' . ucfirst($key). ' is required.</li>';
      }
    }
    if (!preg_match("/^[A-Za-z][A-Za-z0-9]{1,19}$/",$_POST['username'])) {
        $error .= '<li> Name must start with a letter and contain only letters and numbers. Minimum 2 character and maximum 20</li>';
    }
    if ($_POST['password'] !== $_POST['confirmPassword']) {
      $error .= '<li> Password is not the same in both fields!</li>';
    }
    if ((strlen($_POST['password']) < 6) || (strlen($_POST['password']) > 16)) {
      $error .= '<li> Passwords can be 6 characters at the minimum and 16 at the maximum.</li>';
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
      $error .= '<li> Invalid Email Format</li>';
    }
    if ($user->hasSameName($_POST['username'])) {
      $error .= '<li> Please use a different name as this already exists!</li>';
    }
    if ($user->hasSameEmail($_POST['email'])) {
      $error .= '<li> Please use a different email address as this already exists!</li>';
    }

    if (!isset($_POST['newsletter'])) {
      $_POST['newsletter'] = 'off';
    }
    if ($error != '') {
      $content = '
        <h2 >Register</h2>
        <ul #id="errorList">'.$error.'</ul>
      <form action="register.php" method="POST">
        <label>*Username:</label> <input type="text" name="username" />
        <label>*Password:</label> <input type="password" name="password" />
        <label>*Confirm Password:</label> <input type="password" name="confirmPassword" />
        <label>*Email:</label> <input type="text" name="email" />
        <label>I would like to be informed when a new article is posted</label> <input type="checkbox" name="newsletter"/>
        <input type="submit" name="submit" value="Register">
      </form>';
    }else{
      $user->register($_POST['username'],$_POST['password'],$_POST['email'],$_POST['newsletter']);
      $content = '<p>You have succesfully registered! Now you can <a href="login.php">login</a>!</p>';
    }


  }else{
    $content = '
      <h2 >Register</h2>
      <p>All mandatory fields are indicated with *</p>
    <form action="register.php" method="POST">
      <label>*Username:</label> <input type="text" name="username" />
      <label>*Password:</label> <input type="password" name="password" />
      <label>*Confirm Password:</label> <input type="password" name="confirmPassword" />
      <label>*Email:</label> <input type="text" name="email" />
      <label>I would like to be informed when a new article is posted</label> <input type="checkbox" name="newsletter"/>
      <input type="submit" name="submit" value="Register">
    </form>
    ';}
}else {
  $content = '<h1>You are already logged in!</h1>';
}

    require 'layout.php';
?>
