<?php
session_start();
echo "Ambil token <br>";
//print_r($_SESSION);
echo "<a href=$_SESSION[authURL] targe=_blank>Klik Kanan Buka</a>"; ?>
<html>
<body>
<form action="tes_ambiltoken.php" method="POST">
<input type="text" name="authCode" size="80">
<input type="hidden" name="kode" value="1">
<input type="submit" value="Kirim">
</form>
</body>
</html>

         <?php   /*
            $authUrl = $client->createAuthUrl();
            printf("Buka tautan berikut:\n%s\n", "<a href=$authUrl>$authUrl</a>");
            print 'Paste ke: ';
            $authCode = trim(fgets(STDIN));
            //$authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            } */