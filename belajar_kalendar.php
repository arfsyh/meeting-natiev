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
	public function countAllEventThisMonth($yearNow, $monthNow){
		$this->koneksi();
		$query = "SELECT count(meeting_id) AS jumAgenda
				  FROM meetings 
				  WHERE substring(meeting_date,1,4) = '$yearNow'
				  		AND substring(meeting_date,6,2) = '$monthNow'";
		$this->database->execute($query);
		return $this->database->result;
	}
	function displayCountAllEventThisMonth($yearNow, $monthNow){
			foreach($this->countAllEventThisMonth($yearNow, $monthNow) as $c){
			return $c['jumAgenda'];
		}
	}

	/* Mengambil seluruh nama event bulan sekarang */
	function getAllEventThisMonth($yearNow, $monthNow){
		$this->koneksi();
		$query = "SELECT meeting_name, meeting_date, meeting_id
				  FROM meetings
				  WHERE substring(meeting_date,1,4) = '$yearNow'
				  		AND substring(meeting_date,6,2) = '$monthNow'";
		$this->database->execute($query);
		return $this->database->result;
	}
	function displayGetAllEventThisMonth($yearNow, $monthNow){
			foreach($this->getAllEventThisMonth($yearNow, $monthNow) as $c){
			return $this->database->result;
		}
	}

	/* Menghitung jumlah event per hari dalam bulan sekarang */
	function countEverydayEventThisMonth($yearNow, $monthNow, $date){
		$this->koneksi();
		$query = "SELECT COUNT(meeting_id) AS jumMeeting
				  FROM meetings
				  WHERE substring(meeting_date,1,4) = '$yearNow' 
				  		AND substring(meeting_date,6,2) = '$monthNow' 
				  		AND substring(meeting_date,9,2)='$date'";
		$this->database->execute($query);
		return $this->database->result;
	}
	function displayCountEverydayEventThisMonth($yearNow, $monthNow, $date){
			foreach($this->countEverydayEventThisMonth($yearNow, $monthNow, $date) as $c){
				return $c['jumMeeting'];
			}
	}	

} //Class

$yearNow = Date('Y');
@$month=$_GET['month'];
$dateToday = Date('d');
$agenda = new Agenda();
$countAgendaEveryday;
$monthYearNow = $agenda->monthYearNow = Date('Y-m');

$arraryBulan = array("01"=>"Januari", "02"=>"Februari", "03"=>"Maret", "04"=>"April", 
					 "05"=>"Mei", "06"=>"Juni", "07"=>"Juli", "08"=>"Agustus", 
					 "09"=>"September", "10"=>"Oktober", "11"=>"November", "12"=>"Desember");
$namaHari = array(1=>"Senin", 2=>"Selasa", 3=>"Rabu", 4=>"Kamis", 5=>"Jumat", 6=>"Sabtu", 7=>"Minggu");

/* Ambil nama bulan dengan diberi 2 digit bulan */
function displayMonthName($monthTwoDigitString, $arraryBulan){
	foreach ($arraryBulan as $key => $value) {
		if($monthTwoDigitString==$key){
			return $value;
		}
	}
}

/* Ambil jumlah hari dalam satu bulan dengan parameter 2 digit bulan dan 4 digit tahun*/
function numberOfDayInMonth($monthTwoDigitString, $yearNow){
	return $numberOfDayInMonth = cal_days_in_month(CAL_GREGORIAN, $monthTwoDigitString, $yearNow);
}

function getMonthFromInput($month){
	if(!($month < 13 and $month > 0)){
		return $monthNow = Date("m");  // Current month as default month
	} else {
		return $monthNow = $month;
	}
}
$monthNow = getMonthFromInput($month);

/* Ambil digit bulan kemarin */
function getLastMonth($monthNow){
	$lastMonth = $monthNow - 1;
	if($lastMonth==0){
		return $lastMonth = 12;
	} else {
		return $lastMonth;
	}
}
$getLastMonth = getLastMonth($monthNow);

/* Ambil digit bulan berikutnya */
function getNextMonth($monthNow){
	$nextMonth = $monthNow + 1;
	if($nextMonth==13){
		return $nextMonth = 1;
	} else {
		return $nextMonth;
	}
}
$getNextMonth = getNextMonth($monthNow);

/* Ambil digit tahun terakhir */
function getLastYearIn4Digits($yearNow){
	return $lastYear = $yearNow - 1;
}

