<?php

class Organization
{
    // private $id //since the name is Always unique I suppose id isn't necessary

    private $org_name;
    private $parent_org_name;

    private $conn;

    private $rowsAllowed = 100;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function setOrgName($org_name)
    {
        $this->org_name = $org_name;
    }

    public function getOrgName()
    {
        return $this->org_name;
    }


    public function getAllOrganizations()
    {
        $orgs = [];
        $sql = "SELECT * from organization";

        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                array_push($orgs, $row);
            }
        } else {
            echo("no data");
        }

        return $orgs;
    }


    private function saveChild($org, $parentId)
    {
        $this->conn->query("INSERT INTO organization(org_name, parent_org_name)
            VALUES('" . $org->org_name . "', '" . $parentId . "');");
        if (property_exists($org, "daughters")) {
            $parent = $org->org_name;
            foreach ($org->daughters as $daughter) {
                $this->saveChild($daughter, $parent);
            }
        }
    }

    public function saveOrganization($org)
    {
        try {
            $this->conn->query("INSERT INTO organization(org_name) VALUES('" . $org->org_name . "');");
            if (property_exists($org, "daughters")) {
                $parent = $org->org_name;
                foreach ($org->daughters as $daughter) {
                    $this->saveChild($daughter, $parent);
                }
            }
        } catch (Exception $e) {
            echo "Failed to insert organizations: " . e;
            return FALSE;
        }

        return TRUE;

    }


    private function getTotalPages()
    {
        $sql = "SELECT COUNT(*) FROM ( 
                SELECT org_name, 'parent' AS relationship_type FROM organization WHERE org_name = '$this->parent_org_name'
                UNION
                SELECT org_name, 'sibling' AS relationship_type FROM organization WHERE org_name != '$this->org_name' AND parent_org_name = '$this->parent_org_name'
                UNION
                SELECT org_name, 'daughter' AS relationship_type FROM organization WHERE parent_org_name = '$this->org_name'
            ) AS temp
            ";
        $result = $this->conn->query($sql);
        $row = mysqli_fetch_row($result);
        $numRows = $row[0];

        $totalPages = ceil($numRows / $this->rowsAllowed);
        return $totalPages;
    }

    private function getPageArray($currentPage, $totalPages)
    {

        $pageArray = [];
        for($i = 1; $i <= $totalPages; $i++) {
            $page = new stdClass();
            $page->url = "http://localhost:8000/api/getRelations.php?name=$this->org_name&page=$i";
            if($i == $currentPage) {
                $page->current = "yes";
            } else {
                $page->current = "no";
            }
            $page->page_nr = $i;

            array_push($pageArray, $page);
        }
        return $pageArray;
    }

// http://localhost:8000/api/getRelations.php?name=Tier1&page=1
    public function getRelations($currentPage)
    {
        $totalPages = $this->getTotalPages();
        if ($currentPage > $totalPages) {
            $currentPage = $totalPages; //if queried page is over max, default to last page
        }

        $startRecord = ($this->rowsAllowed * $currentPage) - $this->rowsAllowed;

        $relations = [];
        $result = $this->conn->query("SELECT org_name, 'parent' AS relationship_type FROM organization WHERE org_name = '$this->parent_org_name'
            UNION
            SELECT org_name, 'sibling' AS relationship_type FROM organization WHERE org_name != '$this->org_name' AND parent_org_name = '$this->parent_org_name'
            UNION
            SELECT org_name, 'daughter' AS relationship_type FROM organization WHERE parent_org_name = '$this->org_name'
            ORDER BY org_name
            LIMIT $startRecord, $this->rowsAllowed;
            ");

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $relation = new stdClass();
                $relation->org_name = $row['org_name'];
                $relation->relationship_type = $row['relationship_type'];
                array_push($relations, $relation);
            }
        } else {
            echo("no data");
        }

        if ($totalPages > 1) {
            $relationsWithPages = new stdClass();
            $relationsWithPages->relations = $relations;
            $relationsWithPages->pages = $this->getPageArray($currentPage, $totalPages);
            return $relationsWithPages;
        } else {
            return $relations;
        }
    }


    public function readOrg()
    {

        $sql = "SELECT org_name, parent_org_name FROM organization WHERE org_name = '" . $this->org_name . "'";
        $result = $this->conn->query($sql);
        $row = mysqli_fetch_assoc($result);
        $this->org_name = $row['org_name'];
        $this->parent_org_name = $row['parent_org_name'];

    }


}


?>