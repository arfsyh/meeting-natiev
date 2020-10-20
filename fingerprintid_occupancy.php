<html>
	<title>Fingerprint ID Occupancy</title>
	<body>

	</body>
</html>

<?php
require_once ('database.php');
require_once ('fingerprint.php');

/**
 * Halaman dashboard atau landing page setelah login
 */
class Occupancy
{
	function koneksi(){
		$this->database = new Database();
		$this->database->connectToDatabase();
		$this->fingerprint = new Fingerprint();
	}
}//Class

$okupansi = new Occupancy();
$okupansi->createMatrixOccupancyID('FTI-TP-01');
echo "Tes";