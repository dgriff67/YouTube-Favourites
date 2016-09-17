<?php
include("includes/nav-menu.php");

$htmlBody = "";
$name = "";
$title = "";
$videoid = "";
If((!isset($_POST['favourite'])) && (!isset($_POST['btn_submit']))) {
    header("Location: index.php");
    exit;
}  else if((isset($_POST['favourite'])) && (!isset($_POST['btn_submit']))) {
    header("Location: index.php");
    exit;
} else if((!isset($_POST['favourite'])) && (isset($_POST['btn_submit']))) {
    $htmlBody.= <<<END
    <h3>No Video Selected</h3>
    <p>Please select a video to add to favourites!</p>
    <a href=index.php> Search Favourites</a>
END;

} else if ((isset($_POST['favourite'])) && (isset($_POST['btn_submit']))) {
    $name = $_POST['favourite'];
    foreach ($name as $favourite){
        $title = substr(html_entity_decode($favourite),0,-12);
        $videoid = substr(html_entity_decode($favourite),-11);
    }
#Open database
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
        header("Location: searchfavourites.php");
        exit;
    } catch (PDOException $e) {
        printf("We have a problem: %s\n ", $e->getMessage());
    }
} else {
    echo "We have a problem.";
}
?>
<!doctype html>
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
    <?php echo $navbar?>
    <?php echo $htmlBody?>
  </body>
</html>