$urlLastMonth = displayMonthName(getLastMonth($monthNow), $arraryBulan);
$urlMonthNow = displayMonthName($monthNow, $arraryBulan);
$urlNextMonth = displayMonthName(getNextMonth($monthNow), $arraryBulan);
if(strlen($getLastMonth)<2){
	$getLastMonth = '0'.$getLastMonth;
}
if(strlen($getNextMonth)<2){
	$getNextMonth = '0'.$getNextMonth;
}

function urlNextLastMonthNow($month, $getNextMonth, $getLastMonth){
	if($month==12 AND $getNextMonth==01){
		$month = $getNextMonth;
		echo "Tahun sebelum | akhir tahun";
	} else if ($month==01 AND $getLastMonth==12){
		echo "Tahun berikut | awal tahun";
		$month = $getLastMonth;
	} else {
		echo "Tahun berjalan";
	}

}
//var_dump($month, $getNextMonth, $getLastMonth);
urlNextLastMonthNow($month, $getNextMonth, $getLastMonth);

echo "<center><a href=belajar_kalendar.php?month=".$getLastMonth.">".$urlLastMonth."</a> << ";
echo "<b><a href=belajar_kalendar.php>".$urlMonthNow." ".$yearNow."</a></b> >> ";
echo "<a href=belajar_kalendar.php?month=".$getNextMonth.">".$urlNextMonth."</a></b></center>";

/* Ambil 4 digit tahun berikutnya 0000 */
function getNextYearIn4Digits($month, $yearNow){
	$yearNow = TRUE;
	if($month==01 AND is_bool($yearNow)){
		return $yearNow = Date('Y') + 1;
	}
}
$nextYear = getNextYearIn4Digits($month, $yearNow);
//echo $nextYear;

/* Menampilkan sisa tanggal bulan sebelumnya jika awal bulan bukan di hari senin */
function displayBlankFirstDayWeek($getLastMonth, $yearNow, $monthNow){
	$agenda = new Agenda();
	$numberOfDayInLastMonth = numberOfDayInMonth($getLastMonth, $yearNow);
	$startDayLastMonth = $numberOfDayInLastMonth - getFirstDayweekInMonday($monthNow, $yearNow) + 1;
	for($i=$startDayLastMonth;$i<=$numberOfDayInLastMonth;$i++){
		$countAgendaEveryday = $agenda->displayCountEverydayEventThisMonth($yearNow, $getLastMonth, $i);
		if($countAgendaEveryday<=0){
			echo "<td style='vertical-align:top' align='right'><font color='#778899'>$i</font></td>";
		} else {
			echo "<td style='vertical-align:top' align='right'><font color='#778899'>$i</font>";
			foreach($agenda->displayGetAllEventThisMonth($yearNow, $getLastMonth) as $c){
				$ambilTanggal = substr($c['meeting_date'], 8, 3);
				$meetingName10Digits = substr($c['meeting_name'], 0,15);
				if($i==$ambilTanggal){
						echo "<br><a href='event_view.php?id=".$c['meeting_id']."'><font color='#778899'>".$meetingName10Digits."</font></a>";		
				}
			}
			echo " </td>";			
		}
	}	
}

/* Ambil hari pertama di minggu pertama sebagai tanggal 1 di bulan ini 
*  0 = Minggu, 1 = Senin, 2 = Selasa, 3 = Rabu, 4 = Kamis, 5 = Jumat, 6 = Sabtu
*/
function getFirstDayweekInMonday($monthNow, $yearNow){
	$firstDayWeek = date('w',mktime(0,0,0,$monthNow,1,$yearNow));
	$firstDayWeek = $firstDayWeek - 1;  
	if($firstDayWeek < 0){
		$firstDayWeek = 6;
	} //jika hari Minggu
	return $firstDayWeek;
}
$firstDayWeek = getFirstDayweekInMonday($monthNow, $yearNow);
$numberOfDayInMonth = numberOfDayInMonth($monthNow, $yearNow);

