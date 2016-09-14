<?php
include("includes/nav-menu.php");

$htmlBody="";

if ((!isset($_POST['tag'])) || ($_POST['tag']=="")) {
   header("Location: managetags.php");
   exit;
}
if (str_word_count($_POST['tag'])>1) {
    $htmlBody.=<<<END
    <h3>Slight Problem</h3>
    <p>Single word tags only please</p>
    <a href=managetags.php> Manage Tags</a>
END;
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
        $newtag=$_POST['tag'];
        $htmlBody.=<<<END
        <h3>Success!</h3>
        <p>Your new tag "$newtag" has been added</p>
        <a href=managetags.php>Manage Tags</a>
END;
} 
        catch (PDOException $e) {
            printf("We have a problem: %s\n ", $e->getMessage());
        }
    } // end brace for if(isset
        
    else {

    echo "We have a problem.";

    }

?>
 <head>
    <title>Search YouTube Favourites</title>
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