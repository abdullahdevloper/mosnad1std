<?php

session_start();

$DB_host = "localhost";
$DB_user = "root";
$DB_pass = "";
$DB_name = "news_database";

try
{
     $pdo = new PDO("mysql:host={$DB_host};dbname={$DB_name}",$DB_user,$DB_pass);
     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
     echo $e->getMessage();
}


require 'userQueries.class.php';
require 'categoryQueries.class.php';
require 'articleQueries.class.php';
require 'commentQueries.class.php';

$user = new User($pdo);
$categoryQueries = new categoryQueries($pdo);
$articleQueries = new articleQueries($pdo);
$commentQueries = new commentQueries($pdo);



?>
