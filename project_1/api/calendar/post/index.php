<?php
require_once getcwd() . '/vendor/autoload.php';


define('APPLICATION_NAME', 'Google Calendar API PHP Quickstart');
define('CREDENTIALS_PATH', getcwd().'/../.credentials/calendar-php-quickstart.json');
define('CLIENT_SECRET_PATH', getcwd().'/../secret/client_secret.json');
// If modifying these scopes, delete your previously saved credentials
// at ~/.credentials/calendar-php-quickstart.json
define('SCOPES', implode(' ', array(
  Google_Service_Calendar::CALENDAR)
));

//// Begin test
//$json_string = json_encode($_POST);
//$file_handle = fopen(getcwd().'/test.txt', 'w');
//fwrite($file_handle, $json_string);
//fwrite($file_handle, "\n".$_POST["address"]);
//fclose($file_handle);
//// End test

//if (php_sapi_name() != 'cli') {
//  throw new Exception('This application must be run on the command line.');
//}

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
  $client = new Google_Client();
  $client->setApplicationName(APPLICATION_NAME);
  $client->setScopes(SCOPES);
  $client->setAuthConfig(CLIENT_SECRET_PATH);
  $client->setAccessType('offline');

  // Load previously authorized credentials from a file.
  $credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
  if (file_exists($credentialsPath)) {
    $accessToken = json_decode(file_get_contents($credentialsPath), true);
  } else {
    // Request authorization from the user.
    $authUrl = $client->createAuthUrl();
    printf("Open the following link in your browser:\n%s\n", $authUrl);
    print 'Enter verification code: ';
    $authCode = trim(fgets(STDIN));

    // Exchange authorization code for an access token.
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

    // Store the credentials to disk.
    if(!file_exists(dirname($credentialsPath))) {
      mkdir(dirname($credentialsPath), 0700, true);
    }
    file_put_contents($credentialsPath, json_encode($accessToken));
    printf("Credentials saved to %s\n", $credentialsPath);
  }
  $client->setAccessToken($accessToken);

  // Refresh the token if it's expired.
  if ($client->isAccessTokenExpired()) {
    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
  }
  return $client;
}

/**
 * Expands the home directory alias '~' to the full path.
 * @param string $path the path to expand.
 * @return string the expanded path.
 */
function expandHomeDirectory($path) {
  $homeDirectory = getenv('HOME');
  if (empty($homeDirectory)) {
    $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
  }
  return str_replace('~', realpath($homeDirectory), $path);
}

function main_application() {
  // Get the API client and construct the service object.
  $client = getClient();
  $service = new Google_Service_Calendar($client);

  // Print the next 10 events on the user's calendar.
  //$calendarId = 'primary';
  //$optParams = array(
  //  'maxResults' => 10,
  //  'orderBy' => 'startTime',
  //  'singleEvents' => TRUE,
  //  'timeMin' => date('c'),
  //);
  //$results = $service->events->listEvents($calendarId, $optParams);
  //
  //if (count($results->getItems()) == 0) {
  //  print "No upcoming events found.\n";
  //} else {
  //  print "Upcoming events:\n";
  //  foreach ($results->getItems() as $event) {
  //    $start = $event->start->dateTime;
  //    if (empty($start)) {
  //      $start = $event->start->date;
  //    }
  //    printf("%s (%s)\n", $event->getSummary(), $start);
  //  }
  //}


  // Refer to the PHP quickstart on how to setup the environment:
  // https://developers.google.com/google-apps/calendar/quickstart/php
  // Change the scope to Google_Service_Calendar::CALENDAR and delete any stored
  // credentials.

  // Setup heure/minute debut
  $heure_debut = $_POST["heure_debut"];
  $minute_debut = $_POST["minute_debut"];

  $temps_debut = $heure_debut.":".$minute_debut.":00";

  // Setup heure/minute fin
  $heure_debut_nb = (int)$heure_debut;
  $minute_debut_nb = (int)$minute_debut;
  $duree_nb = (int)$_POST["duree"];

  $minute_fin_nb = (int)(($minute_debut_nb + $duree_nb) % 60);
  $heure_fin_nb = (int)($heure_debut_nb + (($minute_debut_nb + $duree_nb) / 60));

  $heure_fin = "";
  $minute_fin = "";

  if ($heure_fin_nb > 10) {
    $heure_fin = (string)$heure_fin_nb;
  } else {
    $heure_fin = "0".$heure_fin_nb;
  }

  if ($minute_fin_nb > 10) {
    $minute_fin = (string)$minute_fin_nb;
  } else {
    $minute_fin = "0".$minute_fin_nb;
  }

  $temps_fin = $heure_fin.":".$minute_fin.":00";

//  // Begin test
//  $file_handle = fopen(getcwd().'/test3.txt', 'w');
//  fwrite($file_handle, $temps_fin);
//  //  fwrite($file_handle, "\n".$_POST["address"]);
//  fclose($file_handle);
//  // End test

  // Envoie du message à Google calendar
  $event = new Google_Service_Calendar_Event(array(
    'summary' => $_POST["summary"],
    'location' => $_POST["address"],
    'description' => $_POST["firstname"]." ".$_POST["lastname"]."\n".$_POST["description"]."\nEvent auto-generated.",
    'start' => array(
      'dateTime' => $_POST["date"].'T'.$temps_debut.'-04:00',
      'timeZone' => 'America/Montreal',
    ),
    'end' => array(
      'dateTime' => $_POST["date"].'T'.$temps_fin.'-04:00',
      'timeZone' => 'America/Montreal',
    ),
  //  'recurrence' => array(
  //    'RRULE:FREQ=DAILY;COUNT=2'
  //  ),
  //  'attendees' => array(
  //    array('email' => 'lpage@example.com'),
  //    array('email' => 'sbrin@example.com'),
  //  ),
  //  'reminders' => array(
  //    'useDefault' => FALSE,
  //    'overrides' => array(
  //      array('method' => 'email', 'minutes' => 24 * 60),
  //      array('method' => 'popup', 'minutes' => 10),
  //    ),
  //  ),
  ));

  $calendarId = 'primary';
  $event = $service->events->insert($calendarId, $event);
  //printf("Event created: %s\n", $event->htmlLink);

//  // Begin test
//  $file_handle = fopen(getcwd().'/test2.txt', 'w');
//  fwrite($file_handle, "Event created: ".$event->htmlLink);
//  fwrite($file_handle, $temps_debut);
//  fwrite($file_handle, $temps_fin);
//  fclose($file_handle);
//  // End test
}

main_application();
?>

<html>
 <head>
  <title>PHP Test</title>
 </head>
 <body>
   Empty file :-).
 </body>
</html>
