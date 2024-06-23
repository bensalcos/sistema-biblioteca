<?php
session_start();
include("conexion.php");
include("templates/header.php");



$query_ejemplares = "SELECT id_ejemplar, id_libro, codigo_ejemplar, estado, condicion FROM ejemplares GROUP BY codigo_ejemplar;";

if (isset($_POST['listar']) || $_POST == Array()) {
    $query_libros = "SELECT id_libro,titulo,autor,editorial,fecha_publicacion,stock FROM libros;";
    $libros = '';
    if ($resultado = mysqli_query($connect, $query_libros)) {
        while ($fila = mysqli_fetch_array($resultado)) {

            $libros .= "<tr class='item'>";
            $libros .= "<td>" . $fila['titulo'] . "</td>";
            $libros .= "<td>" . $fila['autor'] . "</td>";
            $libros .= "<td>" . $fila['editorial'] . "</td>";
            $libros .= "<td>" . $fila['fecha_publicacion'] . "</td>";
            $libros .= "<td>" . $fila['stock'] . "</td>";
            $libros .= "<td><form method='POST' action='prestamos.php'>
                    <input type='hidden' name='id_libro' value='" . $fila['id_libro'] . "'>
                    <button type='submit' class='btn btn-secondary'>Detalles</button>
                </form></td>";
            $libros .= "</tr>";
        }
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
    $libros = mb_convert_encoding($libros, "UTF-8", "ISO-8859-1");
}


if (isset($_POST['id_libro']) && !isset($_POST['id_ejemplar'])) {
    $query_libro = "SELECT id_libro as 'id',
                titulo as 'Titulo', 
                 autor as 'Autor', 
                 editorial as 'Editorial', 
                 fecha_publicacion as 'Año de Publicación',
                 stock as 'Stock'
          FROM libros 
          WHERE libros.id_libro=" . $_POST['id_libro'] . ";";
    $resultado = mysqli_query($connect, $query_libro);
    $fila = mysqli_fetch_array($resultado);
    $id_libro = $fila['id'];
    $titulo = mb_convert_encoding($fila['Titulo'], "UTF-8", "ISO-8859-1");
    $autor = mb_convert_encoding($fila['Autor'], "UTF-8", "ISO-8859-1");
    $editorial = mb_convert_encoding($fila['Editorial'], "UTF-8", "ISO-8859-1");
    $fecha = mb_convert_encoding($fila['Año de Publicación'], "UTF-8", "ISO-8859-1");
    $stock = $fila['Stock'];
} else {
    $id_libro = "";
    $titulo = "";
    $autor = "";
    $editorial = "";
    $fecha = "";
    $stock = "";
}

if (isset($_POST['id_ejemplar'])) {


    $id_ejemplar = $_POST['id_ejemplar'];
    $query_ejemplar = "SELECT id_ejemplar, id_libro,(SELECT titulo FROM libros WHERE libros.id_libro = ejemplares.id_libro) as titulo, codigo_ejemplar, estado, condicion FROM ejemplares WHERE id_ejemplar = $id_ejemplar;";
    $resultado = mysqli_query($connect, $query_ejemplar);
    $fila = mysqli_fetch_array($resultado);
    $id_libro_ejemplar = $fila['id_libro'];
    $titulo_ejemplar = mb_convert_encoding($fila['titulo'], "UTF-8", "ISO-8859-1");
    $codigo = mb_convert_encoding($fila['codigo_ejemplar'], "UTF-8", "ISO-8859-1");
    $estado = mb_convert_encoding($fila['estado'], "UTF-8", "ISO-8859-1");
    $condicion = mb_convert_encoding($fila['condicion'], "UTF-8", "ISO-8859-1");
} else {
    $id_ejemplar = '';
    $id_libro_ejemplar = '';
    $titulo_ejemplar = '';
    $codigo = '';
    $estado = '';
    $condicion = '';
}




if (isset($_POST['ejemplares'])) {
    $id_libro = $_POST['id_libro'];
    $query_ejemplares = "SELECT id_ejemplar, (SELECT titulo FROM libros WHERE libros.id_libro = ejemplares.id_libro) as titulo, codigo_ejemplar, estado, condicion FROM ejemplares WHERE id_libro = $id_libro;";
    $resultado = mysqli_query($connect, $query_ejemplares);
    $ejemplares = '';
    while ($fila = mysqli_fetch_array($resultado)) {
        if (strtolower($fila['estado']) == 'eliminado') {
            continue;
        }
        $ejemplares .= "<tr class='item'>";
        $ejemplares .= "<td>" . ucfirst($fila['titulo']) . "</td>";
        $ejemplares .= "<td>" . $fila['codigo_ejemplar'] . "</td>";
        $ejemplares .= "<td>" . ucfirst($fila['estado']) . "</td>";
        $ejemplares .= "<td>" . ucfirst($fila['condicion']) . "</td>";
        $ejemplares .= "<td><form method='POST' action='prestamos.php'>
                    <input type='hidden' name='id_ejemplar' value='" . $fila['id_ejemplar'] . "'>
                    <button type='submit' class='btn btn-secondary'>Detalles</button>
                </form></td>";
        $ejemplares .= "</tr>";
    }
    $ejemplares = mb_convert_encoding($ejemplares, "UTF-8", "ISO-8859-1");
}


function generarCodigo($rut, $codigo_ejemplar, $fecha_prestamo)
{
    $s = "PM-";
    $s .=  $rut;
    $s .= "-" . $codigo_ejemplar;
    $s .= "-" . $fecha_prestamo;

    return $s;
}


function cantidadPrestamos($id_usuario)
{
    include('conexion.php');
    $query = "SELECT COUNT(*) as cantidad FROM prestamos WHERE id_usuario = $id_usuario and estado = 'al dia';";
    $resultado = mysqli_query($connect, $query);
    $fila = mysqli_fetch_array($resultado);
    return $fila['cantidad'];
}
function esta_al_dia($id_usuario)
{
    include('conexion.php');
    actualizar_prestamo($connect);
    $query = "SELECT COUNT(*) as atrasos FROM prestamos WHERE id_usuario = $id_usuario and estado = 'atrasado';";
    $resultado = mysqli_query($connect, $query);
    $fila = mysqli_fetch_array($resultado);
    return $fila['atrasos'];
}



function es_prestamo_unico($id_usuario, $id_ejemplar,$codigo_prestamo)
{
    include('conexion.php');
    $query = "SELECT id_prestamo,id_usuario, id_ejemplar,codigo_prestamo,estado FROM prestamos WHERE id_usuario = $id_usuario and estado <> 'entregado';";
    $resultado = mysqli_query($connect, $query);
    while ($fila = mysqli_fetch_array($resultado)) {
        $codigo_pres = explode('-', $fila['codigo_prestamo']);
        $codigo_libro = $codigo_pres[2].'-'.$codigo_pres[3];
        $codigo_prestamo2 = explode('-', $codigo_prestamo);
        $codigo_libro2 = $codigo_prestamo2[2].'-'.$codigo_prestamo2[3];

        if ($codigo_libro == $codigo_libro2) {
            echo '<script>agregarAlerta("alert-danger", "Ya tiene en prestamo un ejemplar de este libro")</script>';
            return false;
        }
    }
    return true; 
}
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






if (isset($_POST['solicitar_prestamo'])) {
    actualizar_prestamo($connect);
    $codigo_ejemplar = $_POST['codigo'];
    $id_usuario = $_SESSION['id_usuario'];
    $tipo = strtolower($_SESSION['tipo_usuario']);
    $id_ejemplar = $_POST['id_ejemplar'];
    $estado = $_POST['estado'];
    $cantidad = cantidadPrestamos($id_usuario);
    $fecha_inicio_prestamo = $_POST['fecha_inicio_prestamo'];
    $fecha_inicio_prestamo == '' ? $fecha_inicio_prestamo = date('Y-m-d') : $fecha_inicio_prestamo;
    $dias_prestamo = $_POST['dias_prestamo'];
    $codigo_prestamo = generarCodigo(explode('-', $_SESSION['rut'])[0], $codigo_ejemplar, $fecha_inicio_prestamo);
    
        

    if (strtolower($estado) != 'disponible') {
        echo '<script>agregarAlerta("alert-danger", "El ejemplar no esta disponible")</script>';
    } else {
        if (es_prestamo_unico($id_usuario, $id_ejemplar,$codigo_prestamo)) {
            $estado = 'en prestamo';

            if (esta_al_dia($id_usuario) > 0) {
                echo '<script>agregarAlerta("alert-danger", "Tiene prestamos atrasados")</script>';

            } else{

            if (strtolower($_SESSION['tipo_usuario']) == ('docente')) {
                $fecha_devolucion = date('Y-m-d', strtotime($fecha_inicio_prestamo  . ' + ' . $dias_prestamo . ' days'));
                $query_prestamo = "INSERT INTO prestamos (id_usuario, id_ejemplar, codigo_prestamo,fecha_prestamo, fecha_devolucion, estado) VALUES ($id_usuario, $id_ejemplar,'$codigo_prestamo', '$fecha_inicio_prestamo ', '$fecha_devolucion', 'al dia');";
                $query_ejemplar = "UPDATE ejemplares SET estado = 'en prestamo' WHERE id_ejemplar = $id_ejemplar;";
                if (mysqli_query($connect, $query_ejemplar) && mysqli_query($connect, $query_prestamo)) {
                    echo '<script>agregarAlerta("alert-success", "Prestamo exitoso")</script>';
                } else {
                    echo "Error: " . $query_prestamo . "<br>" . mysqli_error($connect);
                }
            } else {

                $fecha_devolucion = date('Y-m-d', strtotime($fecha_inicio_prestamo  . ' + ' . $dias_prestamo . ' days'));
                if ($cantidad >= 4) {
                    echo '<script>agregarAlerta("alert-danger", "No puede solicitar mas prestamos")</script>';
                } else {
                    $query_prestamo = "INSERT INTO prestamos (id_usuario, id_ejemplar, codigo_prestamo,fecha_prestamo, fecha_devolucion, estado) VALUES ($id_usuario, $id_ejemplar,'$codigo_prestamo', '$fecha_inicio_prestamo ', '$fecha_devolucion', 'al dia');";
                    $query_ejemplar = "UPDATE ejemplares SET estado = 'en prestamo' WHERE id_ejemplar = $id_ejemplar;";
                    if (mysqli_query($connect, $query_ejemplar) && mysqli_query($connect, $query_prestamo)) {
                        echo '<script>agregarAlerta("alert-success", "Prestamo exitoso")</script>';
                    } else {
                        echo '<script>agregarAlerta("alert-danger", "Error: ' . $query_ejemplar . '"<br>" '. mysqli_error($connect).')</script>';
                    }
                }
            }
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
<div class="alerts">





</div>
<script>
    $(document).ready(function() {
        $(".input-buscar-libros").keyup(function() //se crea la funcion keyup
            {
                var texto = $(this).val(); //se recupera el valor del input de texto y se guarda en la variable texto
                var cadenaBuscar = 'palabra=' + texto; //se guarda en una variable nueva para posteriormente pasarla a buscarCategoria.php
                console.log(cadenaBuscar);
                if (texto == '') //si no tiene ningun valor el input de texto no realiza ninguna accion
                {
                    $("#mostrar").empty();
                } else {
                    $.ajax({ //metodo ajax
                        type: "POST", //aqui puede  ser get o post
                        url: "buscar_libro.php", //la url donde se va a mandar la cadena a buscar
                        data: {
                            cadena: cadenaBuscar,
                            tipo: 'prestamo'
                        },
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
        <input type="submit" class="btn btn-secondary" id="listar" name="listar" value="Listar Libros">

    </div>
</form>



<div class="mt-4">
    <input type="text" class="input-buscar-libros form-control me-2" placeholder="Buscar Libros" id="caja_busqueda">
    <div class="card mt-3" id="mostrar">
    </div>
</div>




<?php


if (isset($_POST['id_libro'])) {
    echo <<<EOT
    <div class="mt-4 card">
    <form class="mt-5 me-4 ms-4" action="" method="post" enctype="multipart/form-data" style="margin:auto; " ;>
        <h3>Prestamos</h3>
        <div class="row">
            <div class="col-9">
                <div class="row">
                <div class="col-4">
                        <div class="mb-3">
                            <label for="id_libro" class="form-label">ID:</label>
                            <input type="text" class="form-control" id="id_libro" name="id_libro" readonly value="$id_libro">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Titulo:</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" readonly value="$titulo">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="autor" class="form-label">Autor</label>
                            <input type="text" class="form-control" id="autor" name="autor" readonly value="$autor">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="editorial" class="form-label">Editorial</label>
                            <input type="text" class="form-control" id="editorial" name="editorial" readonly value="$editorial">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="fecha-publicacion" class="form-label">Fecha de Publicación</label>
                            <input type="text" class="form-control" id="fecha_publicacion" name="fecha-publicacion" readonly value="$fecha">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="stock" name="stock" readonly value="$stock">
                        </div>


                    </div>
                </div>
            </div>

        </div>

        <div class="row mb-4">
            <div class="col-12">

                <button type="submit" class="btn btn-primary" name="ejemplares">Ver Ejemplares</button>
            </div>

        </div>



    </form>
</div>
EOT;
}





if (isset($_POST['listar']) || $_POST == Array()) {
    echo <<<EOT
    <div class="mt-3 ms-2 me-2" >
    <table class="table table-hover table-striped table-light sortable">
        <thead class="thead-dark">
            <tr>
                <th>Título</th>
                <th>Autor</th>
                <th>Editorial</th>
                <th>Año de Publicación</th>
                <th>Stock </th>
                <th class="sorttable_nosort">Acciones</th>
            </tr>
        </thead>
        <tbody id="tabla-libros">
            $libros
        </tbody>
    </table>


</div>
EOT;
}


if (isset($_POST['ejemplares'])) {


    echo <<<EOT
    <div class="mt-3 ms-2 me-2" >
    <table class="table table-hover table-striped table-light sortable">
        <thead class="thead-dark">
            <tr>
                <th>Titulo</th>
                <th>Código de Ejemplar</th>
                <th>Estado</th>
                <th>Condición</th>
                <th class="sorttable_nosort">Acciones</th>
            </tr>
        </thead>
        <tbody id="tabla-ejemplares">
            $ejemplares
        </tbody>
    </table>
    </div>

EOT;
}



if (isset($_POST['id_ejemplar'])) {
    $dias_prestamo = 7;
    $minimo = 1;

    if (strtolower($_SESSION['tipo_usuario']) == 'docente'){
        $dias_prestamo = 20;
        $minimo = 7;
    }else {
        $dias_prestamo = 7;
        $minimo = 0;
    }

    $sel = '';
    echo <<<EOT
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var inputDiasPrestamo = document.getElementById('dias_prestamo');
            inputDiasPrestamo.addEventListener('input', function () {
                if (inputDiasPrestamo.value > $dias_prestamo) {
                    inputDiasPrestamo.value = $dias_prestamo;
                } else if (inputDiasPrestamo.value < 0) {
                    inputDiasPrestamo.value = 0;
                }
            });
            inputDiasPrestamo.setAttribute('max', $dias_prestamo);
            inputDiasPrestamo.setAttribute('min', $minimo);
        });
    </script>
    <div class="mt-4 card">
    <form class="mt-5" action="" method="post" enctype="multipart/form-data" style="margin:auto; " ;>
    <div class="row">
        <div class="col-9">
            <div class="row">
                <div class="col-2">
                    <div class="mb-3">
                        <label for="id_ejemplar" class="form-label">ID:</label>
                        <input type="text" class="form-control" id="id_ejemplar" name="id_ejemplar" readonly value="$id_ejemplar">
                    </div>
                </div>
                <div class="col-2">
                    <div class="mb-3">
                        <label for="id_libro_ejemplar" class="form-label">ID Libro:</label>
                        <input type="text" class="form-control" id="id_libro_ejemplar" name="id_libro_ejemplar" readonly value="$id_libro_ejemplar">
                    </div>
                </div>
                
                <div class="col-4">
                    <div class="mb-3">
                        <label for="codigo" class="form-label">Código de Ejemplar:</label>
                        <input type="text" class="form-control" id="codigo" name="codigo" readonly value="$codigo">
                    </div>
                </div>
                <div class="col-4">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Titulo:</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" readonly value="$titulo_ejemplar">
                    </div>
                </div>
                </div>
            <div class="row">
            
            <div class="col-4">
                <div class="mb-3">
                    <label for="condicion" class="form-label">Condición:</label>
                    <select name="condicion" class="form-control" id="condicion" readonly name="condicion" ;>
    EOT;
    (strtolower($condicion)) == '' ? $sel = 'selected' : $sel = "";
    echo '<option value="" ' . $sel . '>Seleccione condicion del ejemplar</option>';
    (strtolower($condicion)) == 'nuevo' ? $sel = 'selected' : $sel = "";
    echo '<option value="nuevo" ' . $sel . '>Nuevo</option>';
    (strtolower($condicion)) == 'semi_nuevo' ? $sel = 'selected' : $sel = "";
    echo '<option value="semi nuevo" ' . $sel . '>Semi Nuevo</option>';
    (strtolower($condicion)) == 'deteriorado' ? $sel = 'selected' : $sel = "";
    echo '<option value="deteriorado" ' . $sel . '>Deteriorado</option>';
    (strtolower($condicion)) == 'buena' ? $sel = 'selected' : $sel = "";
    echo '<option value="buena" ' . $sel . '>Buena</option>';
    echo <<<EOT
                    </select>
                </div>
            </div>
                <div class="col-4">
                    <label for="estado" class="form-label">Estado:</label>
                            <select name="estado" class="form-control" id="estado" readonly name="estado" ;>
    EOT;


    (strtolower($estado)) == 'no disponible' ? $sel = 'selected' : $sel = "";
    echo '<option value="no-disponible" ' . $sel . '>No Disponible</option>';
    (strtolower($estado)) == 'disponible' ? $sel = 'selected' : $sel = "";
    echo '<option value="disponible" ' . $sel . '>Disponible</option>';
    (strtolower($estado)) == 'en prestamo' ? $sel = 'selected' : $sel = "";
    echo '<option value="en-prestamo" ' . $sel . '>En Prestamo</option>';
    (strtolower($estado)) == 'eliminado' ? $sel = 'selected' : $sel = "";
    echo '<option value="eliminado" ' . $sel . '>Eliminado</option>';
    echo <<<EOT
                </select>
                </div>
                <div class="col-4">
                    <div class="mb-3">
                        <label for="dias_prestamo" class="form-label">Dias de Prestamo:</label>
                        <input type="number" class="form-control" id="dias_prestamo" name="dias_prestamo" value="0">
                    </div>
                </div>
            </div>
            <div class="row" >
        <div class="col-4">
        <div class="mb-3">
            <label for="fecha_inicio_prestamo" class="form-label">Fecha de inicio de prestamo:</label>
            <input type="date" id="fecha_inicio_prestamo" name="fecha_inicio_prestamo" >
        </div>
            </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-12">
            <button type="submit" class="btn btn-primary" name="solicitar_prestamo">Solicitar Prestamo</button>
        </div>
    </div>
    
    </form>
    </div>
 EOT;
}





include('templates/footer.php');

?>