<?php

$term = isset($_GET['term']) ? $_GET['term'] : exit("You must enter a term");

$type = isset($_GET['type']) ? $_GET['type'] : 'sites';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="vendor/css/sb-admin-2.min.css">
    <link rel="stylesheet" href="vendor/css/fonts.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

    <title>Document</title>
    <style>
    .form-control {
        border-bottom-right-radius: 0;
        border-top-right-radius: 0;
    }

    .search {
        border-bottom-left-radius: 0;
        border-top-left-radius: 0;
    }

    .tabscontainer {
        margin-left: 150px;
    }

    .tablist {
        padding: 0;
        margin: 0;
    }

    .tablist li {
        display: inline-block;
        padding: 0 16px 12px 16px;
        color: #777 !important;
    }

    .tablist li a {
        text-decoration: none;
        color: #777 !important;
    }

    .tablist li.active {
        border-bottom: 3px solid #1A73E8;
    }

    .tablist li.active a {
        color: #1A73E8 !important;
        font-weight: bold;
    }
    </style>
</head>

<body>
    <div class="header" style="min-height:20vh;width:100vw;">
        <div class="d-flex flex-row align-items-center">
            <div class="text-center" style="width:150px;">
                <img src="vendor/images/logo.png" class="img-fluid" alt="google">
            </div>
            <form class="col-7" method="GET" action="search.php">

                <div class="d-flex flex-row justify-content-center align-items-center">
                    <input type="text" class="form-control shadow" name="term">
                    <button type="submit" class="btn search btn-primary shadow" name="search">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="tabscontainer">
            <ul class="tablist">
                <li class='<?php echo $type == "sites" ? "active" : ""; ?>'>
                    <a href='<?php echo "search.php?term=$term&type=sites"; ?>'>Sites</a>
                </li>
                <li class='<?php echo $type == "images" ? "active" : ""; ?>'>
                    <a href='<?php echo "search.php?term=$term&type=images"; ?>'>Images</a>
                </li>
            </ul>
        </div>
    </div>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

</body>

</html>