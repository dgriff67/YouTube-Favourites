<!doctype html>
<html>
  <head>
    <title>Search YouTube Favourites</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css" rel="stylesheet">
    <link href = "css/youtube_favourites.css" rel = "stylesheet">
  </head>
  <body>
    <h3>Search Your YouTube Favourites</h3>
    <form action="searchfavourites.php" method="POST">
        <div class="form-group">
            <label for="searchtitle">Search:</label>
            <input type="text" class="form-control" name="searchtitle" placeholder="Enter Search Term">
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
            echo "Sorry, no tags";
            exit;
        } else {
            printf('<form action="deletetag.php" method="POST">');
            printf('<div class="form-group">');
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                //We add a checkbox for deleting tags.
                $checkbox = '<input type="checkbox" name="tag[]" id="tag" value="'. urldecode($row["tagid"]) .'">';
                $tagname = htmlentities($row["tag"]);
                printf('<label class="checkbox-inline">%s %s </label>',
                $checkbox,
                $tagname
                );             
            }
            printf('</div>');
            printf('<button type="submit" class= "btn btn-primary" name="submit">Submit</button>');
            printf('</form>');
        }
    
    } catch (PDOException $e) {
            printf("We have a problem: %s\n ", $e->getMessage());
    }
    
    $query = "select * from favourites ";
    $orderbyclause = " order by title";
    if(!isset($_POST['submit'])) {
        echo "Please enter a search term or just hit 'Submit' to see all your favourites";
        exit;
    } elseif ((isset($_POST['submit'])) && ((!isset($_POST['searchtitle'])) || (empty($_POST['searchtitle'])))) {
        $query .= $orderbyclause;
    } elseif ((isset($_POST['searchtitle'])) && (!empty($_POST['searchtitle']))) {
        $whereclause = "where title like '%" . addslashes($_POST['searchtitle']) . "%'";
        $query .= $whereclause . $orderbyclause;
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
        $sth = $db->query($query);
        $favouritecount = $sth->rowCount();
        if ($favouritecount == 0) {
            echo "Sorry, no favourites matching your search term";
            exit;
        } else {
            //echo "We found " . $favouritecount . " favourites matching your search term!";
            printf('<form action="deletefavourite.php" method="POST">');
            printf('<div class="form-group">');
            printf('<ul class="list-group">');
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                //We want to look up all the associated tags and build a string of these tags
                $query2 = "select * FROM tags INNER JOIN favourite_tags ON tags.tagid = favourite_tags.tagid_FK ";
                $whereclause = "WHERE ((favourite_tags.favouriteid_FK)=" . htmlentities($row['favourite_id']) . ")";
                $query2 .= $whereclause;
                $stmt = $db->query($query2);
                $tag_string =  "";
                while ($row2 = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    ($tag_string !== "") && ($tag_string .= ", ");
                    $tag_string .= $row2['tag'];
                }
                //We add an anchor tag for playing the video
                $title = htmlentities($row["title"]);
                $checkbox = '<input type="checkbox" name="favourite" id="favourite" value="'. urldecode($row["favourite_id"]).'">';
                $playanchor = '<a href="http://www.youtube.com/watch?v='.urldecode($row["videoid"]). '" target=_blank> Play </a>';
                //We add a checkbox for deleting favourites.
                printf('<li class="list-group-item">%s %s %s %s</li>',
                $checkbox,
                $title,
                '<strong>' . $tag_string. '</strong>',
                $playanchor);             
            }
            printf('</ul>');
            printf('</div>');
            
        }
    } catch (PDOException $e) {
            printf("We have a problem: %s\n ", $e->getMessage());
    }
    printf('<button type="submit" class= "btn btn-danger" name="submit">Delete</button>');
   printf('</form>');
    ?>
  </body>
</html>