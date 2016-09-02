<!doctype html>
<html>
  <head>
    <title>Manage Your Tags</title>
    <link href = "css/youtube_favourites.css" rel = "stylesheet">
  </head>
  <body>
  <h3>Manage Your Favourites Tags</h3>
    <form action="addtag.php" method="POST">
    <table cellpadding="6">
        <tbody>
            <tr>
                <td>Tag:</td>
                <td><INPUT type="input" name="tag"></td>
            </tr>
            <td></td>
            <td><INPUT type="submit" name="add" value="Add"></td>
        </tbody>
    </table>
    </form>
   
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
            echo "Sorry, no favourites matching your search term";
            exit;
        } else {
            printf('<form action="deletetag.php" method="POST">');
            printf('<table cellpadding="6">');
            printf('<tbody>');
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                //We add a checkbox for deleting tags.
                $checkbox = '<input type="checkbox" name="tag[]" id="tag" value="'. urldecode($row["tagid"]) .'">';
                printf("<tr><td>%s</td><td>%s </td>",
                $checkbox,
                htmlentities($row["tag"])
                );             
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