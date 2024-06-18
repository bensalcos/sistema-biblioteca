<?php
session_start();
include("conexion.php");
include("templates/header.php");

$query_editoriales = "SELECT id_editorial,nombre, (SELECT count(*) FROM libros WHERE libros.editorial = editoriales.nombre) as publicaciones FROM editoriales;";


$libros_editorial = '';

if (isset($_POST['libros_editorial']) && isset($_GET['id'])) {

    $query_libros_editorial = "SELECT 
        libros.id_libro,
        libros.titulo,
        libros.autor,
        libros.editorial,
        libros.fecha_publicacion,
        libros.stock
        FROM 
        libros
        JOIN 
        editoriales ON libros.editorial = editoriales.nombre
        WHERE 
        editoriales.id_editorial = '" . $_GET['id'] . "';";



    if ($resultado = mysqli_query($connect, $query_libros_editorial)) {
        while ($fila = mysqli_fetch_array($resultado)) {
            $libros_editorial .= "<tr class='item'>";
            $libros_editorial .= "<td>" . $fila['titulo'] . "</td>";
            $libros_editorial .= "<td>" . $fila['autor'] . "</td>";
            $libros_editorial .= "<td>" . $fila['editorial'] . "</td>";
            $libros_editorial .= "<td>" . $fila['fecha_publicacion'] . "</td>";
            $libros_editorial .= "<td>" . $fila['stock'] . "</td>";
            $libros_editorial .= "<td><a href='libros.php?id=" . $fila['id_libro'] . "'><i class='bi bi-gear text-danger'></i></a></td>";
            $libros_editorial .= "</tr>";
        }
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
    $libros_editorial = mb_convert_encoding($libros_editorial, "UTF-8", "ISO-8859-1");
}





if (isset($_POST['guardar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];


    if ($id == "") {
        $query = "INSERT INTO editoriales(nombre) VALUES ('$nombre');";
    } else {
        $query = "UPDATE editoriales SET nombre='$nombre' WHERE id_editorial=$id;";
    }

    if (mysqli_query($connect, $query)) {
        echo "Registro guardado";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
}

if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];
    $query = "DELETE FROM editoriales WHERE id_editorial=$id;";
    if (mysqli_query($connect, $query)) {
        echo "Registro eliminado";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
    header('location: editoriales.php');
}

$editoriales = '';

if (isset($_POST['listar'])) {
    if ($resultado = mysqli_query($connect, $query_editoriales)) {
        while ($fila = mysqli_fetch_array($resultado)) {

            $editoriales .= "<tr class='item'>";
            $editoriales .= "<td>" . $fila['id_editorial'] . "</td>";
            $editoriales .= "<td>" . $fila['nombre'] . "</td>";
            $editoriales .= "<td>" . $fila['publicaciones'] . "</td>";
            $editoriales .= "<td><a href='editoriales.php?id=" . $fila['id_editorial'] . "'><i class='bi bi-gear text-danger'></i></a></td>";
            $editoriales .= "</tr>";
        }
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
    $editoriales = mb_convert_encoding($editoriales, "UTF-8", "ISO-8859-1");
}
$suma_ejemplares = 0;


if (isset($_GET['id'])) {
    $query_editorial = "SELECT id_editorial,nombre,
    (SELECT count(*) FROM libros WHERE libros.editorial = editoriales.nombre) as publicaciones
    FROM editoriales WHERE id_editorial=" . $_GET['id'] . ";";
    $resultado = mysqli_query($connect, $query_editorial);
    $fila = mysqli_fetch_array($resultado);
    $id = $fila['id_editorial'];
    $nombre = mb_convert_encoding($fila['nombre'], "UTF-8", "ISO-8859-1");
    $publicaciones = $fila['publicaciones'];


    $query_suma_ejemplares = "SELECT editorial,stock FROM libros WHERE editorial = '" . $nombre . "';";
    if ($resultado = mysqli_query($connect, $query_suma_ejemplares)) {
        while ($fila = mysqli_fetch_array($resultado)) {

            $suma_ejemplares += $fila['stock'];
        }
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
} else {
    $id = "";
    $nombre = "";
    $publicaciones = "";
    $suma_ejemplares = 0;
}
if (isset($_POST['limpiar'])) {

    $id = "";
    $nombre = "";
    $publicaciones = "";
    $suma_ejemplares = 0;
}

if ($_SESSION['tipo_usuario'] == 'Administrativo') {
    include('templates/sidebar_administrativo.php');
} else {
    header('location: index.html');
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
        <input type="submit" class="btn btn-secondary" id="listar" name="listar" value="Listar Editoriales">
        <input type="submit" class="btn btn-success" id="agregar" name="agregar" value="Agregar Editorial">
    </div>
</form>



<div class="mt-4">


    <input type="text" class="input-buscar-libros form-control me-2" placeholder="Buscar Libros" id="caja_busqueda">



    <div class="card mt-3" id="mostrar">
    </div>







</div>




<?php


if (isset($_POST['agregar']) || isset($_GET['id'])) {

    if (isset($_POST['agregar'])) {
        $id = "";
        $nombre = "";
        $publicaciones = "";
        $suma_ejemplares = 0;
    }


    echo <<<EOT
    <div class="mt-4 card">
        <form class="mt-5" action method="post" enctype="multipart/form-data" style="margin:auto; " ;>
            <div class="row">
                <div class="col-9">
                <div class="row">
                    <div class="col-2">
                        <div class="mb-3">
                            <label for="id" class="form-label">ID:</label>
                            <input type="text" class="form-control" id="id" name="id" readonly value="$id">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="nombre"
                                class="form-label">Nombre:</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="$nombre">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <label for="publicaciones"
                                class="form-label">Publicaciones</label>
                            <input type="text" class="form-control" id="publicaciones" readonly name="publicaciones"value="$publicaciones">
                        </div>

                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <label for="cantidad_ejemplares"
                                class="form-label">Ejemplares</label>
                            <input type="text" class="form-control"
                                id="cantidad_ejemplares" readonly name="cantidad_ejemplares" value="$suma_ejemplares">
                        </div>
                    </div>
                </div>
            </div>

        </div>


    <div class="row mb-4">
            <div class="col-12">
                <button type="submit" class="btn btn-primary" name="limpiar">Limpiar</button>
                <button type="submit" class="btn btn-primary" name="libros_editorial">Ver Libros</button>
                <button type="submit" class="btn btn-primary" name="guardar">Modificar</button>
                <button type="submit" class="btn btn-danger" name="eliminar">Eliminar</button>
            </div>

        </div>

        </form>
        
EOT;
}








if (isset($_POST['listar'])) {
    echo <<<EOT
    <div class="mt-3 ms-2 me-2" >
    <table class="table table-hover table-striped table-light sortable">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Nombre editorial</th>
                <th>Libros</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tabla-autores">
            $editoriales
        </tbody>
    </table>


</div>
EOT;
}


if (isset($_POST['libros_editorial']) && isset($_GET['id'])) {

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
            $libros_editorial
        </tbody>
    </table>


</div>




EOT;
}





















include('templates/footer.php');

?>