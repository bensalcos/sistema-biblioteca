<?php
session_start();
include("conexion.php");
include("templates/header.php");




$query = "SELECT id_prestamo, id_usuario, id_ejemplar, fecha_prestamo, fecha_devolucion, estado FROM prestamos WHERE estado = 'activo' ORDER BY fecha_prestamo DESC;";


$query_multas = "SELECT id_multa, id_prestamo, monto, fecha_multa, estado FROM multas WHERE estado = 'pendiente';";


$prestamos = '';
if ($resultado = mysqli_query($connect, $query)) {
    while ($fila = mysqli_fetch_array($resultado)) {
        $prestamos .= "<tr class='item'>";
        $prestamos .= "<td>" . $fila['id_prestamo'] . "</td>";
        $prestamos .= "<td>" . $fila['id_usuario'] . "</td>";
        $prestamos .= "<td>" . $fila['id_ejemplar'] . "</td>";
        $prestamos .= "<td>" . $fila['fecha_prestamo'] . "</td>";
        $prestamos .= "<td>" . $fila['fecha_devolucion'] . "</td>";
        $prestamos .= "<td>" . $fila['estado'] . "</td>";
        $prestamos .= "<td><a href='prestamos.php?id=" . $fila['id_prestamo'] . "'><i class='bi bi-gear text-danger'></i></a></td>";
        $prestamos .= "</tr>";
    }
} else {
    echo "Error: " . $query . "<br>" . mysqli_error($connect);
}
$prestamos = mb_convert_encoding($prestamos, "UTF-8", "ISO-8859-1");


$multas = '';

if ($resultado = mysqli_query($connect, $query_multas)) {
    while ($fila = mysqli_fetch_array($resultado)) {
        $multas .= "<tr class='item'>";
        $multas .= "<td>" . $fila['id_multa'] . "</td>";
        $multas .= "<td>" . $fila['id_prestamo'] . "</td>";
        $multas .= "<td>" . $fila['monto'] . "</td>";
        $multas .= "<td>" . $fila['fecha_multa'] . "</td>";
        $multas .= "<td>" . $fila['estado'] . "</td>";
        $multas .= "<td><a href='prestamos.php?id=" . $fila['id_multa'] . "'><i class='bi bi-gear text-danger'></i></a></td>";
        $multas .= "</tr>";
    }
} else {
    echo "Error: " . $query . "<br>" . mysqli_error($connect);
}

$multas = mb_convert_encoding($multas, "UTF-8", "ISO-8859-1");










if ($_SESSION['tipo_usuario'] == 'Administrativo') {
    include('templates/sidebar_administrativo.php');
} else {
    header('location: index.html');
}
?>




<div class="d-grid d-md-flex mt-2">
    <input type="submit" class="btn btn-secondary" id="limpiar" name="activos" value="Prestamos activos">
    <input type="submit" class="btn btn-success" id="crear" name="multas" value="Multas">
</div>


<div class="mt-4">
    <input type="text" class="input-buscar-usuarios form-control me-2" placeholder="Buscar Usuario" id="caja_busqueda">


    <div class="card" id="mostrar">


    </div>

</div>



<div class="mt-4 card">
    <form class="mt-5" action="" method="post" enctype="multipart/form-data" style="margin: auto; " ;>
        <div class="row">


            <div class="col-4">
                <img class="rounded mx-auto d-block" src='media/img/<?php echo $foto; ?>' width='100' height='100' class='rounded-circle me-2'>
                <div class="mb-3">
                    <label for="subir-foto" class="form-label">Subir foto</label>
                    <input class="form-control" type="file" id="subir-foto">
                </div>

            </div>



            <div class="col-8">
                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="id" class="form-label">ID:</label>
                            <input type="text" class="form-control" id="id" name="id" readonly value="<?php echo $id; ?>">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $nombre; ?>">
                        </div>
                    </div>


                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="apellido" class="form-label">Apellido:</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo $apellido; ?>">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="rut" class="form-label">Rut:</label>
                            <input type="text" class="form-control" id="rut" name="rut" value="<?php echo $rut; ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo:</label>
                                <input type="text" class="form-control" id="correo" name="correo" value="<?php echo $correo; ?>">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="tipo" class="form-label">Tipo de usuario:</label>
                                <input type="text" class="form-control" id="tipo" name="tipo" readonly value="<?php echo $tipo_usuario; ?>">
                            </div>
                        </div>



                    </div>

                </div>








            </div>

        </div>

        <div class="row mt-2 mb-5">
            <div class="col-12">

                <input type="submit" class="btn btn-info" id="limpiar" name="limpiar" value="Limpiar">
                <input type="submit" class="btn btn-success" id="crear" name="crear" value="Registrar">
                <input type="submit" class="btn btn-warning" id="modificar" name="modificar" value="Modificar">
                <input type="submit" class="btn btn-danger" id="eliminar" name="eliminar" value="Eliminar">
            </div>


        </div>


    </form>






</div>






<?php


include("templates/footer.php");

?>