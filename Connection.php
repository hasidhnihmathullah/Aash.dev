<?php
//$connection = mysqli_connect('localhost','root','','userlogin');
$connection = mysqli_connect('localhost','root','','aashdev_portfolio');

if(mysqli_connect_errno()){
    die("Database connection failed: ");
}
else{
    echo "connection successful";
}
   
?>