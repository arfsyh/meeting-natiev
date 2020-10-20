
<!DOCTYPE html>
<html>
<body>

<h2>Enroll new fingerprint ID</h2>
<p>Search by: <input type="text" id="txt1" onkeyup="showHint(this.value)" placeholder="Name or IDs"></p>
Member Name: <span id="nameHint"></span><br>

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
  xhttp.open("GET", "fingerprint.php?q="+str, true);
  xhttp.send();   
}
</script>

</body>
</html>
