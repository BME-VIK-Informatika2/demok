<?php

require_once 'lib/helpers.php';
session_start();

if(isset($_POST['id'])){

    $id = intval($_POST['id']);

    $db = connectDatabase();

    $stmt = $db->prepare("DELETE FROM posts WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if($stmt->rowCount()){
        $_SESSION['message'] = 'Post deleted successfully.';
    }

}

header('Location: index.php');
exit;