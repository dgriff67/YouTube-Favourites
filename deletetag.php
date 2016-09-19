<?php
include("includes/nav-menu.php");
//Let's check to see if a tag is selected - if not we give a link back
$htmlBody = "";
If((!isset($_POST['tag'])) && (!isset($_POST['delete']))){
    header("Location: managetags.php");
    exit;
} 
If((!isset($_POST['tag'])) && (isset($_POST['delete'])) ) {
    $title = "No favourite selected";
    //Heredoc
    $htmlBody.= <<<END
    <h3>No Tag Selected</h3>
    <p>Please select a tag</p>
    <a href=managetags.php>Manage Tags</a>
END;
} 

if((isset($_POST['tag'])) && (isset($_POST['delete']))){
    $name = $_POST['tag'];
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
    //First we need to delete all favourite_tags with this tagid
    $stmt = $db->prepare("DELETE from favourite_tags where tagid_FK = :tagid"); 
    foreach ($name as $tag){
        $stmt->execute(array(
        ":tagid" => $tag)
        );
    }
    
    $stmt = $db->prepare("delete from tags where tagid = :tagid");  
                    
    foreach ($name as $tag){
        $stmt->execute(array(
        ":tagid" => $tag)
        );
    }
    $stmt->closeCursor();
    header("Location: managetags.php");
    } 
    catch (PDOException $e) {
        printf("We have a problem: %s\n ", $e->getMessage());
    }
} 
?>
<html>
  <head>
    <title>Search YouTube Favourites</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css" rel="stylesheet">
    <script type="text/javascript" src="js/jquery-3.1.0.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  
    <link href = "css/youtube_favourites.css" rel = "stylesheet">
  </head>
    <body>
    <?php echo $navbar;?>
    <?php echo $htmlBody?>
    </body>
  </html>