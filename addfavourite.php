<!doctype html>
<html>
  <head>
    <title>Search YouTube Favourites</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css" rel="stylesheet">
    <link href = "css/youtube_favourites.css" rel = "stylesheet">
  </head>
  <body>
    <h3>Add Favourite</h3>
<?php

if((isset($_POST['edittedtitle'])) && (isset($_POST['videoid']))) {
    $title = $_POST['edittedtitle'];
    $videoid = $_POST['videoid'];
    //First we add a new favourite
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
            $stmt = $db->prepare("insert into favourites (title, videoid) values (:title, :videoid)");  
                            
            $stmt->execute(array(
                ":title" => $title,
                ":videoid" => $videoid
            ));
            $lastFavouriteId = $db->lastInsertId();
            //Now let's check for checkbox tags
            if (isset($_POST['tag'])) {
                //if no tags selected we set this variable to empty
                $tags = $_POST['tag'];
                $stmt = $db->prepare("insert into favourite_tags (favouriteid_FK, tagid_FK) values (:favourite_id, :tagid)");
                foreach ($tags as $tag) {
                    $stmt->execute(array(
                        ":favourite_id" => $lastFavouriteId,
                        ":tagid" => $tag)
                    );
                }
            } 
            //Now let's check for new tags
            if (isset($_POST['newtags'])) {
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
                        ":favourite_id" => $lastFavouriteId,
                        ":tagid" => $lastTagId)
                    );
                }  
            } 
            $stmt->closeCursor();
            printf('<p>Congratulations you have added "%s"</p>', $title);
            printf("<a href=searchfavourites.php> Search Favourites</a>");
        } 
        catch (PDOException $e) {
            printf("We have a problem: %s\n ", $e->getMessage());
        }
    } 
        
    else {

    echo "We have a problem.";

    }

?>
  </body>
</html>