<?php
session_start();
include("conexion.php");
include("templates/header.php");

$prestamos = '';

$query_prestamos = "SELECT id_prestamo, id_usuario, id_ejemplar, fecha_prestamo, fecha_devolucion, estado, renovaciones,  
(SELECT titulo FROM libros WHERE id_libro = (SELECT id_libro FROM ejemplares WHERE id_ejemplar = prestamos.id_ejemplar)) as  titulo,
    (SELECT codigo_ejemplar FROM ejemplares WHERE id_ejemplar = prestamos.id_ejemplar) as cod
    FROM prestamos WHERE id_usuario = " . $_SESSION['id_usuario'] . ";";


if ($resultado = mysqli_query($connect, $query_prestamos)) {
    actualizar_prestamo($connect);
    while ($fila = mysqli_fetch_array($resultado)) {

        $prestamos .= "<tr class='item'>";
        $prestamos .= "<td>" . $fila['titulo'] . "</td>";
        $prestamos .= "<td>" . $fila['cod'] . "</td>";
        $prestamos .= "<td>" . $fila['fecha_prestamo'] . "</td>";
        $prestamos .= "<td>" . $fila['fecha_devolucion'] . "</td>";
        $prestamos .= "<td>" . $fila['estado'] . "</td>";
        $prestamos .= "<td>" . $fila['renovaciones'] . "</td>";
        $prestamos .= "<td ><form method='POST' action='mis_prestamos.php'>
                    <input type='hidden' name='id_prestamo' value='" . $fila['id_prestamo'] . "'>
                    <button type='submit' class='btn btn-secondary'>Detalles</button>
                </form></td>";
        $prestamos .= "</tr>";
    }
} else {
    echo "Error: " . $query_prestamos . "<br>" . mysqli_error($connect);
}
$prestamos = mb_convert_encoding($prestamos, "UTF-8", "ISO-8859-1");



function actualizar_prestamo($connect)
{
    $fechaActual = date('Y-m-d');

    // Verificar y actualizar los préstamos que están atrasados
    $sql = "UPDATE prestamos 
        SET estado = 'atrasado' 
        WHERE fecha_devolucion <= '$fechaActual' 
        AND estado != 'atrasado'";

    if (mysqli_query($connect, $sql)) {
        $c = 0;
    } else {
        $c = 0;
    }
}


if (isset($_POST['id_prestamo']) && !isset($_POST['extension'])) {
    actualizar_prestamo($connect);
    $query_prestamo = "SELECT id_prestamo, id_usuario, id_ejemplar, fecha_prestamo, fecha_devolucion, estado, renovaciones, 
    (SELECT titulo FROM libros WHERE id_libro = (SELECT id_libro FROM ejemplares WHERE id_ejemplar = prestamos.id_ejemplar)) as  titulo,
    (SELECT codigo_ejemplar FROM ejemplares WHERE id_ejemplar = prestamos.id_ejemplar) as cod
    FROM prestamos WHERE id_prestamo = " . $_POST['id_prestamo'] . ";";
    if ($resultado = mysqli_query($connect, $query_prestamo)) {
        while ($fila = mysqli_fetch_array($resultado)) {

            $id_prestamo = $fila['id_prestamo'];
            $titulo = mb_convert_encoding($fila['titulo'], "UTF-8", "ISO-8859-1");
            $codigo_ejemplar = mb_convert_encoding($fila['cod'], "UTF-8", "ISO-8859-1");
            $fecha_prestamo = mb_convert_encoding($fila['fecha_prestamo'], "UTF-8", "ISO-8859-1");
            $fecha_devolucion = mb_convert_encoding($fila['fecha_devolucion'], "UTF-8", "ISO-8859-1");
            $estado = mb_convert_encoding($fila['estado'], "UTF-8", "ISO-8859-1");
            $renovaciones = $fila['renovaciones'];
        }
    }
} else {
    $id_prestamo = '';
    $titulo = '';
    $codigo_ejemplar = '';
    $fecha_prestamo = '';
    $fecha_devolucion = '';
    $estado = '';
}

function get_renovaciones($connect, $id_prestamo)
{
    $query = "SELECT renovaciones FROM prestamos WHERE id_prestamo = $id_prestamo;";
    $resultado = mysqli_query($connect, $query);
    $renovaciones = 0;
    if ($resultado) {
        $renovaciones = mysqli_fetch_assoc($resultado)['renovaciones'];
    }
    return $renovaciones;
}



function extension_unica($connect, $id_usuario)
{
    $query = "SELECT COUNT(*) as extensiones FROM prestamos WHERE id_usuario = $id_usuario AND estado = 'extendido';";
    $resultado = mysqli_query($connect, $query);
    return mysqli_fetch_assoc($resultado)['extensiones'];
}



