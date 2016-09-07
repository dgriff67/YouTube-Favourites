<?php

    if(isset($_POST['favourite'])) {
    $favourites = (array)$_POST['favourite'];
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
        //First we need to delete all favourite_tags with this favourite_id
        $stmt = $db->prepare("DELETE from favourite_tags where favouriteid_FK = :favourite_id"); 
        foreach ($favourites as $favourite){
            //echo $favourite;
            $stmt->execute(array(
            ":favourite_id" => $favourite)
            );
        }
        //we are going to use prepared statements for clean coding and to defend against SQL injection
        
        $stmt = $db->prepare("delete from favourites where favourite_id = :favourite_id");  
                        
        foreach ($favourites as $favourite){
            $stmt->execute(array(
            ":favourite_id" => $favourite)
            );
        }
        $stmt->closeCursor();
        header("Location: searchfavourites.php");
        } 
        catch (PDOException $e) {
            printf("We have a problem: %s\n ", $e->getMessage());
        }
    } 
        
    else {

    echo "You did not choose a favourite.";

    }

?>