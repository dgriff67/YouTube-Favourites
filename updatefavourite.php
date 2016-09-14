<?php
include("includes/nav-menu.php");

$htmlBody="";

if((isset($_POST['edittedtitle'])) && (isset($_POST['favourite_id']))) {
    $title = $_POST['edittedtitle'];
    $favourite_id = $_POST['favourite_id'];
    //First update the favourite
        try {
            //connection details for database held in config.ini file
            //parse the ini file to retrieve connection details
            $config = parse_ini_file("config.ini",true);
            $host = $config['mysqlConnection']['host'];
            $dbname = $config['mysqlConnection']['name'];
            $user = $config['mysqlConnection']['user'];
            $pass = $config['mysqlConnection']['pass'];
            //create new PDO object using connection details
            $db = new PDO("mysql:host=$host;dbname=$dbname",$user,$pass);
            //we want PDO to throw an informative exception if there is a problem
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $db->prepare("update favourites set title = :title where favourite_id = :favourite_id");  
                            
            $stmt->execute(array(
                ":title" => $title,
                ":favourite_id" => $favourite_id
            ));
            //Now let's insert where a selected tag does not already exist
            if (isset($_POST['tag'])) {
                //if no tags selected we set this variable to empty
                $tags = $_POST['tag'];
                $stmt = $db->prepare("insert ignore into favourite_tags (favouriteid_FK, tagid_FK) values (:favourite_id, :tagid)");
                foreach ($tags as $tag) {
                    $stmt->execute(array(
                        ":favourite_id" => $favourite_id,
                        ":tagid" => $tag)
                    );
                }
            } 
            //Now let's check for new tags
            if ((isset($_POST['newtags'])) && (!empty($_POST['newtags']))) {
                //if not tags selected we set this variable to empty
                $newtags = $_POST['newtags'];
                $newtag = array_map('trim', explode(',', $newtags));
                foreach ($newtag as $tag) {
                    $stmt = $db->prepare("insert into tags (tag) values (:tag)");
                    $stmt->bindParam(':tag', $tag, PDO::PARAM_INT);        
                    $stmt->execute();
                    $lastTagId = $db->lastInsertId();
                    $stmt = $db->prepare("insert into favourite_tags (favouriteid_FK, tagid_FK) values (:favourite_id, :tagid)");
                    $stmt->execute(array(
                        ":favourite_id" => $favourite_id,
                        ":tagid" => $lastTagId)
                    );
                }  
            } 
            $stmt->closeCursor();
            $htmlBody.='<h3>Editted Favourite</h3>';
            $htmlBody.=sprintf('<p>Congratulations you have editted "%s"</p>', $title);
            $htmlBody.='<a href=searchfavourites.php> Search Favourites</a>';
        } 
        catch (PDOException $e) {
            printf("We have a problem: %s\n ", $e->getMessage());
        }
    } 
        
    else {

    echo "We have a problem.";

    }

?>
<!doctype html>
<html>
  <head>
    <title>Update Favourite</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css" rel="stylesheet">
    <link href = "css/youtube_favourites.css" rel = "stylesheet">
  </head>
  <body>
    <?php echo $navbar?>
    <?php echo $htmlBody?>
  </body>
</html>