<?php

class User
{
    private $db;

    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }
      public function register($username,$password,$email,$newsletter)
    {
       try
       {
           $stmt_insert = $this->db->prepare('INSERT INTO users (user_name, user_password, user_email, on_newsletter) VALUES(:username, :password, :email, :newssigned)');

           $criteriaInsert = [
             'username' => $username,
             'password' => password_hash($password, PASSWORD_DEFAULT),
             'email' => $email,
             'newssigned' => $newsletter
           ];
           $stmt_insert->execute($criteriaInsert);
       }
       catch(PDOException $e)
       {
           echo $e->getMessage();
       }
    }

    public function login($username,$password)
    {
      $stmt = $this->db->prepare("SELECT * FROM users WHERE is_deleted ='n'");
      $stmt->execute();
      $users = $stmt->fetchAll();


      foreach ($users as $thisUser) {
        if ($thisUser['user_name'] === $username && password_verify($password,$thisUser['user_password'])) {
          $_SESSION['loggedin'] = $thisUser['user_ID'];
          $_SESSION['username'] = $thisUser['user_name'];
          return true;
        }
      }
      return false;
   }

   public function is_loggedin()
   {
      if(isset($_SESSION['loggedin']))
      {
         return true;
      }
   }

   public function redirect($url)
   {
       header("Location: $url");
   }

   public function logout()
   {
        session_destroy();
        unset($_SESSION['loggedin']);
        unset($_SESSION['username']);
        return true;
   }

   public function getAllUserAccounts()
   {
     $stmt = $this->db->prepare("SELECT * FROM users");
     $stmt->execute();
     $results=$stmt->fetchAll(PDO::FETCH_ASSOC);
     return $results;
   }
   public function doesUserExist($userID){
     $stmt = $this->db->prepare("SELECT * FROM users WHERE user_ID = :id");

     $criteria = [
       'id' => $userID
     ];

     $stmt->execute($criteria);
     $result=$stmt->fetch();
     if (!empty($result)) {
       return true;
     }else {
       return false;
     }
   }
   /////////////////////////////////////////////////////////////////////////////////////////////////////////
   public function getAllRoles()
   {
     $stmt = $this->db->prepare("SELECT * FROM roles");
     $stmt->execute();
     $results=$stmt->fetchAll(PDO::FETCH_ASSOC);
     return $results;
   }
   /////////////////////////////////////////////////////////////////////////////////////////////////////////
   public function getUserDetails()
   {
     $stmt = $this->db->prepare("SELECT * FROM users WHERE user_ID= :id");
     $stmt->execute(array(':id' => $_SESSION['loggedin']));
     $results=$stmt->fetchAll(PDO::FETCH_ASSOC);
     return $results;
   }
   /////////////////////////////////////////////////////////////////////////////////////////////////////////
   public function getUserDetailsByID($id){
     $stmt = $this->db->prepare("SELECT * FROM users WHERE user_ID= :id");
     $stmt->execute(array(':id' => $id));
     $results=$stmt->fetchAll(PDO::FETCH_ASSOC);
     return $results;
   }
   /////////////////////////////////////////////////////////////////////////////////////////////////////////
   public function getUserName()
   {
     $userDetails = $this->getUserDetails();

     foreach ($userDetails as $value) {
       return $value['user_name'];
     }
   }

   public function getUserEmailByID($userID){
     $userDetails = $this->getUserDetailsByID($userID);

     foreach ($userDetails as $value) {
       return $value['user_email'];
     }
   }



   /////////////////////////////////////////////////////////////////////////////////////////////////////////

   public function getUserNameByID($id)
   {
     $userDetails = $this->getUserDetailsByID($id);
     foreach ($userDetails as $value) {
       return $value['user_name'];
     }
   }


   public function hasPermissions($roleLevel)
   {
  
     if($this->is_loggedin()){
       $stmt = $this->db->prepare("SELECT user_role FROM users WHERE user_ID= :id");
       $stmt->execute(array(':id' => $_SESSION['loggedin']));
       $results=$stmt->fetch(PDO::FETCH_ASSOC);

       if($roleLevel >= (int)$results['user_role'])
       {
         return true;
       }
     }
   }


      /////////////////////////////////////////////////////////////////////////////////////////////////////////
      public function getUserRole()
      {
        $stmt = $this->db->prepare("SELECT user_role FROM users WHERE user_ID= :id");
        $stmt->execute(array(':id' => $_SESSION['loggedin']));
        $results=$stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$results['user_role'];
      }


      public function getUserRoleByNumber($roleID)
      {
        $stmt = $this->db->prepare("SELECT role_name FROM roles WHERE role_id= :id");
        $stmt->execute(array(':id' => $roleID));
        $results=$stmt->fetch(PDO::FETCH_ASSOC);
        return $results['role_name'];
      }

    public function getUserRoleByID($userID)
    {
      $stmt = $this->db->prepare("SELECT user_role FROM users WHERE user_ID= :id");
      $stmt->execute(array(':id' => $userID));
      $results=$stmt->fetch(PDO::FETCH_ASSOC);
      return $results['user_role'];
    }
