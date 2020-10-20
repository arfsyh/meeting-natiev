<?php
require_once('database.php');
//require_once('member.php');
require __DIR__ . '/vendor/autoload.php';

if (php_sapi_name() != 'cli') {
    throw new Exception('This application must be run on the command line.');
}

class Tes
{
    function koneksi(){
      $this->database = new Database();
      $this->database->connectToDatabase();
    }
  
  //Mendapatkan seluruh data members
  //untuk keperluan secara umum
  function getAllEmail(){
    $this->koneksi();
    $query = "SELECT member_email
              FROM members";
    $this->database->execute($query);
    return $this->database->result; 
  }
}
/**
 * client secret: 8aruKPzrU5ewNDvr2Zxi5HVX
 * Returns an authorized API client.
 * return Google_Client the authorized client object
 */
function getClient()
{
    $client = new Google_Client();
    $client->setApplicationName('Google Calendar API PHP Quickstart');
    $client->setScopes(Google_Service_Calendar::CALENDAR);
    $client->setAuthConfig('credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Load previously authorized token from a file, if it exists.
    // The file token.json stores the user's access and refresh tokens, and is
    // created automatically when the authorization flow completes for the first
    // time.
    $tokenPath = 'token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // If there is no previous token or it's expired.
    if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }
    return $client;
}


// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Calendar($client);
$event = new Google_Service_Calendar_Event();

//$calendarId = 'primary';
//var_dump($event = $service->events->insert($calendarId, $event));
//$event = $service->events->insert($calendarId, $event);
//printf('Event created: %s\n', $event->htmlLink);


// First retrieve the event from the API.
$event = $service->events->get('primary', 'eventId');

$event->setSummary('coba');

$updatedEvent = $service->events->update('primary', $event->getId(), $event);

// Print the updated date.
echo $updatedEvent->getUpdated();