<?php 
$server='localhost';
$bd = 'sistema_biblioteca';
$user='bensalcos';
$pass='TwCfo184!';

$connect =@mysqli_connect($server,$user,$pass,$bd);

if (!$connect) {
    echo "Error: ".mysqli_connect_error()."------";
    exit();
}

//echo "<div class='alert alert-success'>Conexión exitosa!</div>";

?>