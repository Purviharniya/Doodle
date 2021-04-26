<?php

include "../config.php";

if (isset($_POST['src'])) {
    $query = $con->prepare("UPDATE images SET clicks = clicks+1 where imageURL=:src");
    $query->bindParam(":src", $_POST['src']);
    $query->execute();
} else {
    echo "Image src not found";
}