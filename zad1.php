<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "baza";

$conn = new mysqli($servername, $username, $password);
// Check connection
if ($conn->connect_error) {
  die("Ne možemo se spojiti na bazu" . $conn->connect_error);
}

// Stvaranje baze 
$sql = "CREATE DATABASE baza";
if ($conn->query($sql) === TRUE) {
  echo "Database created successfully";
} else {
  echo "Error creating database: " . $conn->error;
  exit();
}

$conn->close();

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Ne možemo se spojiti na bazu" . $conn->connect_error);
}

// Kreiranje tablice
$sql = "CREATE TABLE Zaposlenici (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
ime VARCHAR(30) NOT NULL,
prezime VARCHAR(30) NOT NULL,
email VARCHAR(50)
)";

if ($conn->query($sql) === TRUE) {
  echo "Table Zaposlenici created successfully";
} else {
  echo "Error creating table: " . $conn->error;
  exit();
}

////Dodavanje zaposlenika u tablicu
$sql = "INSERT INTO Zaposlenici (ime, prezime, email)
VALUES ('Josip', 'Josipović', 'josip@123jj.com')";
if ($conn->query($sql) === TRUE) {
    echo "Uspjesno dodano u bazu Zaposlenici";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$sql = "INSERT INTO Zaposlenici (ime, prezime, email)
VALUES ('Ivan', 'Ivić', 'ivic.ivan@gmail.com')";
if ($conn->query($sql) === TRUE) {
    echo "Uspjesno dodano u bazu Zaposlenici";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$sql = "INSERT INTO Zaposlenici (ime, prezime, email)
VALUES ('Pero', 'Perić', 'pero.p@gmail.com')";
if ($conn->query($sql) === TRUE) {
    echo "Uspjesno dodano u bazu Zaposlenici";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

///backup
$conn->close();

$dir = "C:/xampp/htdocs/LV2/backup/$dbname";

if (!is_dir($dir)) {
    if (!@mkdir($dir)) {
        die("<p>Ne možemo stvoriti direktorij $dir.</p></body></html>");
    }
}

$time = time();
$values = array();

$dbc = @mysqli_connect('localhost', 'root', '', $dbname) OR die("<p>Ne možemo se spojiti na bazu $dbname.</p></body></html>");
$r = mysqli_query($dbc, 'SHOW TABLES');

if (mysqli_num_rows($r) > 0) {
    echo "<p>Backup za bazu podataka '$dbname'.</p>";
    while (list($table) = mysqli_fetch_array($r, MYSQLI_NUM)) {
        $q = "SELECT * FROM $table";
        $r2 = mysqli_query($dbc, $q);
        if (mysqli_num_rows($r2) > 0) {
            if ($fp = gzopen ("$dir/{$table}_{$time}.sql.gz", 'w9')) {
                while ($row = mysqli_fetch_array($r2, MYSQLI_NUM)) {
                    foreach ($row as $value) {
                        $values[] = addslashes($value);
                    }
                    $format = "INSERT INTO {$dbname} (id, ime, prezime, email)
                    VALUES ('{$values[0]}', '{$values[1]}', '{$values[2]}', '{$values[3]}');\n";
                    unset($values);
                    gzwrite ($fp, $format); 
                } 
                gzclose ($fp); 
                echo "<p>Tablica '$table' je spremljena.</p>";
            } else { 
                echo "<p>Datoteka $dir/{$table}_{$time}.sql.gz se ne može otvoriti.</p>";
                break; 
            } 
        } 
    } 

} else {
    echo "<p>Baza $dbname nema tablice.</p>";
}

?>