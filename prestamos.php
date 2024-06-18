<?php
session_start();
include("conexion.php");
include("templates/header.php");

// CODIGO DE EJEMPLAR
//$s = $fila['Autor'];
//$s = str_replace(' ', '', $s);
//$s = str_replace('.', '', $s);
//$s = strtoupper($s);
//$s = substr($s, 0, 2);
//$s2 = $fila['Titulo'];
//$s2 = str_replace(' ', '', $s2);
//$s2 = str_replace('.', '', $s2);
//$s2 = strtoupper($s2);
//$s2 = substr($s2, 0, 2);
//$s = $s . '-' . $s2 . $fila['id'] . '-';
//$codigo = $s;
//



$libros = '';

$query_libros = "SELECT id_libro,titulo,autor,editorial,fecha_publicacion,stock FROM libros;";
$query_ejemplares = "SELECT id_ejemplar, id_libro, codigo_ejemplar, estado, condicion FROM ejemplares GROUP BY codigo_ejemplar;";


if (isset($_POST['listar'])) {
    if ($resultado = mysqli_query($connect, $query_libros)) {
        while ($fila = mysqli_fetch_array($resultado)) {

            $libros .= "<tr class='item'>";
            $libros .= "<td>" . $fila['titulo'] . "</td>";
            $libros .= "<td>" . $fila['autor'] . "</td>";
            $libros .= "<td>" . $fila['editorial'] . "</td>";
            $libros .= "<td>" . $fila['fecha_publicacion'] . "</td>";
            $libros .= "<td>" . $fila['stock'] . "</td>";
            $libros .= "<td><a href='prestamos.php?id_libro=" . $fila['id_libro'] . "'><i class='bi bi-gear text-danger'></i></a></td>";
            $libros .= "</tr>";
        }
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
    $libros = mb_convert_encoding($libros, "UTF-8", "ISO-8859-1");
}

if (isset($_GET['id_libro'])) {
    $query_libro = "SELECT id_libro as 'id',
                titulo as 'Titulo', 
                 autor as 'Autor', 
                 editorial as 'Editorial', 
                 fecha_publicacion as 'Año de Publicación',
                 stock as 'Stock'
          FROM libros 
          WHERE libros.id_libro=" . $_GET['id_libro'] . ";";
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



if (isset($_POST['ejemplares']) && isset($_GET['id_libro'])) {
    $query_ejemplares = "SELECT id_ejemplar, id_libro, codigo_ejemplar, estado, condicion FROM ejemplares WHERE id_libro = $id_libro;";
    $resultado = mysqli_query($connect, $query_ejemplares);
    $ejemplares = '';
    while ($fila = mysqli_fetch_array($resultado)) {
        $ejemplares .= "<tr class='item'>";
        $ejemplares .= "<td>" . $fila['codigo_ejemplar'] . "</td>";
        $ejemplares .= "<td>" . $fila['estado'] . "</td>";
        $ejemplares .= "<td>" . $fila['condicion'] . "</td>";
        $ejemplares .= "<td><a class='btn btn-primary'  href='prestamos.php?ejemplar=" . $fila['codigo_ejemplar'] . "'>Solicitar Prestamo </button></a></td>";
        $ejemplares .= "</tr>";
    }
    $ejemplares = mb_convert_encoding($ejemplares, "UTF-8", "ISO-8859-1");
}

if (isset($_POST['solicitar_prestamo']) && isset($_GET['ejemplar'])) {

    $codigo_ejemplar = $_GET['ejemplar'];
    $id_usuario = $_SESSION['id_usuario'];
    $tipo = strtolower($_SESSION['tipo_usuario']);
    var_dump($_POST);
    echo $tipo;
    if (strtolower($_SESSION['tipo_usuario']) == ('docente')) {

        $codigo_ejemplar = $_GET['ejemplar'];
        $id_usuario = $_SESSION['id_usuario'];
        $tipo = strtolower($_SESSION['tipo_usuario']);
        $fecha_prestamo = date('Y-m-d');
        $fecha_devolucion = date('Y-m-d', strtotime($fecha_prestamo . ' + 15 days'));
        $estado = 'activo';
        $query_prestamo = "INSERT INTO prestamos (id_usuario, id_ejemplar, fecha_prestamo, fecha_devolucion, estado) VALUES ($id_usuario, $id_ejemplar, '$fecha_prestamo', '$fecha_devolucion', '$estado');";

        //if (mysqli_query($connect, $query_prestamo)) {
        //    echo "Prestamo solicitado";
        //} else {
        //    echo "Error: " . $query_prestamo . "<br>" . mysqli_error($connect);
        //}

        echo $query_prestamo;
    } else if (strtolower($_SESSION['tipo_usuario']) == ('alumno')) {

        $fecha_prestamo = date('Y-m-d');
        $fecha_devolucion = date('Y-m-d', strtotime($fecha_prestamo . ' + 7 days'));
        $estado = 'activo';
        $query_prestamo = "INSERT INTO prestamos (id_usuario, id_ejemplar, fecha_prestamo, fecha_devolucion, estado) VALUES ($id_usuario, $id_ejemplar, '$fecha_prestamo', '$fecha_devolucion', '$estado');";
        //if (mysqli_query($connect, $query_prestamo)) {
        //    echo "Prestamo solicitado";
        //} else {
        //    echo "Error: " . $query_prestamo . "<br>" . mysqli_error($connect);
        //}
        echo $query_prestamo;
    }
}







if ($_SESSION['tipo_usuario'] == 'Administrativo') {
    include('templates/sidebar_administrativo.php');
} else {
    include('templates/sidebar_usuarios.php');
}

?>


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
                        data: {cadena:cadenaBuscar, tipo:'prestamo'},
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



echo <<<EOT
    <div class="mt-4 card">
    <form class="mt-5" action="" method="post" enctype="multipart/form-data" style="margin:auto; " ;>
        <h3>Prestamos</h3>
        <div class="row">
            <div class="col-9">
                <div class="row">
                <div class="col-4">
                        <div class="mb-3">
                            <label for="id_libro" class="form-label">ID:</label>
                            <input type="text" class="form-control" id="id_libro" name="id_libro" value="$id_libro">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Titulo:</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" value="$titulo">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="autor" class="form-label">Autor</label>
                            <input type="text" class="form-control" id="autor" name="autor" value="$autor">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="editorial" class="form-label">Editorial</label>
                            <input type="text" class="form-control" id="editorial" name="editorial" value="$editorial">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="fecha-publicacion" class="form-label">Fecha de Publicación</label>
                            <input type="text" class="form-control" id="fecha_publicacion" name="fecha-publicacion" value="$fecha">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="stock" name="stock" value="$stock">
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







if (isset($_POST['listar'])) {
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
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tabla-libros">
            $libros
        </tbody>
    </table>


</div>
EOT;
}


if (isset($_POST['ejemplares']) && isset($_GET['id_libro'])) {


    echo <<<EOT
    <div class="mt-3 ms-2 me-2" >
    <table class="table table-hover table-striped table-light sortable">
        <thead class="thead-dark">
            <tr>
                <th>Código de Ejemplar</th>
                <th>Estado</th>
                <th>Condición</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tabla-ejemplares">
            $ejemplares
        </tbody>
    </table>

EOT;
}




















include('templates/footer.php');

?>