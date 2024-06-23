<?php
session_start();
include("conexion.php");
include("templates/header.php");

if (empty($_SESSION)) {
    header('location: index.html');
}

$prestamos = '';

function actualizar_prestamo($connect)
{
    $fechaActual = date('Y-m-d');

    // Verificar y actualizar los préstamos que están atrasados
    $sql = "UPDATE prestamos 
        SET estado = 'atrasado' 
        WHERE fecha_devolucion < '$fechaActual' 
        AND estado != 'atrasado'";

    if (mysqli_query($connect, $sql)) {
        $c = 0;
    } else {
        $c = 0;
    }
}
if (isset($_POST['activos']) || $_POST == array()) {

    actualizar_prestamo($connect);
    $query = "SELECT id_prestamo, id_usuario, id_ejemplar, fecha_prestamo, fecha_devolucion, estado, renovaciones FROM prestamos  ORDER BY fecha_prestamo DESC;";
    $prestamos = '';


    if ($resultado = mysqli_query($connect, $query)) {
        while ($fila = mysqli_fetch_array($resultado)) {
            $id_libro = get_id_libro($fila['id_ejemplar']);
            $query_libro = "SELECT * FROM libros WHERE id_libro = " . $id_libro . ";";
            $resultado_libro = mysqli_query($connect, $query_libro);
            $fila_libro = mysqli_fetch_assoc($resultado_libro);
            $query_usuario = "SELECT * FROM usuarios WHERE id_usuario = " . $fila['id_usuario'] . ";";
            $resultado_usuario = mysqli_query($connect, $query_usuario);
            $fila_usuario = mysqli_fetch_assoc($resultado_usuario);

            $prestamos .= "<tr class='item'>";
            $prestamos .= "<td>" . $fila['id_prestamo'] . "</td>";
            $prestamos .= "<td>" . ucfirst($fila_usuario['nombre']) . ' ' . $fila_usuario['apellido']  . "</td>";
            $prestamos .= "<td>" . $fila_usuario['rut'] . "</td>";
            $prestamos .= "<td>" . ucfirst($fila_usuario['tipo_usuario']) . "</td>";
            $prestamos .= "<td>" . $fila_usuario['correo']. "</td>";
            $prestamos .= "<td>" . $fila_libro['titulo'] . "</td>";
            $prestamos .= "<td>" . $fila['fecha_prestamo'] . "</td>";
            $prestamos .= "<td>" . $fila['fecha_devolucion'] . "</td>";
            $prestamos .= "<td>" . $fila['estado'] . "</td>";
            $prestamos .= "<td>" . $fila['renovaciones'] . "</td>";
            $prestamos .= "</tr>";
        }
        $prestamos = mb_convert_encoding($prestamos, "UTF-8", "ISO-8859-1");
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
}


if (isset($_POST['pendientes'])) {

    $query = "SELECT id_prestamo, id_usuario, id_ejemplar, fecha_prestamo, fecha_devolucion, estado, renovaciones FROM prestamos  ORDER BY fecha_prestamo DESC;";
    $prestamos = '';
    actualizar_prestamo($connect);

    if ($resultado = mysqli_query($connect, $query)) {
        while ($fila = mysqli_fetch_array($resultado)) {
            $id_libro = get_id_libro($fila['id_ejemplar']);
            $query_libro = "SELECT * FROM libros WHERE id_libro = " . $id_libro . ";";
            $resultado_libro = mysqli_query($connect, $query_libro);
            $fila_libro = mysqli_fetch_assoc($resultado_libro);
            $query_usuario = "SELECT * FROM usuarios WHERE id_usuario = " . $fila['id_usuario'] . ";";
            $resultado_usuario = mysqli_query($connect, $query_usuario);
            $fila_usuario = mysqli_fetch_assoc($resultado_usuario);

            if ($fila['estado'] == 'pendiente' || $fila['estado'] == 'atrasado') {
                $prestamos .= "<tr class='item'>";
                $prestamos .= "<td>" . $fila['id_prestamo'] . "</td>";
                $prestamos .= "<td>" . ucfirst($fila_usuario['nombre']) . ' ' . $fila_usuario['apellido']  . "</td>";
                $prestamos .= "<td>" . $fila_usuario['rut'] . "</td>";
                $prestamos .= "<td>" . ucfirst($fila_usuario['tipo_usuario']) . "</td>";
                $prestamos .= "<td>" . $fila_usuario['correo']. "</td>";
                $prestamos .= "<td>" . $fila_libro['titulo'] . "</td>";
                $prestamos .= "<td>" . $fila['fecha_prestamo'] . "</td>";
                $prestamos .= "<td>" . $fila['fecha_devolucion'] . "</td>";
                $prestamos .= "<td>" . $fila['estado'] . "</td>";
                $prestamos .= "<td>" . $fila['renovaciones'] . "</td>";

                $prestamos .= "</tr>";
            }
        }
        $prestamos = mb_convert_encoding($prestamos, "UTF-8", "ISO-8859-1");
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
}

function get_id_libro($id_ejemplar)
{
    include('conexion.php');
    $query = "SELECT id_libro FROM ejemplares WHERE id_ejemplar = $id_ejemplar;";
    $resultado = mysqli_query($connect, $query);
    $fila = mysqli_fetch_array($resultado);
    return $fila['id_libro'];
}
if (isset($_POST['multas'])) {

    $query = "SELECT id_prestamo, id_usuario, id_ejemplar, fecha_prestamo, fecha_devolucion, estado, renovaciones FROM prestamos  ORDER BY fecha_prestamo DESC;";
    $prestamos = '';
    actualizar_prestamo($connect);

    if ($resultado = mysqli_query($connect, $query)) {
        while ($fila = mysqli_fetch_array($resultado)) {

            $id_libro = get_id_libro($fila['id_ejemplar']);
            $query_libro = "SELECT * FROM libros WHERE id_libro = " . $id_libro . ";";
            $resultado_libro = mysqli_query($connect, $query_libro);
            $fila_libro = mysqli_fetch_assoc($resultado_libro);
            $query_usuario = "SELECT * FROM usuarios WHERE id_usuario = " . $fila['id_usuario'] . ";";
            $resultado_usuario = mysqli_query($connect, $query_usuario);
            $fila_usuario = mysqli_fetch_assoc($resultado_usuario);
            $fecha_actual = date('Y-m-d');
            $fecha_devolucion = $fila['fecha_devolucion'];
            $dias_atraso = strtotime($fecha_actual) - strtotime($fecha_devolucion);
            $dias_atraso = ($dias_atraso / 86400);
            $multa = $dias_atraso * 1000;

            if ($fila['estado'] == 'atrasado' || $fila['estado'] == 'pendiente') {
                $prestamos .= "<tr class='item'>";
                $prestamos .= "<td>" . $fila['id_prestamo'] . "</td>";
                $prestamos .= "<td>" . ucfirst($fila_usuario['nombre'] . ' ' . $fila_usuario['apellido'])  . "</td>";
                $prestamos .= "<td>" . $fila_usuario['rut'] . "</td>";
                $prestamos .= "<td>" . ucfirst($fila_usuario['tipo_usuario']) . "</td>";
                $prestamos .= "<td>" . $fila_usuario['correo']. "</td>";
                $prestamos .= "<td>" . $fila_libro['titulo'] . "</td>";
                $prestamos .= "<td>" . $fila['fecha_prestamo'] . "</td>";
                $prestamos .= "<td>" . $fila['fecha_devolucion'] . "</td>";
                $prestamos .= "<td>" . $fila['estado'] . "</td>";
                $prestamos .= "<td>" . '$'. $multa . "</td>";

                $prestamos .= "</tr>";
            }
        }
        $prestamos = mb_convert_encoding($prestamos, "UTF-8", "ISO-8859-1");
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
}
if (isset($_POST['docentes'])) {
    $query = "SELECT id_prestamo, id_usuario, id_ejemplar, fecha_prestamo, fecha_devolucion, estado, renovaciones FROM prestamos  ORDER BY fecha_prestamo DESC;";
    $prestamos = '';
    actualizar_prestamo($connect);

    if ($resultado = mysqli_query($connect, $query)) {
        while ($fila = mysqli_fetch_array($resultado)) {
            $id_libro = get_id_libro($fila['id_ejemplar']);
            $query_libro = "SELECT * FROM libros WHERE id_libro = " . $id_libro . ";";
            $resultado_libro = mysqli_query($connect, $query_libro);
            $fila_libro = mysqli_fetch_assoc($resultado_libro);
            $query_usuario = "SELECT * FROM usuarios WHERE id_usuario = " . $fila['id_usuario'] . ";";
            $resultado_usuario = mysqli_query($connect, $query_usuario);
            $fila_usuario = mysqli_fetch_assoc($resultado_usuario);

            if (strtolower($fila_usuario['tipo_usuario']) == 'docente') {
                $prestamos .= "<tr class='item'>";
                $prestamos .= "<td>" . $fila['id_prestamo'] . "</td>";
                $prestamos .= "<td>" . ucfirst($fila_usuario['nombre']) . ' ' . $fila_usuario['apellido']  . "</td>";
                $prestamos .= "<td>" . $fila_usuario['rut'] . "</td>";
                $prestamos .= "<td>" . ucfirst($fila_usuario['tipo_usuario']) . "</td>";
                $prestamos .= "<td>" . $fila_usuario['correo']. "</td>";
                $prestamos .= "<td>" . $fila_libro['titulo'] . "</td>";
                $prestamos .= "<td>" . $fila['fecha_prestamo'] . "</td>";
                $prestamos .= "<td>" . $fila['fecha_devolucion'] . "</td>";
                $prestamos .= "<td>" . $fila['estado'] . "</td>";
                $prestamos .= "<td>" . $fila['renovaciones'] . "</td>";

                $prestamos .= "</tr>";
            }
        }
        $prestamos = mb_convert_encoding($prestamos, "UTF-8", "ISO-8859-1");
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
}
if (isset($_POST['alumnos'])) {
    $query = "SELECT id_prestamo, id_usuario, id_ejemplar, fecha_prestamo, fecha_devolucion, estado, renovaciones FROM prestamos  ORDER BY fecha_prestamo DESC;";
    $prestamos = '';
    actualizar_prestamo($connect);

    if ($resultado = mysqli_query($connect, $query)) {
        while ($fila = mysqli_fetch_array($resultado)) {
            $id_libro = get_id_libro($fila['id_ejemplar']);
            $query_libro = "SELECT * FROM libros WHERE id_libro = " . $id_libro . ";";
            $resultado_libro = mysqli_query($connect, $query_libro);
            $fila_libro = mysqli_fetch_assoc($resultado_libro);
            $query_usuario = "SELECT * FROM usuarios WHERE id_usuario = " . $fila['id_usuario'] . ";";
            $resultado_usuario = mysqli_query($connect, $query_usuario);
            $fila_usuario = mysqli_fetch_assoc($resultado_usuario);

            if (strtolower($fila_usuario['tipo_usuario']) == 'alumno') {
                $prestamos .= "<tr class='item'>";
                $prestamos .= "<td>" . $fila['id_prestamo'] . "</td>";
                $prestamos .= "<td>" . ucfirst($fila_usuario['nombre']) . ' ' . $fila_usuario['apellido']  . "</td>";
                $prestamos .= "<td>" . $fila_usuario['rut'] . "</td>";
                $prestamos .= "<td>" . ucfirst($fila_usuario['tipo_usuario']) . "</td>";
                $prestamos .= "<td>" . $fila_usuario['correo']. "</td>";
                $prestamos .= "<td>" . $fila_libro['titulo'] . "</td>";
                $prestamos .= "<td>" . $fila['fecha_prestamo'] . "</td>";
                $prestamos .= "<td>" . $fila['fecha_devolucion'] . "</td>";
                $prestamos .= "<td>" . $fila['estado'] . "</td>";
                $prestamos .= "<td>" . $fila['renovaciones'] . "</td>";

                $prestamos .= "</tr>";
            }
        }
        $prestamos = mb_convert_encoding($prestamos, "UTF-8", "ISO-8859-1");
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
}

if (isset($_POST['detalles'])) {
    actualizar_prestamo($connect);

    $id_usuario = $_POST['id_usuario'];
    $query = "SELECT id_prestamo, id_usuario, id_ejemplar, fecha_prestamo, fecha_devolucion, estado, renovaciones FROM prestamos WHERE id_usuario=$id_usuario ORDER BY fecha_prestamo DESC;";
    


    if ($resultado = mysqli_query($connect, $query)) {
        while ($fila = mysqli_fetch_array($resultado)) {
            $id_libro = get_id_libro($fila['id_ejemplar']);
            $query_libro = "SELECT * FROM libros WHERE id_libro = " . $id_libro . ";";
            $resultado_libro = mysqli_query($connect, $query_libro);
            $fila_libro = mysqli_fetch_assoc($resultado_libro);
            $query_usuario = "SELECT * FROM usuarios WHERE id_usuario = " . $fila['id_usuario'] . ";";
            $resultado_usuario = mysqli_query($connect, $query_usuario);
            $fila_usuario = mysqli_fetch_assoc($resultado_usuario);
            
            $prestamos .= "<tr class='item'>";
            $prestamos .= "<td>" . $fila['id_prestamo'] . "</td>";
            $prestamos .= "<td>" . ucfirst($fila_usuario['nombre']) . ' ' . $fila_usuario['apellido']  . "</td>";
            $prestamos .= "<td>" . $fila_usuario['rut'] . "</td>";
            $prestamos .= "<td>" . ucfirst($fila_usuario['tipo_usuario']) . "</td>";
            $prestamos .= "<td>" . $fila_usuario['correo']. "</td>";
            $prestamos .= "<td>" . $fila_libro['titulo'] . "</td>";
            $prestamos .= "<td>" . $fila['fecha_prestamo'] . "</td>";
            $prestamos .= "<td>" . $fila['fecha_devolucion'] . "</td>";
            $prestamos .= "<td>" . $fila['estado'] . "</td>";
            $prestamos .= "<td>" . $fila['renovaciones'] . "</td>";
            $prestamos .= "</tr>";


        }
        $prestamos = mb_convert_encoding($prestamos, "UTF-8", "ISO-8859-1");
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
}


if ($_SESSION['tipo_usuario'] == 'Administrativo') {
    include('templates/sidebar_administrativo.php');
} else {
    header('location: index.html');
}
?>
<script>
    $(document).ready(function() {
        console.log("ready!");
        $(".input-buscar-usuarios").keyup(function() //se crea la funcion keyup
            {
                console.log("keyup!");
                var texto = $(this).val(); //se recupera el valor del input de texto y se guarda en la variable texto
                var cadenaBuscar = 'palabra=' + texto; //se guarda en una variable nueva para posteriormente pasarla a buscarCategoria.php
                if (texto == '') //si no tiene ningun valor el input de texto no realiza ninguna accion
                {
                    $("#mostrar").empty();
                } else {
                    $.ajax({ //metodo ajax
                        type: "POST", //aqui puede  ser get o post
                        url: "buscar_usuario_reporte.php", //la url donde se va a mandar la cadena a buscar
                        data: cadenaBuscar,
                        cache: false,
                        success: function(html) //funcion que se activa al recibir un dato
                        {
                            $("#mostrar").html(html).show(); // funcion jquery que muestra el div con identificador mostrar, como formato html
                        }
                    });
                }
                return false;
            });
    });
</script>


<form action="" method="post" enctype="multipart/form-data" style="margin:auto; " ;>
    <div class="d-grid d-md-flex mt-2">
        <input type="submit" class="btn btn-primary" id="activos" name="activos" value="Ultimos prestamos">
        <input type="submit" class="btn btn-secondary" id="docentes" name="docentes" value="Prestamos Docentes">
        <input type="submit" class="btn btn-secondary" id="alumnos" name="alumnos" value="Prestamos Alumnos">
        <input type="submit" class="btn btn-secondary" id="pendientes" name="pendientes" value="Prestamos Atrasados">
        <input type="submit" class="btn btn-success" id="multas" name="multas" value="Multas">
    </div>
</form>


<div class="mt-4">
    <input type="text" class="input-buscar-usuarios form-control me-2" placeholder="Buscar Usuario por RUT" id="caja_busqueda">
    <div class="card" id="mostrar">


    </div>

</div>


<div class="mt-3 ms-2 me-2">
    <table class="table table-hover table-striped table-light sortable">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Rut</th>
                <th>Tipo de usuario</th>
                <th>Correo</th>
                <th>Titulo</th>
                <th>Fecha Prestamo</th>
                <th>Fecha Devolucion</th>
                <th>Estado</th>
                <?php if (!isset($_POST['multas'])) {
                    echo '<th>Renovaciones</th>';
                } ?>
                <?php if (isset($_POST['multas'])) {
                    echo '<th>Multa</th>';
                } ?>
            </tr>
        </thead>
        <tbody id="tabla-prestamos">
            <?php echo $prestamos ?>
        </tbody>
    </table>
</div>



<?php


include("templates/footer.php");

?>