/////////////////////////////////////////////////////////////////////////////////////////////////////////
      public function getPermissions()
      {
        $userRole = $this->getUserRole();
        if($userRole === 1){
          $perms = array(
    
            array( //change article category, editing title or text. deleting articles
              'link' => 'manageArticles.php',
              'name' => 'Manage Articles'
            ),
            array(
              'link' => 'deleteCategory.php',
              'name' => 'Delete Category'
            ),
        
          );
        }else if($userRole === 2){
          $perms = array(
          
          );
        }else {
          return null;
        }
        return $perms;
      }

        public function hasSameName($name){
          $stmt_username = $this->db->prepare('SELECT * FROM users WHERE user_name= :username');

          $criteria = [
           'username' => $name
          ];

          $stmt_username->execute($criteria);
          $results = $stmt_username->fetchAll();

          if(sizeof($results) > 0){
            return true;
        }else{
          return false;
        }
      }

        public function hasSameEmail($email){
          $stmt_email = $this->db->prepare('SELECT * FROM users WHERE user_email= :email');

          $criteria = [
           'email' => $email
          ];

          $stmt_email->execute($criteria);
          $results = $stmt_email->fetchAll();

          if(sizeof($results) > 0){
            return true;
        }else{
          return false;
        }
      }

      public function getUserID(){
        if($this->is_loggedin()){
          return $_SESSION['loggedin'];
        }else {
          return 0;
        }
      }
     
      public function deleteUser($userID){
        $stmt = $this->db->prepare('UPDATE users SET is_deleted = "y" WHERE user_id = :id');

        $criteria = [
          'id' => $userID
        ];
        $stmt->execute($criteria);
      }
     
      public function isDeleted($userID){
        $stmt = $this->db->prepare('SELECT is_deleted FROM users WHERE user_ID= :id');

        $criteria = [
          'id' => $userID
        ];
        $stmt->execute($criteria);
        $result = $stmt->fetch();

        if ($result['is_deleted'] === 'y') {
          return true;
        }elseif($result['is_deleted'] === 'n') {
          return false;
        }
      }
     
      public function restoreUser($userID){
        $stmt = $this->db->prepare('UPDATE users SET is_deleted = "n" WHERE user_id = :id');

        $criteria = [
          'id' => $userID
        ];
        $stmt->execute($criteria);
      }

      
      public function changeRole($newRole, $userID)
      {
        $stmt = $this->db->prepare("UPDATE users SET user_role = :newRole WHERE user_id = :id");
        $stmt->execute(array('id' => $userID, 'newRole' => $newRole));
      }
     
      public function changeName($newName, $userID){
        $stmt = $this->db->prepare("UPDATE users SET user_name = :newName WHERE user_id = :id");
        $stmt->execute(array('id' => $userID, 'newName' => $newName));
      }

     
      public function changeEmail($newMail, $userID){
        $stmt = $this->db->prepare("UPDATE users SET user_email = :newMail WHERE user_id = :id");
        $stmt->execute(array('id' => $userID, 'newMail' => $newMail));
      }

     
      public function changeNewsletter($newState, $userID){
        $stmt = $this->db->prepare("UPDATE users SET on_newsletter = :newState WHERE user_id = :id");
        $stmt->execute(array('id' => $userID, 'newState' => $newState));
      }
     
      public function sendMailToSubscribedUsers($articleObj){
        $stmt = $this->db->prepare('SELECT user_email FROM users WHERE on_newsletter ="on"');
        $stmt->execute();
        $emails = $stmt->fetchAll();


        $subject = 'New article posted at News Website!';
        $from = 'admin@newsweb.com';

        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        $headers .= 'From: '.$from."\r\n".
        'Reply-To: '.$from."\r\n" .
        'X-Mailer: PHP/' . phpversion();

        $message = '<html><body>';
        $message .= '<h1 style="color:#f40;">New Article!</h1>';
        $message .= '<p>A new article has been posted in News Website. Follow the link to read:</p>';
        $message .= '<a href="article.php?articleID='.$articleObj->getID().'">Click Here</a>';
        $message .= '</body></html>';

        foreach ($emails as $email) {
          mail($email['user_email'], $subject, $message, $headers);
        }
      }


    }



?>
