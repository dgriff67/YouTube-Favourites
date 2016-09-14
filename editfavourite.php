<?php
include("includes/nav-menu.php");

$htmlBody = "";
$name = $_POST['favourite'];
$title = "";
$videoid = "";
foreach ($name as $favourite){
 $title = htmlentities(substr($favourite,0,-12));
 
 $videoid = htmlentities(substr($favourite,-11));
}
//echo $title;
$htmlBody.=<<<END
<h3>Edit Your YouTube Favourite</h3>
    <form action="addfavourite.php" method="POST">
        <div class="form-group">
            <textarea cols="30" rows="3" class="form-control" name="edittedtitle">$title</textarea>
            <input type="hidden" name="videoid" value="$videoid">
        </div>
        <div class="form-group" class="col-xs-4">
            <label for="newtags">New tag:</label>
            <input type="text" class="form-control" name="newtags" placeholder="Enter new tags, like: tag1, tag2">
        </div>
END;
 
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
            //echo "Sorry, no tags matching your search term";
            //exit;
        } else {
            $htmlBody.='<div class="form-group">';
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                //We add a checkbox for deleting tags.
                $checkbox = '<input type="checkbox" name="tag[]" id="tag" value="'. urldecode($row["tagid"]) .'">';
                $tagname = htmlentities($row["tag"]);
                //printf('<li class="list-group-item">%s %s </li>',
                $htmlBody.=sprintf('<label class="checkbox-inline">%s %s </label>', $checkbox,$tagname);             
            }
            $htmlBody.=<<<END
            '</div>'
            <button type="submit" class= "btn btn-primary" name="submit">Submit</button>
            </form>
END;
        }
        
    } catch (PDOException $e) {
            printf("We have a problem: %s\n ", $e->getMessage());
    }
?>
    

<!doctype html>
<html>
  <head>
    <title>Edit Your YouTube Favourite</title>
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
