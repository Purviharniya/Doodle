<?php

include "../config.php";

if (isset($_POST['linkid'])) {
    $query = $con->prepare("UPDATE sites SET clicks = clicks+1 where id=:linkid");
    $query->bindParam(":linkid", $_POST['linkid']);
    $query->execute();
} else {
    echo "link id not found";
}