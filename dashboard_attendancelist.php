<?php
require_once ('database.php');
require_once ('meeting.php');
date_default_timezone_set('Asia/Jakarta');

class Agenda
{
	public $monthYearNow;

	public function koneksi(){
		$this->database = new Database();
		$this->database->connectToDatabase();
		$this->meeting = new Meetings();
	}

	/* Hitung jumlah event bulan saat ini */
	public function countAllEventThisMonth($monthYearNow){
		$this->koneksi();
		$query = "SELECT count(meeting_id) AS jumAgenda
				  FROM meetings 
				  WHERE substring(meeting_date,1,7) = '$monthYearNow'";
		$this->database->execute($query);
		return $this->database->result;
	}
	function displayCountAllEventThisMonth($monthYearNow){
			foreach($this->countAllEventThisMonth($monthYearNow) as $c){
			return $c['jumAgenda'];
		}
	}

	/* Mengambil seluruh nama event bulan sekarang */
	function getAllEventThisMonth($monthYearNow){
		$this->koneksi();
		$query = "SELECT meeting_name, meeting_date
				  FROM meetings
				  WHERE substring(meeting_date,1,7) = '$monthYearNow'";
		$this->database->execute($query);
		return $this->database->result;
	}
	function displayGetAllEventThisMonth($monthYearNow){
			foreach($this->getAllEventThisMonth($monthYearNow) as $c){
			return $this->database->result;
		}
	}

	/* Menghitung jumlah event per hari dalam bulan sekarang */
	function countEverydayEventThisMonth($monthYearNow, $date){
		$this->koneksi();
		$query = "SELECT COUNT(meeting_id) AS jumMeeting
				  FROM meetings
				  WHERE substring(meeting_date,1,7) = '$monthYearNow' AND substring(meeting_date,9,2)='$date'";
		$this->database->execute($query);
		return $this->database->result;
	}
	function displayCountEverydayEventThisMonth($monthYearNow, $date){
			foreach($this->countEverydayEventThisMonth($monthYearNow, $date) as $c){
				return $c['jumMeeting'];
			}
	}	

} //Class

?>

<!DOCTYPE html>
<html lang="en" >

<head>
  <meta charset="UTF-8">
  <title>CSS grid calendar</title>
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">

  
      <link rel="stylesheet" href="./style.css">

  
</head>

<body>

  
<div class="calendar-container">
  <div class="calendar-header">
   <h1>
       <a href="#"><< Before</a> November
      <button>▾</button><a href="#">Next >> </a>
    </h1>
    <p>2018</p>
  </div>
  <div class="calendar"><span class="day-name">Mon</span><span class="day-name">Tue</span><span class="day-name">Wed</span><span class="day-name">Thu</span><span class="day-name">Fri</span><span class="day-name">Sat</span><span class="day-name">Sun</span>
    <div class="day day--disabled">30</div>
    <div class="day day--disabled">31</div>
	<?php
	// buat objek baru
	$agenda = new Agenda();
	$countAgendaEveryday;
	$monthYearNow = $agenda->monthYearNow = Date("Y-m");
	$dateToday = Date("d");
	//$agenda->displayGetAllEventNameThisMonth($monthYearNow);
	$tgl = array(1,2,3,4,5,6,7,8,9,10,
				 11,12,13,14,15,16,17,18,19,20,
				 21,22,23,24,25,26,27,28,29,30,31);

	foreach ($tgl as $key => $arraryTanggal){
		$countAgendaEveryday = $agenda->displayCountEverydayEventThisMonth($monthYearNow, $arraryTanggal);
		echo "<div class='day'>";
		if($countAgendaEveryday<=0){
			echo $arraryTanggal;
		} else {
			echo $arraryTanggal;						
			foreach($agenda->displayGetAllEventThisMonth($monthYearNow) as $c){
				$ambilTanggal = substr($c['meeting_date'], 8, 3);
				if($arraryTanggal==$ambilTanggal){
					if($ambilTanggal==$dateToday){
						echo "<br><b><a href=#>o ".$c['meeting_name']."</a></b>";
					} else {
						echo "<br><a href=#>o ".$c['meeting_name']."</a>";					
					}
				}
			}		
		}
		echo "</div>";
	}
	?>
    <div class="day day--disabled">1</div>
    <div class="day day--disabled">2</div>
    </section>
  </div>
</div>
  
  

</body>

</html>