<?php
include("includes/nav-menu.php");
//Heredoc
$htmlBody = <<<END
    <h3>Search YouTube</h3>
    <form method="GET">
        <div class="form-group" class="col-xs-4">
            <label for="q">Search YouTube:</label>
            <input type="text" class="form-control" id="q" name="q" placeholder="Enter Search Term">
        </div>
        <div class="form-group">
            <label for="maxResults">Max Results:</label>
            <input type="number" id="maxResults" name="maxResults" min="1" max="50" step="1" value="5">
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
END;

// This code will execute if the user entered a search query in the form
// and submitted the form. Otherwise, the page displays the form above.
if (isset($_GET['q']) && isset($_GET['maxResults'])) {
  // Call set_include_path() as needed to point to your client library.
//require_once '..\google\google-api-php-client-master\src\Google\Client.php';
//require_once '..\google\google-api-php-client-master\src\Google\Service.php';
require_once '.\vendor\autoload.php';

  /*
   * Set $DEVELOPER_KEY to the "API key" value from the "Access" tab of the
   * Google Developers Console <https://console.developers.google.com/>
   * Please ensure that you have enabled the YouTube Data API for your project.
   */
   $config = parse_ini_file("config.ini",true);
   $DEVELOPER_KEY = $config['GoogleDeveloperKey']['Developer_Key'];
                    

  $client = new Google_Client();
  $client->setDeveloperKey($DEVELOPER_KEY);

  // Define an object that will be used to make all API requests.
  $youtube = new Google_Service_YouTube($client);

  try {
    // Call the search.list method to retrieve results matching the specified
    // query term.
    $searchResponse = $youtube->search->listSearch('id,snippet', array(
      'q' => $_GET['q'],
      'maxResults' => $_GET['maxResults'],
    ));

    $videos = '';
    $channels = '';
    $playlists = '';
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
    $stmt = $db->prepare("select * from favourites where videoid like :videoid");           

    // Add each result to the appropriate list, and then display the lists of
    // matching videos. We are not interested in channels and plylists here 
    foreach ($searchResponse['items'] as $searchResult) {
      switch ($searchResult['id']['kind']) {
        case 'youtube#video':
            //Lets check yo see if this video is already a favourite
            $stmt->execute(array(
                        ":videoid" => "%".$searchResult['id']['videoId']."%"
                        ));
            $isafavourite = "";
            if ($stmt->rowCount()>0) {
                $isafavourite = "<strong>#favourite</strong>";
            }
            $videos .= sprintf(' <li class="list-group-item"><input type="radio" name="favourite[]" id="favourite" value="%s:%s"> %s %s (%s)</li>', $searchResult['snippet']['title'],  $searchResult['id']['videoId'], $searchResult['snippet']['title'], $isafavourite,    
            "<a href=http://www.youtube.com/watch?v=".$searchResult['id']['videoId']." target=_blank> Play</a>"); 
            break;  
      }
    }
    //More Heredoc
    $htmlBody .= <<<END
    <h3>Videos</h3>
    <form action="editfavourite.php" method = "POST">
        <div class="form-group">
            <ul class="list-group">
                $videos
            </ul>
        </div>
        <button type="submit" class="btn btn-primary" id="Add_Favourite" name="btn_submit">Add Favourite</button>
    </form>
END;
  } catch (Google_Service_Exception $e) {
    $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  } catch (Google_Exception $e) {
    $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  } catch (PDOException $e) {
            printf("We have a problem: %s\n ", $e->getMessage());
  }
}
?>

<!doctype html>
<html>
  <head>
    <title>YouTube Search</title>
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
   <script type="text/javascript">
 $(document).ready(function()) {
    $(document).on("click", "btn_submit", function() {
        var this_id = $(this).attr('id');
        ajaxCall(this_id);
    };
    
    function ajaxCall(this_id) {
        var data = 'id=' + this_id;

        $.ajax({
            url: 'editfavourite.php',  
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