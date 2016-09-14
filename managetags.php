<?php
include("includes/nav-menu.php");
$htmlBody="";
$htmlBody.=<<<END
  <h3>Manage Your Favourites Tags</h3>
    <form action="addtag.php" method="POST">
    <div class="form-group">
        <label for="tag">New Tag:</label>
        <input type="text" class="form-control" name="tag" placeholder="Enter New Tag">
    </div>
        <button type="submit" class= "btn btn-primary" name="submit">Add</button>
    </form>
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
            echo "Sorry, no tags";
            exit;
        } else {
            $htmlBody.=<<<END
            <form action="deletetag.php" method="POST">
            <div class="form-group">
END;
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                //We add a checkbox for deleting tags.
                $checkbox = '<input type="checkbox" name="tag[]" id="tag" value="'. urldecode($row["tagid"]) .'">';
                $tagname = htmlentities($row["tag"]);
                $htmlBody.=sprintf('<label class="checkbox-inline">%s %s </label>',$checkbox,$tagname);             
            }
            $htmlBody.='</div>';
        }
    } catch (PDOException $e) {
            printf("We have a problem: %s\n ", $e->getMessage());
    }
    $htmlBody.='<button type="submit" class= "btn btn-danger" name="delete">Delete</button>';
    
?>
<!doctype html>
<html>
  <head>
    <title>Manage Your Tags</title>
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