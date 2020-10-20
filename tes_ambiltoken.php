<?php
session_start();
require_once('database.php');
require_once ('meeting.php');
require_once ('member.php');
date_default_timezone_set('Asia/Jakarta');
require __DIR__ . '/vendor/autoload.php';


/**
 * Membuat agenda baru
 * Jika token kadaluarsa, maka dialihkan ke tes_oauth.php
 * jika token masih aktif, maka langsung membuat agenda baru
 */
class Event
{
  public $groupID, $event_title, $event_place, $event_start_time,
         $event_end_time, $event_description;

  function koneksi(){
        $this->database = new Database();
        $this->database->connectToDatabase();
        $this->meeting = new Meetings();
        $this->member = new Member();
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

  function getAllEmailByGroup($groupID){
    $this->koneksi();   
    $arrEmail = array();
    foreach($this->meeting->getAllEmailByGroupMembers($groupID) as $c){
      $arrEmail[] = array('email' => $c['member_email']);
    }
    return $arrEmail;
  }

  function createMeeting($groupID, $meetingDate, $meetingEnd, $meetingStart, $meetingName, $meetingPlace){
    $this->koneksi();
    if($this->meeting->displayCountSameMeetingSchedule($groupID, $meetingDate, $meetingEnd, $meetingStart)==1){
      echo "Sudah ada meeting yang sama. \n";
    } else if ($this->meeting->displayJumOverlapMeetingByDate($groupID, $meetingDate)==0 OR $this->meeting->displayOverlapMeetingByTime($groupID, $meetingDate, $meetingStart)==0){
      //echo "Nol" dan "overlap = 0";
      //echo "\n";
      echo "Berhasil membuat event <b>$meetingName</b>";
      $this->meeting->createNewMeeting($groupID, $meetingName, $meetingDate, $meetingStart, $meetingEnd, $meetingPlace);
    } else {
      echo "Sori, ada tabrakan jadwal. \n";
    }
  }

  function getClient($authCode)
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

              // Exchange authorization code for an access token.
             
              $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
              $client->setAccessToken($accessToken);
              echo "Token berhasil<br>";

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

  $groupID = $_SESSION['event_group'];
  $event_title = $_SESSION['event_title'];
  $event_place = $_SESSION['event_place'];
  $event_start_time = $_SESSION['event_start_time'];
  $event_end_time = $_SESSION['event_end_time'];
  $event_description = $_SESSION['event_description'];
  //mengubah date sesuai format GCalc API
  $eventStartTime = str_replace(" ", "T", $event_start_time).":00+07:00";
  $eventEndTime = str_replace(" ", "T", $event_end_time).":00+07:00";
  //mengubah jadi date saja
  $meetingDate = substr($event_start_time, 0, 10);
  //mengubah jadi time saja
  $meetingStart = substr($event_start_time, 11, 5).":00";
  $meetingEnd = substr($event_end_time, 11, 5).":00";

  $tes = new Event();
  
  // Get the API client and construct the service object.
  $client = $tes->getClient($_POST['authCode']);
  $service = new Google_Service_Calendar($client);

  $data = array(
    'summary' => $event_title,
    'location' => $event_place,
    'sendNotifications' => TRUE,
    'sendUpdates' => TRUE,
    'description' => $event_description,
    'start' => array(
      'dateTime' => $eventStartTime,
      'timeZone' => 'Asia/Jakarta',
    ),
    'end' => array(
      'dateTime' => $eventEndTime,
      'timeZone' => 'Asia/Jakarta',
    ),
    'attendees' => $tes->getAllEmailByGroup($groupID),
    'reminders' => array(
      'useDefault' => FALSE,
      'overrides' => array(
        array('method' => 'email', 'minutes' => 24 * 60),
        array('method' => 'popup', 'minutes' => 10),
      ),
    ),
  );

$event = new Google_Service_Calendar_Event($data);
$calendarId = 'primary';

//masukkan ke kalendar Google
$event = $service->events->insert($calendarId, $event);
//Memasukkan ke database
$tes->createMeeting($groupID, $meetingDate, $meetingEnd, $meetingStart, $event_title, $event_place);

/*
class Tes
{
    

  function createMeeting($groupID, $meetingDate, $meetingEnd, $meetingStart, $meetingName, $meetingPlace){
    $this->koneksi();
    if($this->meeting->displayCountSameMeetingSchedule($groupID, $meetingDate, $meetingEnd, $meetingStart)==1){
      echo "Sudah ada meeting yang sama. \n";
    } else if ($this->meeting->displayJumOverlapMeetingByDate($groupID, $meetingDate)==0 OR $this->meeting->displayOverlapMeetingByTime($groupID, $meetingDate, $meetingStart)==0){
      //echo "Nol" dan "overlap = 0";
      //echo "\n";
      $meeting = new Tes();
      echo "Berhasil membuat event";
      $this->meeting->createNewMeeting($groupID, $meetingName, $meetingDate, $meetingStart, $meetingEnd, $meetingPlace);
    } else {
      echo "Sori, ada tabrakan jadwal. \n";
    }

  }

}
/**
 * client secret: 8aruKPzrU5ewNDvr2Zxi5HVX
 * Returns an authorized API client.
 * return Google_Client the authorized client object
 */

/*
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

            //pindahkan ke halaman ambil token sambil bawa session
            header("location:tes_oauth.php");
            $_SESSION['event_group'] = $_POST['event-group'];
            $_SESSION['event_title'] = $_POST['event-title'];
            $_SESSION['event_place'] = $_POST['event-place'];
            $_SESSION['event_start_time'] = $_POST['event-start-time'];
            $_SESSION['event_end_time'] = $_POST['event-end-time'];
            $_SESSION['event_description'] = $_POST['event-description'];
            
            // Request authorization from the user.
            //$authUrl = $client->createAuthUrl();
            $_SESSION['authURL'] = $client->createAuthUrl();
            /*
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN)); */

            // Exchange authorization code for an access token.
           /*
            $accessToken = $client->fetchAccessTokenWithAuthCode($_SESSION['authCode']);
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

//Menyiapkan koneksi ke database


// Refer to the PHP quickstart on how to setup the environment:
// https://developers.google.com/calendar/quickstart/php
// Change the scope to Google_Service_Calendar::CALENDAR and delete any stored
// credentials.

$email = new Tes();
$arrEmail = array();
foreach($email->getAllEmail() as $c){
  $arrEmail[] = array('email' => $c['member_email']);
}

$data = array(
  'summary' => $event_title,
  'location' => $event_place,
  'sendNotifications' => TRUE,
  'sendUpdates' => TRUE,
  'description' => $event_description,
  'start' => array(
    'dateTime' => $eventStartTime,
    'timeZone' => 'Asia/Jakarta',
  ),
  'end' => array(
    'dateTime' => $eventEndTime,
    'timeZone' => 'Asia/Jakarta',
  ),
  'attendees' => $arrEmail, //array(
    //'email' => $email,
    //'email' => array('ardiansyah@tif.uad.ac.id', 'ardiansyah2018@mail.ugm.ac.id')
    //array('email' => 'ardiansyah2018@mail.ugm.ac.id', 'email' => 'ardiansyah@tif.uad.ac.id')
    //array('email' => 'ardiansyah2018@mail.ugm.ac.id'),
    //array('email' => 'emailyas2013@gmail.com'),
    //array('email' => 'sriningsih2013@gmail.com'),
    //var_dump($gabung)
  //),
  'reminders' => array(
    'useDefault' => FALSE,
    'overrides' => array(
      array('method' => 'email', 'minutes' => 24 * 60),
      array('method' => 'popup', 'minutes' => 10),
    ),
  ),
);

      $event = new Google_Service_Calendar_Event($data);
      $calendarId = 'primary';
      //var_dump($event = $service->events->insert($calendarId, $event));
      $event = $service->events->insert($calendarId, $event);
      $meeting->createMeeting($groupID, $meetingDate, $meetingEnd, $meetingStart, $event_title, $event_place);
      //printf('Event created: %s\n', $event->htmlLink);
*/

