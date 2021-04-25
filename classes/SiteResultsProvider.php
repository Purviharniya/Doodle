<?php

class SiteResultsProvider
{
    private $con;
    public function __construct($con)
    {
        $this->con = $con;
    }

    public function getNumResults($term)
    {
        $query = $this->con->prepare("SELECT COUNT(*) as total from sites where
                                      title LIKE :term or url like :term or keywords LIKE :term
                                      or description like :term");
        $searchTerm = "%" . $term . "%";
        $query->bindParam(":term", $searchTerm);
        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC);
        return $row["total"];

    }

    public function getSiteResults($page, $pagesize, $term)
    {
        $fromLimit = ($page - 1) * $pagesize;

        $query = $this->con->prepare("SELECT * from sites where
                                      title LIKE :term or url like :term or keywords LIKE :term
                                      or description like :term order by clicks desc LIMIT :fromlimit,:pagesize");
        $searchTerm = "%" . $term . "%";
        $query->bindParam(":term", $searchTerm);
        $query->bindParam(":fromlimit", $fromLimit, PDO::PARAM_INT);
        $query->bindParam(":pagesize", $pagesize, PDO::PARAM_INT);
        $query->execute();

        $resultsHTML = "<div class='siteResults d-flex flex-column'>";
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $id = $row["id"];
            $title = $row["title"];
            $description = $row["description"];
            $url = $row["url"];

            $title = $this->trimResults($title, 40);
            $description = $this->trimResults($description, 100);

            $resultsHTML .= "<div class='result-container d-flex flex-column'>

                                <h4 class='title'>
                                    <a class='result' href='$url' data-linkid='$id'>
                                        $title
                                    </a>
                                </h4>
                                <span class='url'>$url </span>
                                <span class='description'>$description </span>
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