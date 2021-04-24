<?php
include "config.php";
include "classes/DomDocumentParser.php";

$crawledAlready = array();
$crawling = array();
$alreadyFoundImages = array();

function existsLink($url)
{
    global $con;

    $query = $con->prepare("SELECT * from sites where url=:url");

    $query->bindParam(":url", $url);
    $query->execute();
    return $query->rowCount() != 0;
}

function insertLink($url, $title, $description, $keywords)
{

    global $con;

    $query = $con->prepare("INSERT INTO sites(url,title,description,keywords)
                            VALUES (:url,:title,:description,:keywords)");

    $query->bindParam(":url", $url);
    $query->bindParam(":title", $title);
    $query->bindParam(":description", $description);
    $query->bindParam(":keywords", $keywords);

    return $query->execute();
}

function insertImage($url, $src, $alt, $title)
{

    global $con;

    $query = $con->prepare("INSERT INTO images(siteURL,imageURL,alt,title)
                            VALUES (:siteURL,:imageURL,:alt,:title)");

    $query->bindParam(":siteURL", $url);
    $query->bindParam(":imageURL", $src);
    $query->bindParam(":alt", $alt);
    $query->bindParam(":title", $title);

    return $query->execute();
}

function createLinks($src, $url)
{

    $scheme = parse_url($url)["scheme"];
    $host = parse_url($url)["host"];

    if (substr($src, 0, 2) == '//') {
        $src = $scheme . ":" . $src;
    } else if (substr($src, 0, 1) == '/') {
        $src = $scheme . "://" . $host . $src;
    } else if (substr($src, 0, 2) == './') {
        $src = $scheme . "://" . $host . dirname(parse_url($url)["path"]) . substr($src, 1);
    } else if (substr($src, 0, 3) == '../') {
        $src = $scheme . "://" . $host . '/' . $src;
    } else if (substr($src, 0, 5) != 'https' && substr($src, 0, 4) != 'http') {
        $src = $scheme . "://" . $host . '/' . $src;
    }

    return $src;
}

function getDetails($url)
{
    global $alreadyFoundImages;
    $parser = new DomDocumentParser($url);
    $titleArray = $parser->getTitleTags();

    if (sizeof($titleArray) == 0 || $titleArray->item(0) == null) {
        return;
    }

    $title = $titleArray->item(0)->nodeValue;
    $title = str_replace("\n", "", $title);
    if ($title == "") {
        return;
    }

    $description = "";
    $keywords = "";

    $metaArray = $parser->getMetaTags();

    foreach ($metaArray as $meta) {
        // print_r($meta->getAttribute("name"));
        if ($meta->getAttribute("name") == "description") {
            $description = $meta->getAttribute("content");
        }
        if ($meta->getAttribute("name") == "keywords") {
            $keywords = $meta->getAttribute("content");
        }
    }

    $description = str_replace("\n", "", $description);
    $keywords = str_replace("\n", "", $keywords);

    if (existsLink($url)) {
        echo "$url already exists<br>";
    } else if (insertLink($url, $title, $description, $keywords)) {
        echo "SUCCESS: $url<br>";
    } else {
        echo "ERROR: failed to insert $url";
    }

    // echo "URL: $url, TITLE: $title, DESCR: $description , KEY: $keywords<br>";

    $imageArray = $parser->getImageTags();
    foreach ($imageArray as $image) {
        $src = $image->getAttribute("src");
        $alt = $image->getAttribute("alt");
        $title = $image->getAttribute("title");
        if (!$alt && !$title) {
            continue;
        }

        $src = createLinks($src, $url);

        if (!in_array($src, $alreadyFoundImages)) {
            $alreadyFoundImages[] = $src;
            insertImage($url, $src, $alt, $title);
        }
    }
}

function followLinks($url)
{

    global $crawledAlready;
    global $crawling;

    $parser = new DomDocumentParser($url);
    $linkList = $parser->getLinks();

    foreach ($linkList as $link) {
        $href = $link->getAttribute("href");
        if (strpos($href, '#') !== false) {
            continue;
        } else if (substr($href, 0, 11) == 'javascript:') {
            continue;
        }

        $href = createLinks($href, $url);

        if (!in_array($href, $crawledAlready)) {
            $crawledAlready[] = $href;
            $crawling[] = $href;
            getDetails($href);
        }
        // else {
        //     return;
        // }

        // echo $href . "<br>";
    }

    array_shift($crawling);
    foreach ($crawling as $site) {
        followLinks($site);
    }
}

$startURL = "http://www.apple.com";
followLinks($startURL);