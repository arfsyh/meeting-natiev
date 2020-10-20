<?php

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

function getUser($id){
  $this->koneksi();
            $query = "SELECT member_name, member_id, member_email
            FROM members 
            WHERE member_id= $id ";
            $this->database->execute($query);
            return $this->database->result;
}
}

?>