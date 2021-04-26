<?php
include "config.php";
$term = isset($_GET['term']) ? $_GET['term'] : exit("You must enter a term");

$type = isset($_GET['type']) ? $_GET['type'] : 'sites';
$page = isset($_GET['page']) ? $_GET['page'] : 1;

// include "crawl.php";

include "classes/SiteResultsProvider.php";
include "classes/ImageResultsProvider.php";

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
    <script src="vendor/jquery/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />

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

    .numResults {
        margin-left: 150px;
        color: #808080;
        font-size: 0.9rem;
    }

    .main-results-section .result-container {
        margin-left: 150px;
        margin-bottom: 26px;
    }

    .siteResults {
        margin-top: 26px;
    }

    .result-container .url {
        color: #006621;
        font-size: 17px;
    }

    .result-container .description {
        font-size: 16px;
    }

    .page-no-container img {
        height: 37px;
    }

    .grid-item {
        position: relative;
        margin-bottom: 5px;
    }

    .grid-item img {
        max-width: 200px;
        min-width: 50px;
        visibility: hidden;
    }

    .grid-item .details {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        font-size: 0.8rem;
        padding: 3px;
        box-sizing: border-box;
        white-space: nowrap;
        color: #fff;
        overflow: hidden;
        background: rgba(0, 0, 0, 0.8);
        visibility: hidden;
    }

    .grid-item:hover .details {
        visibility: visible;
    }

    .fancybox-caption__body {
        text-align: left;
    }
    </style>
</head>

<body>
    <div class="header" style="min-height:10vh;max-width:100vw;">
        <div class="d-flex flex-row align-items-center">
            <div class="text-center" style="width:150px;">
                <img src="vendor/images/logo.png" class="img-fluid" alt="google">
            </div>
            <form class="col-7" method="GET" action="search.php">

                <div class="d-flex flex-row justify-content-center align-items-center">
                    <input type="hidden" name="type" value="<?php echo $type; ?>">
                    <input type="text" class="form-control shadow" name="term" value="<?php echo $term; ?>">
                    <button type="submit" class="btn search btn-primary shadow">
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
    <div class="main-results-section pt-4">
        <?php

if ($type == 'sites') {
    $resultsProvider = new SiteResultsProvider($con);
    $numResults = $resultsProvider->getNumResults($term);

} else {

    $resultsProvider = new ImageResultsProvider($con);
    $numResults = $resultsProvider->getNumResults($term);
}
?>
        <div class="numResults">
            <?php echo $numResults; ?> results found
        </div>

        <?php
if ($type == 'sites') {
    $pagesize = 20;
    echo $resultsProvider->getSiteResults($page, $pagesize, $term);
} else {
    $pagesize = 30;
    echo $resultsProvider->getSiteResults($page, $pagesize, $term);
}

?>

    </div>

    <div class="pagination-container d-flex flex-row justify-content-center mb-5">
        <div class="page-btns d-flex">
            <div class="page-no-container">
                <img src="vendor/images/pageStart.png">
            </div>
            <?php

$pagestoshow = 10; //10 pages to show in the pagination
$numofpages = ceil($numResults / $pagesize); // how many pages are getting formed out of the results found //page size is 20 as defined above
// $currentpage = 1;
$pagesleft = min($pagestoshow, $numofpages); //to maintain the page length-> even if we reach to the last page which can be 2 for some search results
$currentpage = $page - floor($pagestoshow / 2); //keep the 10 page length maintained, like if we're on the 5th page, it should show 4 pages before it and 5 pages after it
if ($currentpage < 1) {
    $currentpage = 1;
}
// to keep 10 page length maintained if we reach the end of the system, like:
//if we're on 78, and there are 80 pages, then the pagination should show 71-80 instead of 73-80
//this happens because pages after 80 cant be calculated
if ($currentpage + $pagesleft > $numofpages + 1) {
    $currentpage = $numofpages + 1 - $pagesleft;
}

while ($pagesleft != 0 && $currentpage <= $numofpages) {

    if ($currentpage == $page) {
        echo "<div class='page-no-container d-flex flex-column align-items-center'>
                <img src='vendor/images/pageSelected.png'>
                <span class='pageno text-dark'> $currentpage </span>
            </div>";
    } else {
        echo "<div class='page-no-container '>
                <a href='search.php?term=$term&type=$type&page=$currentpage' class='d-flex flex-column align-items-center'>
                    <img src='vendor/images/page.png'>
                    <span class='pageno'> $currentpage </span>
                </a>
            </div>";
    }

    $currentpage++;
    $pagesleft--;
}
?>

            <div class="page-no-container">
                <img src="">
            </div>
            <div class="page-no-container">
                <img src="vendor/images/pageEnd.png">
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="vendor/js/script.js"></script>
</body>

</html>