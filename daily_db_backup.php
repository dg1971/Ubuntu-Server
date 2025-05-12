<?php

$host = 'localhost';
$hostname = 'sms.dats';
$username = 'dbbackup';
$password = '';

date_default_timezone_set('Asia/Colombo');
$day = date("dMY");
$folder = '/home/bkup/';
$tempfolder = "$folder/$day";

$databasesToSkip = array("sys", "information_schema", "performance_schema");

shell_exec("mkdir $tempfolder");

$mysqli = new mysqli($host, $username, $password);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$databases = $mysqli->query("SHOW DATABASES");
while ($row = $databases->fetch_assoc()) {
    $databaseName = $row['Database'];
    if (!in_array($databaseName, $databasesToSkip))
    {
     $outputFile = "$tempfolder/$databaseName.sql";
     $command = "mysqldump -h $host -u $username -p$password --add-drop-database --databases $databaseName > $outputFile";
     exec($command);

     echo "Database '$databaseName' backed up to '$outputFile' successfully.\n";
    }
}

$mysqli->close();

system("tar cvfz $folder/$hostname-$day-db.tar.gz $tempfolder/*");
shell_exec("rm -r $tempfolder");

?>
