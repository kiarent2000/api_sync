<?
$conn = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_USERNAME);
// Check connection
if($conn === false){
	echo "Нет соединения";
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
mysqli_set_charset($conn , 'utf8' );
//echo "Соединение установлено";
$start_date = date('Y-m-d H:i:s');
$start = microtime(true); //начало измерения