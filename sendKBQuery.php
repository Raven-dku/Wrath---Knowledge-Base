<?php
/**
 * Knowledge base main menu. Displays all topics, also sets Hot issue and Updated values.
 * Also, contains categories and subcategories.
 * Reverse engineered by Raven.
 * User: Raven
 * Date: 28/08/2020
 * Time: 16:54
 */
//==== "SECURITY" PART ====
define('MY_USER_AGENT', 'Blizzard Web Client');
define('REDIRECT_LOCATION', 'https://www.wrathplus.com');

if ($_SERVER['HTTP_USER_AGENT'] !== MY_USER_AGENT) {
    header('Location: ' . REDIRECT_LOCATION);
    die();
}
//==== "SECURITY" END  ====
include 'config.php';
$page = $_GET['pageNumber'];                                                                                            // Get Page number. OFFSET in other words.
$numArticles = $_GET['numArticles'];                                                                                    // Article count to display. LIMIT in other words.

if(isset($_GET['categoryId']))
    $categoryID = $_GET['categoryId'];
if(isset($_GET['searchQuery']))
    $searchQuery = $_GET['searchQuery'];

if($page == 1) $page = 0;                                                                                               // Page number 1, doesn't need offset.
else if($page > 1) $page = $page."0";                                                                                   // Page number is greater than 1, append 0 (2 becomes 20, 3 -> 30 and so on)

$con = mysqli_connect ( $config['DB_HOST'], $config['DB_USER'], $config['DB_PASS'], $config['DB_NAME']) ;               // Establish database connection.

if (mysqli_connect_errno ( $con))                                                                                       // Something doesn't check out , better exit.
    exit("Failed to connect to MySQL: " . mysqli_connect_error());
header('Content-Type: text/xml');                                                                                // This is must as what we output is going to be in XML, not in PHP! Without this, it just wont work!


echo "<KnowledgeBaseFrameData>";
// FETCH Search results.
    if(!isset($searchQuery) && !isset($categoryID)) {
        $sql = "SELECT * FROM kbase_articles LIMIT ".$numArticles ." OFFSET ".$page;
        $sql_count = "SELECT * FROM kbase_articles";
    }
    elseif(isset($searchQuery) && isset($categoryID)) {
        $sql = "SELECT * FROM kbase_articles WHERE category = '" . (int)$categoryID . "' AND question  LIKE '%" . $searchQuery . "%' LIMIT " . $numArticles . " OFFSET " . $page;
        $sql_count = $sql;
    }
    elseif(isset($searchQuery)) {
        $sql = "SELECT * FROM kbase_articles WHERE question LIKE '%" . $searchQuery . "' OR question LIKE '" . $searchQuery . "%' OR question LIKE '%" . $searchQuery . "%' LIMIT " . $numArticles . " OFFSET " . $page;
        $sql_count = $sql;
    }
    elseif(isset($categoryID)) {
        $sql = "SELECT * FROM kbase_articles WHERE category = ".$categoryID." LIMIT ".$numArticles ." OFFSET ".$page;
        $sql_count = $sql;
    }

$result = mysqli_query($con, $sql_count);
if(mysqli_num_rows($result) > 0)
    $totalCount = $result->num_rows;
mysqli_free_result($result);


if($result = mysqli_query($con, $sql)){
    if(mysqli_num_rows($result) > 0){
        echo "<ArticleHeaders articleCount='". $totalCount ."'>";
        while($row = mysqli_fetch_array($result))
        {
            echo "<article id='". $row['id'] ."' ";                                                                     // Article ID
            if($row['question_color']){
                echo "subject='|cFF". $row['question_color'] . addslashes($row['question']) ."|r ";
                if($row['isnew'])    echo " |cFF".$color_of_new ."*NEW*|r' ";                                                           // Is subject new? if so, attach *NEW*.
                else echo "' ";                                                                                         // Subject is not new, proceed.

            } else {                                                                                                      // Question is not new, and doesn't have color.
                echo "subject='". $row['question'] ." ";
                if($row['isnew'])   echo " |cFF".$color_of_new ."*NEW*|r' ";                                                           // Is subject new? if so, attach *NEW*.
                else echo "' ";                                                                                         // Subject is not new, proceed.
            }

            if($row['ishot']) echo "hotIssue='TRUE' ";                                                                 // is Hot Issue flag set? BOOLEAN
            if($row['updated']) echo "updated='TRUE' ";                                                                // is Updated flag set? BOOLEAN


            echo " />";
        }

        echo "</ArticleHeaders>";
        // Free result set
        mysqli_free_result($result);
    } else{
        echo "No records matching your query were found.";
    }
} else
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);

echo "</KnowledgeBaseFrameData>"; ?>
