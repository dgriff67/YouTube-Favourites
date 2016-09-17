<?php
include("includes/nav-menu.php");
$searchtitle = '';
$maxResults = 5;
if(isset($_POST['searchtitle'])) {
    $searchtitle = $_POST['searchtitle'].'"';
}
if(isset($_POST['maxResults'])) {
    $maxResults = $_POST['maxResults'].'"';
} 


$htmlBody = <<<END
<h3>Search Favourites</h3>
    <form action="searchfavourites.php" method="POST">
        <div class="form-group">
            <label for="searchtitle">Search Your Favourites:</label>
            <input type="text" class="form-control" name="searchtitle" value="$searchtitle" placeholder="Enter Search Term">
        </div>
        <div class="form-group">
            <label for="maxResults">Max Results:</label>
            <input type="number" id="maxResults" name="maxResults" min="1" max="50" step="1" value="$maxResults">
        </div>
        <div class="form-group">
            <label for="Sort">Sort option:</label>
            <select name="sortOption" class="form-control" id="Sort">
                <option value="N">Newest First</option>
                <option value="A">Alphabetical</option>
            </select>
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
        echo "Sorry, no tags";
        exit;
    } else {
$htmlBody.=<<<END
    <div class="form-group">
END;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            //Checkboxes for filtering favourites by tags.
            //We want to keep checkboxes checked after submit
            $checked = "";
            if(isset($_POST['tag'])) {
                $name = $_POST['tag'];
                foreach ($name as $tag){
                    if ($row["tagid"] == $tag) {
                        $checked = 'checked = "checked"';
                    }
                }
            }
            $checkbox = '<input type="checkbox" name="tag[]" id="tag" value="'. urldecode($row["tagid"]) .'"' . $checked .'>';
            $tagname = htmlentities($row["tag"]);
            $htmlBody.=sprintf('<label class="checkbox-inline">%s %s </label>', $checkbox,$tagname);             
        }
    } 
} catch (PDOException $e) {
        printf("We have a problem: %s\n ", $e->getMessage());
}
$htmlBody.=<<<END
    </div>
    <button type="submit" class= "btn btn-primary" name="submit">Submit</button>
    </form>
END;
$query = "SELECT DISTINCT favourite_id, title, videoid FROM favourites ". 
"LEFT JOIN favourite_tags ON favourites.favourite_id = favourite_tags.favouriteid_FK ";
$whereclause = "";
$orderbyclause = " order by favourite_id DESC";
$limitbyclause = " limit 5";

        
if(!isset($_POST['submit'])) {
    $query.=$orderbyclause . $limitbyclause;
} else {
    //echo $_POST['maxResults'];
    $limitbyclause = " limit ".$_POST['maxResults'];
    if ($_POST['sortOption']=="A") {
        $orderbyclause = "order by title";
    } 
    if ((!isset($_POST['searchtitle']) OR (empty($_POST['searchtitle']))) && ((!isset($_POST['tag'])) OR (empty($_POST['tag'])))) {
        //echo "no title no tags";
        $query .= $orderbyclause . $limitbyclause;
    } elseif ((isset($_POST['searchtitle']) && (!empty($_POST['searchtitle']))) && ((!isset($_POST['tag'])) OR (empty($_POST['tag'])))) {
        //echo "title is set no tags";
        $whereclause = "where title like '%" . addslashes($_POST['searchtitle']) . "%'";
        $query .= $whereclause . $orderbyclause . $limitbyclause;
    } elseif ((!isset($_POST['searchtitle']) OR (empty($_POST['searchtitle']))) && ((isset($_POST['tag'])) && (!empty($_POST['tag'])))) {
        //echo "tag is set";
        $name = $_POST['tag'];
        $whereclause = "where ";
        foreach ($name as $tag){
            ($whereclause !== "where ") && ($whereclause .= " OR ");
            $whereclause .= "((favourite_tags.tagid_FK)=".$tag.")";
        }
        $query .= $whereclause . $orderbyclause . $limitbyclause;
    } elseif ((isset($_POST['searchtitle']) && (!empty($_POST['searchtitle']))) && ((isset($_POST['tag'])) && (!empty($_POST['tag'])))) {
        //echo "title and tags are set";
        $name = $_POST['tag'];
        $whereclause = "where ";
        foreach ($name as $tag){
            ($whereclause !== "where ") && ($whereclause .= " OR ");
            $whereclause .= "((favourite_tags.tagid_FK)=".$tag.")";
        }
        $whereclause .= " AND title like '%" . addslashes($_POST['searchtitle']) . "%'";
        $query .= $whereclause . $orderbyclause . $limitbyclause; 
    }
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
        $htmlBody.= "<p>Sorry, no favourites matching your search term and/or with these tags<p>";
    } else {
$htmlBody.=<<<END
            <form action="processfavourite.php" method="POST">
            <div class="form-group">
            <ul class="list-group">
END;
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
            $favourite_id = urldecode($row["favourite_id"]);
            $radiobutton = sprintf('<input type="radio" name="favourite" id="favourite" value="%s¬%s">', $title, $favourite_id);
            $playanchor = '<a href="http://www.youtube.com/watch?v='.urldecode($row["videoid"]). '" target=_blank> Play </a>';
            //We add a radio button for deleting favourites.
            $htmlBody.=sprintf('<li class="list-group-item">%s %s %s %s</li>',
            $radiobutton,
            $title,
            '<strong>' . $tag_string. '</strong>',
            $playanchor);             
        }
$htmlBody.=<<<END
            </ul>
        </div>
        <button type="submit" class= "btn btn-primary" id="Edit" name="btn_submit" value="Edit">Edit</button>
        <button type="submit" class= "btn btn-danger" id="Delete" name="btn_submit" value="Delete">Delete</button>
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
  <script type="text/javascript">
 $(document).ready(function()) {
    $(document).on("click", "btn_submit", function() {
        var this_id = $(this).attr('id');
        ajaxCall(this_id);
    };
    
    function ajaxCall(this_id) {
        var data = 'id=' + this_id;

        $.ajax({
            url: 'processfavourite.php',  
            type: "POST",
            data: data,
            cache: false,
            success: function (html) {
                <!--DO WHAT EVER YOU WANT WITH THE RETURNED html-->
            })   
        };
    }
}
 </script>
</html>
