<?php
    include '../config/app.php';
    include '../config/database.php';
    include '../config/security.php';

if($_GET){
    if(deletePet($_GET['id'], $conx)){
        $_SESSION['message'] = "La mascota fue eliminada con exito!";
        echo "<script> window.location.replace('dashboard.php');</script>";
    }
}

?>

