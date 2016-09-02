<?php


    if(isset($_POST['favourite'])) {
    $name = $_POST['favourite'];
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
                        
        //echo "You chose the following favourite(s): <br>";
            foreach ($name as $favourite){
            //echo "title: " . substr($favourite,0,-12). " Videoid: " .substr($favourite,-11) ."<br />";
            //we are going to use prepared statements for clean coding and to defend against SQL injection
                $stmt->execute(array(
                ":title" => substr($favourite,0,-12),
                ":videoid" => substr($favourite,-11))
                );
            }
            $stmt->closeCursor();
        } 
        catch (PDOException $e) {
            printf("We have a problem: %s\n ", $e->getMessage());
        }
    } // end brace for if(isset
        
    else {

    echo "You did not choose a favourite.";

    }

?>