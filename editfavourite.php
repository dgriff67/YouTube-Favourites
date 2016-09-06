<?php
$name = $_POST['favourite'];
$title = "";
$videoid = "";
foreach ($name as $favourite){
 $title = htmlentities(substr($favourite,0,-12));
 
 $videoid = htmlentities(substr($favourite,-11));
}
//echo $title;
?>

<!doctype html>
<html>
  <head>
    <title>Edit New YouTube Favourite</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css" rel="stylesheet">
    <link href = "css/youtube_favourites.css" rel = "stylesheet">
  </head>
  <body>
    <h3>Edit Your New YouTube Favourite</h3>
    <form action="addfavourite.php" method="POST">
        <div class="form-group">
            <textarea cols="30" rows="3" class="form-control" name="edittedtitle"><?php echo $title ?></textarea>
            <input type="hidden" name="videoid" value="<?php echo $videoid ?>">
        </div>
    <?php
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
        $query = "select * from tags order by tag";
        $stmt = $db->query($query);
        $tagcount = $stmt->rowCount();
        if ($tagcount == 0) {
            echo "Sorry, no tags matching your search term";
            exit;
        } else {
            printf('<div class="form-group">');
            printf('<ul class="list-group">');
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                //We add a checkbox for deleting tags.
                $checkbox = '<input type="checkbox" name="tag[]" id="tag" value="'. urldecode($row["tagid"]) .'">';
                $tagname = htmlentities($row["tag"]);
                printf('<li class="list-group-item">%s %s </li>',
                $checkbox,
                $tagname
                );             
            }
            printf('</ul>');
            printf('</div>');
        }
    } catch (PDOException $e) {
            printf("We have a problem: %s\n ", $e->getMessage());
    }
    
    ?>
            <button type="submit" class= btn btn-default" name="submit">Submit</button>
    </form>
   </body>
</html>