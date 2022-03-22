<?php header('Content-Type: text/xml');                                                                                // This is must as what we output is going to be in XML, not in PHP! Without this, it just wont work!

/**
 * Knowledge base article page.
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
$con = mysqli_connect ( $config['DB_HOST'], $config['DB_USER'], $config['DB_PASS'], $config['DB_NAME']) ;               // Establish database connection.
$articleID = $_GET['articleId'];
if (mysqli_connect_errno ( $con))                                                                                       // Something doesn't check out , better exit.
    exit("Failed to connect to MySQL: " . mysqli_connect_error());
$sql = "SELECT * FROM kbase_articles WHERE id=".$articleID;
if($result = mysqli_query($con, $sql)){
    if(mysqli_num_rows($result) > 0){
        echo "<KnowledgeBaseFrameData>";
        echo "<Article>";
        while($row = mysqli_fetch_array($result))
        {
            echo "<ArticleText>";
            echo $row['answer'];
            echo "</ArticleText>";
            if($row['question_color']){
                echo "<subject>";
                echo "|cFF".$row['question_color'].$row['question']."|r";
                if($row['isnew'])
                    echo " |cFF".$color_of_new ."*NEW*|r";
                echo "</subject>";

            } else {                                                                                                      // Question is not new, and doesn't have color.
                echo "subject='". $row['question'] ." ";
                if($row['isnew']) echo "|cffff000a*NEW*|r' ";                                                           // Is subject new? if so, attach *NEW*.
                else echo "' ";                                                                                         // Subject is not new, proceed.
            }
            echo "<subject>".$row['question']."</subject>";
            echo "<id>".$row['id']."</id>";
        }
        echo "</Article>";
        echo "</KnowledgeBaseFrameData>";
        // Free result set
        mysqli_free_result($result);
    } else{
        echo "No records matching your query were found.";
    }
} else
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);


?>
