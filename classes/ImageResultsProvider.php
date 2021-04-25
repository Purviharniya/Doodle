<?php

class ImageResultsProvider
{
    private $con;
    public function __construct($con)
    {
        $this->con = $con;
    }

    public function getNumResults($term)
    {
        $query = $this->con->prepare("SELECT COUNT(*) as total from images where
                                      title LIKE :term or siteURL LIKE :term or alt like :term AND broken=0");
        $searchTerm = "%" . $term . "%";
        $query->bindParam(":term", $searchTerm);
        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC);
        return $row["total"];

    }

    public function getSiteResults($page, $pagesize, $term)
    {
        $fromLimit = ($page - 1) * $pagesize;

        $query = $this->con->prepare("SELECT * from images where
                                      title LIKE :term  or siteURL LIKE :term or alt like :term AND broken=0 order by clicks desc LIMIT :fromlimit,:pagesize");
        $searchTerm = "%" . $term . "%";
        $query->bindParam(":term", $searchTerm);
        $query->bindParam(":fromlimit", $fromLimit, PDO::PARAM_INT);
        $query->bindParam(":pagesize", $pagesize, PDO::PARAM_INT);
        $query->execute();

        $resultsHTML = "<div class='imageResults m-3'>";
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $id = $row["id"];
            $title = $row["title"];
            $alt = $row["alt"];
            $imageurl = $row["imageURL"];
            $siteurl = $row["siteURL"];

            if ($title) {
                $displayText = $title;
            } else if ($alt) {
                $displayText = $alt;
            } else {
                $displayText = $imageurl;
            }
            $resultsHTML .= "<div class='grid-item d-flex flex-column'>
                                <a href='$imageurl'>
                                    <img src='$imageurl' alt='$alt'>
                                    <span class='details'>$displayText</span>
                                </a>
                            </div>
                            ";
        }

        $resultsHTML .= "</div>";
        return $resultsHTML;
    }

    private function trimResults($string, $characterlimit)
    {
        $dots = strlen($string) > $characterlimit ? "..." : "";
        return substr($string, 0, $characterlimit) . $dots;
    }

}