<?php
session_start();
require_once ('meeting.php');
require_once ('member.php');
date_default_timezone_set('Asia/Jakarta');
require __DIR__ . '/vendor/autoload.php';




class tes{
  public $groupID, $event_title, $event_place, $event_start_time,
  $event_end_time, $event_description;

function koneksi(){
 $this->database = new Database();
 $this->database->connectToDatabase();
 $this->meeting = new Meetings();
 $this->member = new Member();
}

function delMett($mi){
    $this->koneksi();
    $query = "DELETE
        FROM meetings
        WHERE meeting_id=$mi";
      $this->database->execute($query);
      return $this->database->result;

}
function DeleteOldAtt($mi){
    $this->koneksi();
    $query = "DELETE FROM meeting_attendance
        WHERE meeting_id='$mi'";
        $this->database->execute($query);
        return $this->database->result;	
  }

function getMeeting($mi){
    $this->koneksi();
    $query = "SELECT meeting_name
        FROM meetings
        WHERE meeting_id=$mi";
      $this->database->execute($query);
      return $this->database->result;	
  }
  
  function tampil($mi){
    $this->koneksi();
    foreach($this->getMeeting($mi) as $a){
      $summarry = $a['meeting_name'];
    }
    return $summarry;
  
  }
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
}

$ini = new tes();

$client = $ini->getClient();
$service = new Google_Service_Calendar($client);

$events = $service->events->listEvents('primary');
$mi = $_GET['id'];


while(true) {
    foreach ($events->getItems() as $event) {
      if( $event->getSummary() == $ini->tampil($mi)){
        $event = $service->events->get('primary', $event->getId());

        $service->events->delete('primary', $event->getId());
  
    }
    }
    $pageToken = $events->getNextPageToken();
    if ($pageToken) {
      $optParams = array('pageToken' => $pageToken);
      $events = $service->events->listEvents('primary', $optParams);
    } else {
      break;
    }
  }

  $ini->DeleteOldAtt($mi);
  $ini->delMett($mi);