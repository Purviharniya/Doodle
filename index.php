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
    </style>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;min-width:100vw;">
        <form class="col-12 col-md-7" method="GET" action="search.php">
            <div class="text-center">
                <img src="vendor/images/logo.png" alt="google" class="img-fluid w-50">
            </div>
            <div class="d-flex flex-row justify-content-center align-items-center">
                <input type="text" class="form-control shadow" name="term">
                <button type="submit" class="btn search btn-primary shadow" name="search">
                    <i class="fa fa-search"></i>
                </button>
            </div>
        </form>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

</body>

</html>