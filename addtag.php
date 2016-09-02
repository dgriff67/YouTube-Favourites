<?php

if (str_word_count($_POST['tag'])>1) {
    printf("Single word tags only please");
    printf("<a href=managetags.php> Manage Tags</a>");
}
    else if ((isset($_POST['tag'])) && (str_word_count($_POST['tag'])<2)) {
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
        $stmt = $db->prepare("insert into tags (tag) values (:tag)");
        $stmt->bindParam(':tag', $_POST['tag'], PDO::PARAM_INT);        
        $stmt->execute();
        
        $stmt->closeCursor();
        printf("New tag '". $_POST['tag'] . "' added");
        printf("<a href=managetags.php> Manage Tags</a>");
        } 
        catch (PDOException $e) {
            printf("We have a problem: %s\n ", $e->getMessage());
        }
    } // end brace for if(isset
        
    else {

    echo "You did not choose a favourite.";

    }

?>