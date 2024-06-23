<?php
session_start();
include("conexion.php");
include("templates/header.php");

$query_autores = "SELECT id_autor,nombre,apellido,(SELECT count(*) FROM libros WHERE libros.autor = CONCAT(autores.nombre,' ',autores.apellido)) as publicaciones FROM autores;";

$query_editoriales = "SELECT id_editorial,nombre FROM editoriales;";

$libros_autor = '';
if (isset($_POST['libros_autor']) && isset($_POST['id_autor'])) {

    $query_autor_libros = "SELECT 
        libros.id_libro,
        libros.titulo,
        libros.autor,
        libros.editorial,
        libros.fecha_publicacion,
        libros.stock
        FROM 
        libros
        JOIN 
        autores ON libros.autor = CONCAT(autores.nombre, ' ', autores.apellido)
        WHERE 
        autores.id_autor = '" . $_POST['id_autor'] . "';";

    if ($resultado = mysqli_query($connect, $query_autor_libros)) {
        while ($fila = mysqli_fetch_array($resultado)) {
            $libros_autor .= "<tr class='item'>";
            $libros_autor .= "<td>" . $fila['titulo'] . "</td>";
            $libros_autor .= "<td>" . $fila['autor'] . "</td>";
            $libros_autor .= "<td>" . $fila['editorial'] . "</td>";
            $libros_autor .= "<td>" . $fila['fecha_publicacion'] . "</td>";
            $libros_autor .= "<td>" . $fila['stock'] . "</td>";
            $libros_autor .= "<td><form method='POST' action='libros.php'>
                    <input type='hidden' name='id' value='" . $fila['id_libro'] . "'>
                    <button type='submit' class='btn btn-secondary'>Detalles</button>
                </form></td>";
            $libros_autor .= "</tr>";
        }
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
    $libros_autor = mb_convert_encoding($libros_autor, "UTF-8", "ISO-8859-1");
}







if (isset($_POST['guardar'])) {
    $id_autor = $_POST['id_autor'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];

    if ($id_autor == "") {
        $query = "INSERT INTO autores(nombre,apellido) VALUES ('$nombre','$apellido');";
    } else {
        $query = "UPDATE autores SET nombre='$nombre',apellido='$apellido' WHERE id_autor=$id_autor;";
    }

    if (mysqli_query($connect, $query)) {
        echo '<script>agregarAlerta("alert-success", "Autor guardado con éxito.")</script>';
    } else {
        echo '<script>agregarAlerta("alert-danger", "Error, no se pudo ingresar el autor.")</script>';
    }
}

if (isset($_POST['eliminar'])) {
    $id_autor = $_POST['id_autor'];
    $query = "DELETE FROM autores WHERE id_autor=$id_autor;";
    if (mysqli_query($connect, $query)) {
        echo '<script>agregarAlerta("alert-success", "Autor eliminado con éxito.")</script>';
    } else {
        echo '<script>agregarAlerta("alert-danger", "Error, no se pudo eliminar el autor.")</script>';
    }
}
 
$autores = '';

if (isset($_POST['listar']) || $_POST == Array()) {
    if ($resultado = mysqli_query($connect, $query_autores)) {
        while ($fila = mysqli_fetch_array($resultado)) {

            $autores .= "<tr class='item'>";
            $autores .= "<td>" . $fila['id_autor'] . "</td>";
            $autores .= "<td>" . $fila['nombre'] . "</td>";
            $autores .= "<td>" . $fila['apellido'] . "</td>";
            $autores .= "<td>" . $fila['publicaciones'] . "</td>";
            $autores .= "<td><form method='POST' action='autores.php'>
                    <input type='hidden' name='id_autor' value='" . $fila['id_autor'] . "'>
                    <button type='submit' class='btn btn-secondary'>Detalles</button>
                </form></td>";
            $autores .= "</tr>";
        }
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
    $autores = mb_convert_encoding($autores, "UTF-8", "ISO-8859-1");
}

if (isset($_POST['id_autor']) && !isset($_POST['guardar'])) {
    $query_autor = "SELECT id_autor,nombre,apellido,(SELECT count(*) FROM libros WHERE libros.autor = CONCAT(autores.nombre,' ',autores.apellido)) as publicaciones FROM autores WHERE id_autor=" . $_POST['id_autor'] . ";";
    $resultado = mysqli_query($connect, $query_autor);
    $fila = mysqli_fetch_array($resultado);
    $id_autor = $fila['id_autor'];
    $nombre = mb_convert_encoding($fila['nombre'], "UTF-8", "ISO-8859-1");
    $apellido = mb_convert_encoding($fila['apellido'], "UTF-8", "ISO-8859-1");
    $publicaciones = $fila['publicaciones'];
} else {
    $id_autor = "";
    $titulo = "";
    $nombre = "";
    $apellido = "";
    $publicaciones = "";
}
if (isset($_POST['limpiar'])) {
    $id_autor = "";
    $nombre = "";
    $apellido = "";
    $publicaciones = "";
}

if ($_SESSION['tipo_usuario'] == 'Administrativo') {
    include('templates/sidebar_administrativo.php');
} else {
    header('location: index.html');
}

?>


<script>
    $(document).ready(function() {
        $(".input-buscar").keyup(function() //se crea la funcion keyup
            {
                var texto = $(this).val(); //se recupera el valor del input de texto y se guarda en la variable texto
                var cadenaBuscar = 'palabra=' + texto; //se guarda en una variable nueva para posteriormente pasarla a buscarCategoria.php
                if (texto == '') //si no tiene ningun valor el input de texto no realiza ninguna accion
                {
                    $("#mostrar").empty();
                } else {
                    $.ajax({ //metodo ajax
                        type: "POST", //aqui puede  ser get o post
                        url: "buscar_autor.php", //la url donde se va a mandar la cadena a buscar
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
        <input type="submit" class="btn btn-secondary" id="listar" name="listar" value="Listar Autores">
        <input type="submit" class="btn btn-success" id="agregar" name="agregar" value="Agregar Autor">
    </div>
</form>



<div class="mt-4">
    <input type="text" class="input-buscar form-control me-2" placeholder="Buscar Autor" id="caja_busqueda">
    <div class="card mt-3" id="mostrar">
    </div>
</div>




<?php


if (isset($_POST['agregar']) || isset($_POST['id_autor'])  ) {
    if (isset($_POST['agregar'])) {
        $id_autor = "";
        $nombre = "";
        $apellido = "";
        $publicaciones = "";
        $boton = '';
    }else{
        $boton = '<button type="submit" class="btn btn-primary" name="libros_autor">Ver Publicaciones</button>';
    }
    echo <<<EOT
    <div class="mt-4 card">
    <form class="mt-5" action="" method="post" enctype="multipart/form-data" style="margin:auto; " ;>
        <div class="row">
            <div class="col-9">
                <div class="row">
                    <div class="col-2">
                        <div class="mb-3">
                            <label for="id_autor" class="form-label">ID:</label>
                            <input type="text" class="form-control" id="id_autor" name="id_autor" readonly value="$id_autor">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="$nombre">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" value="$apellido">
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="mb-3">
                            <label for="publicaciones" class="form-label">Publicaciones</label>
                            <input type="text" class="form-control" id="publicaciones" readonly name="publicaciones" value="$publicaciones">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row mb-4">
            <div class="col-12">
                <button type="submit" class="btn btn-primary" name="limpiar">Limpiar</button>
                <button type="submit" class="btn btn-primary" name="guardar">Guardar</button>
                $boton
                <button type="submit" class="btn btn-danger" name="eliminar">Eliminar</button>
            </div>

        </div>



    </form>
</div>
EOT;
}








if (isset($_POST['listar']) || $_POST == Array ( ) ) {
    echo <<<EOT
    <div class="mt-3 ms-2 me-2" >
    <table class="table table-hover table-striped table-light sortable">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Publicaciones</th>
                <th class="sorttable_nosort">Acciones</th>
            </tr>
        </thead>
        <tbody id="tabla-autores">
            $autores
        </tbody>
    </table>


</div>
EOT;
}


if (isset($_POST['libros_autor']) && isset($_POST['id_autor'])) {

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
            $libros_autor
        </tbody>
    </table>


</div>




EOT;
}





















include('templates/footer.php');

?>