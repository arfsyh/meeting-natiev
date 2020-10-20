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

function getEmailSelected($groupID){
  $this->koneksi();
  $query = "SELECT mb.member_name, mb.member_id, mb.member_email
            FROM meeting_groups mg JOIN members mb JOIN group_members gm
            ON mg.group_id=gm.group_id 
            AND mb.member_id=gm.member_id
            WHERE mg.group_id='$groupID'";
  $this->database->execute($query);
  return $this->database->result;	  
}

function createAttendanceLists($groupID, $email){
  $this->koneksi();
  foreach($this->getEmailSelected($groupID) as $x){
      foreach($this->meeting->getMaxRowsAndGroupIDOFMeetings($groupID) as $c){
          foreach($email as $e){
              if($e == $x['member_email']){
                $query = "INSERT INTO meeting_attendance (meeting_id, member_id, group_id, member_name)
                    VALUES ('$c[meeting_id]','$x[member_id]','$groupID','$x[member_name]')";
               
              }
          }
          
      }
  }
  $this->database->execute($query);	
}

function UpdateMeeting($groupID, $event_title, $meetingDate, $meetingStart, $meetingEnd, $event_place,$mi,$email){
  $query = "UPDATE meetings
            SET group_id='$groupID', meeting_name='$event_title', meeting_date='$meetingDate', meeting_start='$meetingStart', meeting_end='$meetingEnd', meeting_place='$event_place'
            WHERE meeting_id='$mi'";
  $this->database->execute($query);
  sleep(3);
  //$this->createAttendanceLists($groupID);
  $this->createAttendanceLists($groupID, $email);
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
function DeleteOldAtt($mi){
  $this->koneksi();
  $query = "DELETE FROM meeting_attendance
      WHERE meeting_id='$mi'";
      $this->database->execute($query);
      return $this->database->result;	
}
Function CreatNewAtt(){

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
$mi = $_POST['mi'];

$ini->DeleteOldAtt($mi);
$groupID = $_POST['event-group'];
  $event_title = $_POST['event-title'];
  $place = $_POST['event-place'];
  $other = $_POST['other'];
  if($place==""){
    $event_place=$other;
  }else{
    $event_place=$place;
  }
  $event_start_time = $_POST['event-start-time'];
  $event_end_time = $_POST['event-end-time'];
  $event_description = $_POST['event-description'];

  $pisah1  = explode('/',$event_start_time);
  $larik1 = array($pisah1[0],$pisah1[1],$pisah1[2]);
  $satukan1 = implode('-',$larik1);

  $pisah2 = explode('/',$event_end_time);
  $larik2 = array($pisah2[0],$pisah2[1],$pisah2[2]);
  $satukan2 = implode('-',$larik2);


  //mengubah date sesuai format GCalc API
  $eventStartTime = str_replace(" ", "T", $satukan1).":00+07:00";
  $eventEndTime = str_replace(" ", "T", $satukan2).":00+07:00";
  //mengubah jadi date saja
  $meetingDate = substr($event_start_time, 0, 10);
  //mengubah jadi time saja
  $meetingStart = substr($event_start_time, 11, 5).":00";
  $meetingEnd = substr($event_end_time, 11, 5).":00";
  $email = $_POST['member'];

while(true) {
  foreach ($events->getItems() as $event) {
    if( $event->getSummary() == $ini->tampil($mi)){
    
      $event = $service->events->get('primary', $event->getId());
      $attendee1 = new Google_Service_Calendar_EventAttendee();
      $attendee1->setResponseStatus('needsAction');
      foreach($email as $v){
      $attendee1->setEmail($v);}
      $attendees = array($attendee1);
      $event->setAttendees($attendees);

      $event->setSummary($event_title);
      $event->setLocation($event_place);

      $start = new Google_Service_Calendar_EventDateTime();
      $start->setDateTime($eventStartTime);  
      $event->setStart($start);
      $end = new Google_Service_Calendar_EventDateTime();
      $end->setDateTime($eventEndTime);  
      $event->setEnd($end);
      $updatedEvent = $service->events->update('primary', $event->getId(), $event);
      echo $updatedEvent->getUpdated();

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
$ini->UpdateMeeting($groupID, $event_title, $meetingDate, $meetingStart, $meetingEnd, $event_place,$mi,$email);
/*
$groupID = $_POST['event-group'];
  $event_title = $_POST['event-title'];
  $place = $_POST['event-place'];
  $other = $_POST['other'];
  if($place==""){
    $event_place=$other;
  }else{
    $event_place=$place;
  }
  $event_start_time = $_POST['event-start-time'];
  $event_end_time = $_POST['event-end-time'];
  $event_description = $_POST['event-description'];
  //mengubah date sesuai format GCalc API
  $eventStartTime = str_replace(" ", "T", $event_start_time).":00+07:00";
  $eventEndTime = str_replace(" ", "T", $event_end_time).":00+07:00";
  //mengubah jadi date saja
  $meetingDate = substr($event_start_time, 0, 10);
  //mengubah jadi time saja
  $meetingStart = substr($event_start_time, 11, 5).":00";
  $meetingEnd = substr($event_end_time, 11, 5).":00";
  $email = $_POST['member'];

// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Calendar($client);




$event = new Google_Service_Calendar_Event(array(
  'summary' => $event_title,
  'location' => $event_place,
  'description' => 'A chance to hear more about Google\'s developer products.',
  'start' => array(
    'dateTime' => '2020-08-31T09:00:00+07:00',
    'timeZone' => 'Asia/Jakarta',
  ),
  'end' => array(
    'dateTime' => '2020-08-31T17:00:00+07:00',
    'timeZone' => 'Asia/Jakarta',
  ),
  'recurrence' => array(
    'RRULE:FREQ=DAILY;COUNT=2'
  ),
  'attendees' => array(
    array('email' => 'arfyan.vira@gmail.com'),
  ),
  'reminders' => array(
    'useDefault' => FALSE,
    'overrides' => array(
      array('method' => 'email', 'minutes' => 24 * 60),
      array('method' => 'popup', 'minutes' => 10),
    ),
  ),
));

$calendarId = 'primary';
$event = $service->events->insert($calendarId, $event);
printf('Event created: %s\n', $event->htmlLink);*/
?>