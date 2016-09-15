
<?php
include("includes/nav-menu.php");
//Let's check to see if a favourite is selected - if not we give a link back
$title = "";
$htmlBody = "";
If((!isset($_POST['favourite'])) && (!isset($_POST['btn_submit'])) ){
    header("Location: searchfavourites.php");
    exit;
} 
If((!isset($_POST['favourite'])) && (isset($_POST['btn_submit'])) ) {
    $title = "No favourite selected";
    //Heredoc
    $htmlBody.= <<<END
    <h3>No Favourite Selected</h3>
    <p>Please select a favourite</p>
    <a href=searchfavourites.php> Search Favourites</a>
END;
} 

//So we know that both favourite and btn_submit are set

if((isset($_POST['favourite'])) && ($_POST['btn_submit']=="Delete")) {
    $favourite_tokens = explode("|",$_POST['favourite']);
    $favouritetitle = $favourite_tokens[0];
    $favourite_id = $favourite_tokens[1];
    
    //$favourites = (array)$_POST['favourite'];
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
        //foreach ($favourites as $favourite){
            //echo $favourite;
            $stmt->execute(array(
            ":favourite_id" => $favourite_id)
            );
            
        //}
        $stmt->closeCursor();
        
        //we are going to use prepared statements for clean coding and to defend against SQL injection
        
        $stmt2 = $db->prepare("delete from favourites where favourite_id = :favourite_id");  
                        
        //foreach ($favourites as $favourite){
            $stmt2->execute(array(
            ":favourite_id" => $favourite_id)
            );
        //}
        $stmt2->closeCursor();
        $title = "Favourite successfully deleted";
        $htmlBody.= <<<END
        <h3>Deleted Favourite</h3>
        <p>You have successfully deleted your favourite "$favouritetitle"</p>
        <a href=index.php>Search YouTube</a><br>
        <a href=searchfavourites.php> Search Favourites</a>
END;
        } 
        catch (PDOException $e) {
            printf("We have a problem: %s\n ", $e->getMessage());
        }
    } 
    
if ((isset($_POST['favourite'])) && ($_POST['btn_submit']=="Edit")) {
    $favourite_tokens = explode("|",$_POST['favourite']);
    $favouritetitle = $favourite_tokens[0];
    $favourite_id = $favourite_tokens[1];
    
    $title = "Edit Favourite";
    $htmlBody.= <<<END
    <h3>Edit Your YouTube Favourite</h3>
    <form action="updatefavourite.php" method="POST">
        <div class="form-group">
            <textarea cols="30" rows="3" class="form-control" name="edittedtitle">$favouritetitle </textarea>
            <input type="hidden" name="favourite_id" value="$favourite_id">
        </div>
        <div class="form-group" class="col-xs-4">
            <label for="newtags">New tag:</label>
            <input type="text" class="form-control" name="newtags" placeholder="Enter new tags, like: tag1, tag2">
        </div>
END;
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
            $htmlBody.= '<div class="form-group">';
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                //Let's see if this tag is already selected
                $query2 = "select * from favourite_tags where tagid_FK=:tagid and favouriteid_FK=:favourite_id";
                $stmt2 = $db->prepare($query2);
                $stmt2->execute(array(
                    ":tagid" => $row["tagid"],
                    ":favourite_id" => $favourite_id
                ));
                $checked = "";
                if ($stmt2->rowCount()>0) {
                    $checked = "checked";
                }
                //$checkbox = '<input type="checkbox" name="tag[]" id="tag" value="'. urldecode($row["tagid"]) .'">';
                $checkbox = sprintf('<input type="checkbox" name="tag[]" id="tag" value="%s" %s>',urldecode($row["tagid"]),$checked);
                $tagname = htmlentities($row["tag"]);
                $htmlBody.= sprintf('<label class="checkbox-inline">%s %s </label>',
                $checkbox,
                $tagname
                );             
            }
            $htmlBody.= <<<END
            </div>
            <button type="submit" class= "btn btn-primary" name="submit">Submit</button>
END;
        }
        
    } catch (PDOException $e) {
            printf("We have a problem: %s\n ", $e->getMessage());
    }
    
}


?>
<!doctype html>
<html>
  <head>
    <title><?php echo $title?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css" rel="stylesheet">
    <link href = "css/youtube_favourites.css" rel = "stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  
  </head>
  <body>
    <?php echo $navbar?>
    <?php echo $htmlBody?>
</body>
</html>