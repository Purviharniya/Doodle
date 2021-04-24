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
        $query = $this->con->prepare("SELECT COUNT(*) as total from sites where title LIKE :term or url like :term or keywords LIKE :term or description like :term");
        $searchTerm = "%" . $term . "%";
        $query->bindParam(":term", $searchTerm);
        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC);
        return $row["total"];

    }

}