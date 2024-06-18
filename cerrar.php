<?php
session_start();//iniciamos sesion o traspasamos el array de sesion
session_unset();//Eliminamos y liberamos el espacio utilizado por el array
session_destroy();//destruimos todos los datos asociados al arreglo de sesion
header("location:index.html");//redireccionamos a otro archivo
?>