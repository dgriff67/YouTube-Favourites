<!doctype html>
<html>
  <head>
    <title>Search YouTube Favourites</title>
    <link href = "css/youtube_favourites.css" rel = "stylesheet">
  </head>
  <body>
    <h3>Search Your YouTube Favourites</h3>
    <form action="searchfavourites.php" method="POST">
    <table cellpadding="6">
        <tbody>
            <tr>
                <td>Title:</td>
                <td><INPUT type="input" name="searchtitle"></td>
            </tr>
            <td></td>
            <td><INPUT type="submit" name="submit" value="Submit"></td>
        </tbody>
    </table>
    </form>
    <?php
    $query = "select * from favourites ";
    if(!isset($_POST['submit'])) {
        echo "Please enter a search term or just hit 'Submit' to see all your favourites";
        exit;
    } elseif ((isset($_POST['searchtitle'])) && (!empty($_POST['searchtitle']))) {
        $whereclause = "where title like '%" . addslashes($_POST['searchtitle']) . "%'";
        $query = $query . $whereclause;
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
            printf('<table cellpadding="6">');
            printf('<tbody>');
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                //We add an anchor tag for playing the video
                $playanchor = '<a href="http://www.youtube.com/watch?v='
                .urldecode($row["videoid"]). '" target=_blank> Play </a>';
                //We add a checkbox for deleting favourites.
                $checkbox = '<input type="checkbox" name="favourite[]" id="favourite" value="'. urldecode($row["videoid"]) .'">';
                printf("<tr><td>%s</td><td>%s </td><td>%s</td>",
                $checkbox,
                htmlentities($row["title"]),
                $playanchor);             
            }
        }
    } catch (PDOException $e) {
            printf("We have a problem: %s\n ", $e->getMessage());
    }
    printf('<tr><td></td><td><INPUT type="submit" name="delete" value="Delete"></td></tr>');
    printf('</tbody>');
    printf('</table>');
    
    ?>
  </body>
</html>