function getNumberBlankLastDayWeek($firstDayWeek, $numberOfDayInMonth){
	$hari = $firstDayWeek + 1;
	$blankLast = array(28 => array(1=>0, 2=>6, 3=>5, 4=>4, 5=>3, 6=>2, 7=>1),
				   	   29 => array(1=>6, 2=>5, 3=>4, 4=>3, 5=>2, 6=>1, 7=>0),
				   	   30 => array(1=>5, 2=>4, 3=>3, 4=>2, 5=>1, 6=>0, 7=>6),
				   	   31 => array(1=>4, 2=>3, 3=>2, 4=>1, 5=>0, 6=>6, 7=>5),
				 );
	foreach ($blankLast as $tanggal => $sisaHari) {
		if($tanggal==$numberOfDayInMonth){
			foreach ($sisaHari as $key => $value) {
				if($key==$hari){
					return $value;					
				}
			}
		}
	}
}
$numberofBlankLastDayWeek = getNumberBlankLastDayWeek($firstDayWeek, $numberOfDayInMonth);

echo "<center><table border=1 width='1000'><tr><td align=center style='font-weight:bold'>SENIN</td><td align=center style='font-weight:bold'>SELASA</td><td align=center style='font-weight:bold'>RABU</td><td align=center style='font-weight:bold'>KAMIS</td><td align=center style='font-weight:bold'>JUMAT</td><td align=center style='font-weight:bold'>SABTU</td><td align=center style='font-weight:bold'>MINGGU</td></tr><tr style='height:100px' style='width:100px'>";
displayBlankFirstDayWeek($getLastMonth, $yearNow, $monthNow);

function displayBlankLastDayWeek($numberofBlankLastDayWeek, $yearNow, $getNextMonth){
	$agenda = new Agenda();
	for($i=1; $i<=$numberofBlankLastDayWeek; $i++){
		$i = '0'.$i;
		$countAgendaEveryday = $agenda->displayCountEverydayEventThisMonth($yearNow, $getNextMonth, $i);
		if($countAgendaEveryday<=0){
			echo "<td style='vertical-align:top' align='right'><font color='#778899'>$i</font></td>";
		} else {
			echo "<td style='vertical-align:top' align='right'><font color='#778899'>$i</font>";
			foreach($agenda->displayGetAllEventThisMonth($yearNow, $getNextMonth) as $c){
				$ambilTanggal = substr($c['meeting_date'], 8, 3);
				$meetingName10Digits = substr($c['meeting_name'], 0,15);
				if($i==$ambilTanggal){
						echo "<br><a href='event_view.php?id=".$c['meeting_id']."'><font color='#778899'>".$meetingName10Digits."</font></a>";		
				}
			}
			echo " </td>";			
		}
	}
}

$monthnow = Date('m');
for($i=1; $i<=$numberOfDayInMonth; $i++){
	if(strlen($i)<2){
		$i = '0'.$i; //
	}
	$countAgendaEveryday = $agenda->displayCountEverydayEventThisMonth($yearNow, $monthNow, $i);
	if($countAgendaEveryday<=0){
		if($i==$dateToday AND $monthNow==$monthnow){
			echo "<td style='vertical-align:top' align='right' bgcolor='yellow'><b>$i</b>";
			echo " </td>";			
		} else {
			echo "<td style='vertical-align:top' align='right'><b>$i</b>";
			echo " </td>";	
		}
	} else {
		if($i==$dateToday AND $monthNow==$monthnow){
			echo "<td style='vertical-align:top' align='right' bgcolor='yellow'><b>$i</b>";	
		} else {
			echo "<td style='vertical-align:top' align='right'><b>$i</b>";
		}
		foreach($agenda->displayGetAllEventThisMonth($yearNow, $monthNow) as $c){
			$ambilTanggal = substr($c['meeting_date'], 8, 3);
			$meetingName10Digits = substr($c['meeting_name'], 0,15);
			if($i==$ambilTanggal){
				if($ambilTanggal==$dateToday){
					echo "<br><b><a href='event_view.php?id=".$c['meeting_id']."'>".$meetingName10Digits."</a></b>";
				} else {
					echo "<br><a href='event_view.php?id=".$c['meeting_id']."'>".$meetingName10Digits."</a>";					
				}
			}
		}
		echo " </td>";	
	}
	$firstDayWeek++;
	if($firstDayWeek==7){
		echo "<tr style='height:100px' style='width:100px'>"; // start a new row
		$firstDayWeek=0;
	}
}
displayBlankLastDayWeek($numberofBlankLastDayWeek, $yearNow, $getNextMonth);   // Blank the balance cell of calendar at the end 
echo "</tr></table></center>";