<?php

include "../config.php";

if (isset($_POST['src'])) {
    $query = $con->prepare("UPDATE images SET broken = 1 where imageURL=:src");
    $query->bindParam(":src", $_POST['src']);
    $query->execute();
} else {
    echo "image src not found";
}