<?php
require_once("database.php");
class Machine
{
  function koneksi(){
    $this->database = new Database();
    $this->database->connectToDatabase();
  }
    function cetakAllMachineID(){
    $this->koneksi();
    $query = 'SELECT machine_id
          FROM fingerprint_machine';
    $this->database->execute($query);
    return $this->database->result;
  }
} //Class

  $machine = new Machine();
?>

<select id="mySelect" onchange="myFunction()">
  <option value="Audi">Audi</option>
  <option value="BMW">BMW</option>
  <option value="Mercedes">Mercedes</option>
  <option value="Volvo">Volvo</option>
</select>

<p>When you select a new car, a function is triggered which outputs the value of the selected car.</p>

<p id="demo"></p>

<script>
function myFunction() {
  var x = document.getElementById("mySelect").value;
  document.getElementById("demo").innerHTML = "You selected: " + x;
}
</script>
-->


<!DOCTYPE html>
<html>
<body>

<h2>Presensi Kehadiran</h2>


  Mesin presensi:
  <select id="mySelect" onchange="myFunction()">
  <option value='0'>----Pilih----</option>";
  foreach ($machine->cetakAllMachineID() as $c) {
    echo "<option value=".$c['machine_id'].">".$c['machine_id']."</option>";
  }
  echo "</select>";
?>

<p>Your enrollment ID: <input type="text" id="txt1" onkeyup="showHint(this.value)" placeholder="Name or IDs"></p>
Member ID: <span id="nameHint"></span><br>

<script>
function showHint(str) {
  var xhttp;
  if (str.length == 0) { 
    document.getElementById("nameHint").innerHTML = "";
    return;
  }
  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("nameHint").innerHTML = this.responseText;
    }
  };
  xhttp.open("GET", "attendance.php?q="+str, true);
  xhttp.send();   
}
</script>

</body>
</html>
