<?php
include "config.php";
include "classes/DomDocumentParser.php";

$crawledAlready = array(); //to store already crawled websites
$crawling = array(); //to store the sites that are to be crawled including already crawled
$alreadyFoundImages = array(); //to store already crawled images

function existsLink($url)
{
    global $con; //defining a reference to the con variable of config.php

    $query = $con->prepare("SELECT * from sites where url=:url"); //selecting the sites to know which of those have been crawled

    $query->bindParam(":url", $url);
    $query->execute();
    return $query->rowCount() != 0;
}

//function to insert the crawled site to the db
function insertLink($url, $title, $description, $keywords)
{
    global $con; //defining a reference to the con variable of config.php

    $query = $con->prepare("INSERT INTO sites(url,title,description,keywords)
                            VALUES (:url,:title,:description,:keywords)");

    $query->bindParam(":url", $url);
    $query->bindParam(":title", $title);
    $query->bindParam(":description", $description);
    $query->bindParam(":keywords", $keywords);

    return $query->execute();
}

//function to insert images to the db
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

//function to create links of sites/images
function createLinks($src, $url)
{

    $scheme = parse_url($url)["scheme"];
    $host = parse_url($url)["host"];

    //if of the form http://www.abc.com
    if (substr($src, 0, 2) == '//') {
        $src = $scheme . ":" . $src;
    }
    //if of the form /about/about.php => original link should be http://www.abc.com/about/about.php or something like this
    else if (substr($src, 0, 1) == '/') {
        $src = $scheme . "://" . $host . $src;
    }
    //if of the form ./about/about.php => original link should be http://www.abc.com/about/about.php or something like this
    else if (substr($src, 0, 2) == './') {
        $src = $scheme . "://" . $host . dirname(parse_url($url)["path"]) . substr($src, 1);
    }
    //if of the form ../about/about.php => original link should be http://www.abc.com/about/about.php or something like this
    else if (substr($src, 0, 3) == '../') {
        $src = $scheme . "://" . $host . '/' . $src;
    }
    //if of the form about/about.php => original link should be http://www.abc.com/about/about.php or something like this
    else if (substr($src, 0, 5) != 'https' && substr($src, 0, 4) != 'http') {
        $src = $scheme . "://" . $host . '/' . $src;
    }

    return $src;
}

function getDetails($url)
{
    global $alreadyFoundImages;
    $parser = new DomDocumentParser($url); //parser to get the parsed document of the website
    $titleArray = $parser->getTitleTags(); // get the <title> tags for title

    //if the site has no title, we ignore it
    if (sizeof($titleArray) == 0 || $titleArray->item(0) == null) {
        return;
    }

    $title = $titleArray->item(0)->nodeValue; //get the title value
    $title = str_replace("\n", "", $title); //replace a new line
    //if title is empty, we ignore the site
    if ($title == "") {
        return;
    }

    $description = "";
    $keywords = "";

    $metaArray = $parser->getMetaTags(); //get the <meta> tags for keyword and description

    foreach ($metaArray as $meta) {
        // print_r($meta->getAttribute("name"));
        //get the description
        if ($meta->getAttribute("name") == "description") {
            $description = $meta->getAttribute("content");
        }
        //get the keywords
        if ($meta->getAttribute("name") == "keywords") {
            $keywords = $meta->getAttribute("content");
        }
    }

    //replacing the new lines in description and keywords
    $description = str_replace("\n", "", $description);
    $keywords = str_replace("\n", "", $keywords);

    //check if the link has already been crawled and inserted into the db, and ignore the link.
    if (existsLink($url)) {
        echo "$url already exists<br>";
    }
    //else we insert the link into the db
    else if (insertLink($url, $title, $description, $keywords)) {
        echo "SUCCESS: $url<br>";
    } else {
        echo "ERROR: failed to insert $url";
    }

    // echo "URL: $url, TITLE: $title, DESCR: $description , KEY: $keywords<br>";

    //get the image tags
    $imageArray = $parser->getImageTags();
    foreach ($imageArray as $image) {
        $src = $image->getAttribute("src"); //get the src attribute from the image tag
        $alt = $image->getAttribute("alt"); //get the alt attribute from the image tag
        $title = $image->getAttribute("title"); //get the title attribute from the image tag
        //if image has no alt or title attribute, we ignore the image
        if (!$alt && !$title) {
            continue;
        }

        //convert the relative path of image to absolute path
        $src = createLinks($src, $url);

        //check if link has not been already found
        if (!in_array($src, $alreadyFoundImages)) {
            $alreadyFoundImages[] = $src; //add the image to the already found array
            insertImage($url, $src, $alt, $title); //insert the image into the db
        }
    }
}

function followLinks($url)
{
    //adding the global references
    global $crawledAlready;
    global $crawling;

    $parser = new DomDocumentParser($url); //getting the parsed document of the website
    $linkList = $parser->getLinks(); //get all the <a> tags from the website

    foreach ($linkList as $link) {
        $href = $link->getAttribute("href"); //get the href attribute
        if (strpos($href, '#') !== false) { //if href=="#" , we ignore the link
            continue;
        } else if (substr($href, 0, 11) == 'javascript:') { //if href=="javascript:" , we ignore the link
            continue;
        }
        //convert the href to link; relative->absolute
        $href = createLinks($href, $url);

        //if the link has not been crawled
        if (!in_array($href, $crawledAlready)) {
            $crawledAlready[] = $href; //add it to the crawled array
            $crawling[] = $href; //add it to the crawling array
            getDetails($href); //get the details- title,desc,keywords of the website.
        }
        // else {
        //     return;
        // }

        // echo $href . "<br>";
    }

    array_shift($crawling); //remove the current link(last crawled link) from the crawling array
    //continue crawling all the links recursively
    foreach ($crawling as $site) {
        followLinks($site);
    }
}

$startURL = "http://www.apple.com"; //the site to be crawled.
followLinks($startURL); //the entry point