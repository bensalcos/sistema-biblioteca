<?php
session_start();

include("conexion.php");

if (empty($_SESSION)) {
    //2 rescatamos variables locales
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];

    //3 armamos la consulta
    $query = "SELECT id_usuario,nombre,apellido,rut,correo,clave,tipo_usuario,foto FROM usuarios WHERE correo = '" . $usuario . "'";

    //3 ejecutamos la consulta
    $resultado = mysqli_query($connect, $query);

    //4 verificamos las coincidencias
    if (mysqli_num_rows($resultado) == 0) {
        header('location: index.html');
    } else {
        $fila = mysqli_fetch_assoc($resultado);

        if (password_verify($clave, $fila['clave'])) {
            $_SESSION['id_usuario'] = $fila['id_usuario'];
            $_SESSION['usuario'] = $fila['nombre'] . " " . $fila['apellido'];
            $_SESSION['correo'] = $fila['correo'];
            $_SESSION['rut'] = $fila['rut'];
            $_SESSION['tipo_usuario'] = $fila['tipo_usuario'];
            $_SESSION['foto'] = $fila['foto'];
        } else {
            header('location: index.html');
        }
    }
} else
if (strtolower($_SESSION['tipo_usuario']) == 'administrativo') {
    header('location: usuarios.php');
} else {
    header('location: prestamos.php');
}







include("templates/header.php");
if (strtolower($_SESSION['tipo_usuario']) == 'administrativo') {
    include('templates/sidebar_administrativo.php');
} else {
    include('templates/sidebar_usuarios.php');
}

?>
















<?php
include('templates/footer.php');

?>
