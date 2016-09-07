<?php
if((!isset($_POST['tag']) || $_POST['tag']=="")) {
    //if not tags selected we set this variable to empty
    $tags = "";
} else if((isset($_POST['edittedtitle'])) && (isset($_POST['videoid'])) && (isset($_POST['tag']))) {
    $title = $_POST['edittedtitle'];
    $videoid = $_POST['videoid'];
    $tags = $_POST['tag'];
   //$name = $_POST['edittedtitle'];
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
            $lastId = $db->lastInsertId();
            if(isset($tags)){            
                $stmt = $db->prepare("insert into favourite_tags (favouriteid_FK, tagid_FK) values (:favourite_id, :tagid)");
                foreach ($tags as $tag) {
                    $stmt->execute(array(
                    ":favourite_id" => $lastId,
                    ":tagid" => $tag)
                    );
                }
            }           
            $stmt->closeCursor();
        } 
        catch (PDOException $e) {
            printf("We have a problem: %s\n ", $e->getMessage());
        }
    } 
        
    else {

    echo "We have a problem.";

    }

?>