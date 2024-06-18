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
            $_SESSION['tipo_usuario'] = $fila['tipo_usuario'];
            $_SESSION['foto'] = $fila['foto'];
        } else {
            header('location: index.html');
        }
    }
}

$usuarios = "";
$query2 = "SELECT id_usuario,nombre,apellido,rut,correo,tipo_usuario,clave,foto FROM usuarios";
$resultado2 = mysqli_query($connect, $query2);
while ($fila = mysqli_fetch_array($resultado2)) {
    $usuarios .= "<tr>";
    $usuarios .= "<td><img height='40px' width='auto' src='media/img/" . $fila[7] . "' alt='imagen del usuario'></td>";
    $usuarios .= "<td>" . $fila[1] . "</td>";
    $usuarios .= "<td>" . $fila[2] . "</td>";
    $usuarios .= "<td>" . $fila[3] . "</td>";
    $usuarios .= "<td>" . $fila[4] . "</td>";
    $usuarios .= "<td>" . $fila[5] . "</td>";
    $usuarios .= "<td>  
                    <a href='usuario.php?id=$fila[0]'><i class='bi bi-gear text-danger'></i></a>
                </td>";
    $usuarios .= "</tr>";
}






include("templates/header.php");
if (strtolower($_SESSION['tipo_usuario']) == 'administrativo') {
    include('templates/sidebar_administrativo.php');
} else {
    include('templates/sidebar_usuarios.php');
}

?>


<div>
    <h3>Listado de Usuarios</h3>
    <table class="table table-hover table-striped table-dark sortable">
        <thead>
            <tr>
                <th>Fotografía</th>                
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Rut</th>
                <th>Correo</th>
                <th>Perfil</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>

            <?php

            echo mb_convert_encoding($usuarios, "UTF-8", "ISO-8859-1");
            ?>



        </tbody>
    </table>











</div>


<?php
include('templates/footer.php');

?>