if (isset($_POST['extension'])) {

    $fecha_devolucion = $_POST['fecha_devolucion'];
    $id_prestamo = $_POST["id_prestamo"];
    $renovaciones = get_renovaciones($connect, $id_prestamo);

    if (strtolower($_SESSION['tipo_usuario']) == 'alumno') {
        $fecha_devolucion_extendida = date('Y-m-d', strtotime($fecha_devolucion  . ' + 3 days'));
        if (extension_unica($connect, $_SESSION['id_usuario']) >= 1) {
            echo '<script>agregarAlerta("alert-warning", "Ya cuenta con una renovación en curso.")</script>';
        } else {
            if ($renovaciones > 1) {
                echo '<script>agregarAlerta("alert-warning", "Ya cuenta con 3 renovaciones, no puede volver a solicitar otra.")</script>';
            } else {
                $renovaciones += 1;
                $query = "UPDATE prestamos SET fecha_devolucion = '$fecha_devolucion_extendida', estado = 'extendido',renovaciones = $renovaciones  WHERE id_prestamo = $id_prestamo;";
                if (mysqli_query($connect, $query)) {
                    echo '<script>agregarAlerta("alert-success", "Plazo extendido con éxito")</script>';
                } else {
                    echo '<script>agregarAlerta("alert-warning", "No cumple con los requisitos para renovar el plazo")</script>';
                }
            }
        }
    }
    if (strtolower($_SESSION['tipo_usuario']) == 'docente') {
        $fecha_devolucion_extendida = date('Y-m-d', strtotime($fecha_devolucion  . ' + 7 days'));

        if ($renovaciones >= 3) {
            echo '<script>agregarAlerta("alert-warning", "Ya cuenta con 3 renovaciones de este ejemplar, no puede volver a solicitar otra.")</script>';
        } else {
            $renovaciones += 1;
            $query = "UPDATE prestamos SET fecha_devolucion = '$fecha_devolucion_extendida', estado = 'extendido',renovaciones = $renovaciones  WHERE id_prestamo = $id_prestamo;";
            if (mysqli_query($connect, $query)) {
                echo '<script>agregarAlerta("alert-success", "Plazo extendido con éxito")</script>';
            } else {
                echo '<script>agregarAlerta("alert-warning", "No cumple con los requisitos para renovar el plazo")</script>';
            }
        }
    }
}


if ($_SESSION['tipo_usuario'] == 'Administrativo') {
    include('templates/sidebar_administrativo.php');
} else {
    include('templates/sidebar_usuarios.php');
}

?>

<div class="mt-4 card">
    <form class="mt-5" action="" method="post" enctype="multipart/form-data" style="margin:auto; " ;>
        <div class="row">
            <div class="col-9">
                <div class="row">


                    <div class="col-8">
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Titulo:</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" readonly value="<?php echo ucfirst($titulo); ?>">
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="mb-3">
                            <label for="id_prestamo" class="form-label">ID Prestamo:</label>
                            <input type="text" class="form-control" id="id_prestamo" name="id_prestamo" readonly value="<?php echo $id_prestamo; ?>">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="codigo_ejemplar" class="form-label">Codigo del Ejemplar</label>
                            <input type="text" class="form-control" id="codigo_ejemplar" name="codigo_ejemplar" readonly value=" <?php echo $codigo_ejemplar; ?>">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="fecha_prestamo" class="form-label">Fecha de Prestamo</label>
                            <input type="text" class="form-control" id="fecha_prestamo" name="fecha_prestamo" readonly value="<?php echo $fecha_prestamo; ?>">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="fecha_devolucion" class="form-label">Fecha de Devolucion</label>
                            <input type="text" class="form-control" id="fecha_devolucion" name="fecha_devolucion" readonly value="<?php echo $fecha_devolucion; ?>">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <input type="text" class="form-control" id="stock" name="estado" readonly value="<?php echo ucfirst($estado); ?>">
                        </div>
                    </div>


                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-12">
                <button type="submit" class="btn btn-secondary" name="extension">Solicitar Extension de prestamo</button>
            </div>
        </div>
    </form>
</div>



<div class="mt-3 ms-2 me-2">
    <table class="table table-hover table-striped table-light sortable">
        <thead class="thead-dark">
            <tr>
                <th>Título</th>
                <th>Codigo de ejemplar</th>
                <th>Fecha de incio</th>
                <th>Fecha de Devolucion</th>
                <th>Estado</th>
                <th>Renovaciones</th>
                <th class="sorttable_nosort">Acciones</th>
            </tr>
        </thead>
        <tbody id="tabla-prestamos">
            <?php echo $prestamos ?>
        </tbody>
    </table>
</div>




<?php

include('templates/footer.php');

?>