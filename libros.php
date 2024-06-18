<?php
session_start();
include("conexion.php");
include("templates/header.php");

$query_agregar_ejemplar = "INSERT INTO ejemplares (id_libro,codigo_ejemplar,estado,condicion) VALUES ";
$libros = '';
$query_libros = "SELECT id_libro,titulo,autor,editorial,fecha_publicacion,stock FROM libros;";
$query_autores = "SELECT id_autor,nombre,apellido,(SELECT count(*) FROM libros WHERE libros.autor = CONCAT(autores.nombre,' ',autores.apellido)) as publicaciones FROM autores;";
$query_editoriales = "SELECT id_editorial,nombre FROM editoriales;";
$lista_ejemplares = '';

if (isset($_GET['id_ejemplar'])) {
    $id_ejemplar = $_GET['id_ejemplar'];

    $query_ejemplar = "SELECT id_ejemplar, (SELECT titulo FROM libros WHERE libros.id_libro = ejemplares.id_libro) as titulo, codigo_ejemplar, estado, condicion FROM ejemplares WHERE id_ejemplar = $id_ejemplar;";
    $resultado = mysqli_query($connect, $query_ejemplar);
    $fila = mysqli_fetch_array($resultado);
    $titulo_ejemplar = mb_convert_encoding($fila['titulo'], "UTF-8", "ISO-8859-1");;
    $codigo = mb_convert_encoding($fila['codigo_ejemplar'], "UTF-8", "ISO-8859-1");;
    $estado = mb_convert_encoding($fila['estado'], "UTF-8", "ISO-8859-1");;
    $condicion = mb_convert_encoding($fila['condicion'], "UTF-8", "ISO-8859-1");;
}
if (isset($_POST['ejemplares']) && isset($_GET['id'])) {

    $query_ejemplares = "SELECT id_ejemplar, (SELECT titulo FROM libros WHERE libros.id_libro = ejemplares.id_libro) as titulo, codigo_ejemplar, estado, condicion FROM ejemplares WHERE id_libro = $id;";
    $resultado = mysqli_query($connect, $query_ejemplares);
    $ejemplares = '';
    while ($fila = mysqli_fetch_array($resultado)) {
        $ejemplares .= "<tr class='item'>";
        $ejemplares .= "<td>" . $fila['titulo'] . "</td>";
        $ejemplares .= "<td>" . $fila['codigo_ejemplar'] . "</td>";
        $ejemplares .= "<td>" . $fila['estado'] . "</td>";
        $ejemplares .= "<td>" . $fila['condicion'] . "</td>";
        $ejemplares .= "<td><a href='libros.php?id_ejemplar=" . $fila['id_ejemplar'] . "'><i class='bi bi-gear text-danger'></i></a></td>";
        $ejemplares .= "</tr>";
    }
    $ejemplares = mb_convert_encoding($ejemplares, "UTF-8", "ISO-8859-1");
}

if (isset($_POST['listar_ejemplares'])) {
    $query_ejemplares = "SELECT id_ejemplar, (SELECT titulo FROM libros WHERE libros.id_libro = ejemplares.id_libro) as titulo, codigo_ejemplar, estado, condicion FROM ejemplares GROUP BY codigo_ejemplar;";


    if ($resultado = mysqli_query($connect, $query_ejemplares)) {
        while ($fila = mysqli_fetch_array($resultado)) {
            if (strtolower($fila['estado']) != 'eliminado') {
                $lista_ejemplares .= "<tr class='item'>";
                $lista_ejemplares .= "<td>" . $fila['titulo'] . "</td>";
                $lista_ejemplares .= "<td>" . $fila['codigo_ejemplar'] . "</td>";
                $lista_ejemplares .= "<td>" . $fila['estado'] . "</td>";
                $lista_ejemplares .= "<td>" . $fila['condicion'] . "</td>";
                $lista_ejemplares .= "<td><a href='libros.php?id_ejemplar=" . $fila['id_ejemplar'] . "'><i class='bi bi-gear text-danger'></i></a></td>";
                $lista_ejemplares .= "</tr>";
            }
        }
    } else {
        echo "Error: " . $query_ejemplares . "<br>" . mysqli_error($connect);
    }
    $lista_ejemplares = mb_convert_encoding($lista_ejemplares, "UTF-8", "ISO-8859-1");
}


if (isset($_POST['guardar'])) {
    $id = $_POST['id'];
    $titulo = $_POST['titulo'];
    $autor = $_POST['autor'];
    $editorial = $_POST['editorial'];
    $fecha = $_POST['fecha-publicacion'];
    $stock = $_POST['stock'];

    if ($id == "") {
        $query = "INSERT INTO libros (titulo,autor,editorial,fecha_publicacion,stock) VALUES ('$titulo','$autor','$editorial','$fecha',$stock);";
    } else {
        $query = "UPDATE libros SET titulo='$titulo',autor='$autor',editorial='$editorial',fecha_publicacion='$fecha',stock=$stock WHERE id_libro=$id;";
    }

    if (mysqli_query($connect, $query)) {
        echo "Registro guardado";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
}

if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];
    $query = "DELETE FROM libros WHERE id_libro=$id;";
    if (mysqli_query($connect, $query)) {
        echo "Registro eliminado";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
}


if (isset($_POST['listar'])) {
    if ($resultado = mysqli_query($connect, $query_libros)) {
        while ($fila = mysqli_fetch_array($resultado)) {

            $libros .= "<tr class='item'>";
            $libros .= "<td>" . $fila['titulo'] . "</td>";
            $libros .= "<td>" . $fila['autor'] . "</td>";
            $libros .= "<td>" . $fila['editorial'] . "</td>";
            $libros .= "<td>" . $fila['fecha_publicacion'] . "</td>";
            $libros .= "<td>" . $fila['stock'] . "</td>";
            $libros .= "<td><a href='libros.php?id=" . $fila['id_libro'] . "'><i class='bi bi-gear text-danger'></i></a></td>";
            $libros .= "</tr>";
        }
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
    $libros = mb_convert_encoding($libros, "UTF-8", "ISO-8859-1");
}

if (isset($_GET['id'])) {
    $query_libro = "SELECT id_libro as 'id',titulo as 'Titulo', 
                 autor as 'Autor', 
                 editorial as 'Editorial', 
                 fecha_publicacion as 'Año de Publicación',
                 stock as 'Stock'
          FROM libros 
          WHERE libros.id_libro=" . $_GET['id'] . ";";
    $resultado = mysqli_query($connect, $query_libro);
    $fila = mysqli_fetch_array($resultado);
    $id = $fila['id'];
    $titulo = mb_convert_encoding($fila['Titulo'], "UTF-8", "ISO-8859-1");
    $autor = mb_convert_encoding($fila['Autor'], "UTF-8", "ISO-8859-1");
    $editorial = mb_convert_encoding($fila['Editorial'], "UTF-8", "ISO-8859-1");
    $fecha = mb_convert_encoding($fila['Año de Publicación'], "UTF-8", "ISO-8859-1");
    $stock = $fila['Stock'];
} else {
    $id = "";
    $titulo = "";
    $autor = "";
    $editorial = "";
    $fecha = "";
    $stock = "";
}

if (isset($_POST['limpiar'])) {
    $id = "";
    $titulo = "";
    $autor = "";
    $editorial = "";
    $fecha = "";
    $stock = "";
    $id = "";
    $titulo = "";
    $autor = "";
    $editorial = "";
    $fecha = "";
    $stock = "";
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
        <input type="submit" class="btn btn-secondary" id="listar_ejemplares" name="listar_ejemplares" value="Listar Ejemplares">
        <input type="submit" class="btn btn-secondary" id="listar" name="listar" value="Listar Libros">
        <input type="submit" class="btn btn-success" id="agregar" name="agregar" value="Agregar Libro">
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
        $titulo = "";
        $autor = "";
        $editorial = "";
        $fecha = "";
        $stock = "";
    }



    echo <<<EOT
        <div class="mt-4 card">
        <form class="mt-5" action="" method="post" enctype="multipart/form-data" style="margin:auto; " ;>
        <div class="row">
            <div class="col-9">
                <div class="row">
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="id" class="form-label">ID:</label>
                            <input type="text" class="form-control" id="id" name="id" readonly value="$id">
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
                <button type="submit" class="btn btn-primary" name="limpiar">Limpiar</button>
                <button type="submit" class="btn btn-primary" name="ejemplares">Ver Ejemplares</button>
                <button type="submit" class="btn btn-primary" name="guardar">Guardar</button>
                <button type="submit" class="btn btn-danger" name="eliminar">Eliminar</button>
            </div>
        </div>
    </form>
    </div>
    EOT;
}

if (isset($_POST['listar_ejemplares'])) {
    echo <<<EOT
    <div class="mt-3 ms-2 me-2" >
    <table class="table table-hover table-striped table-light sortable">
        <thead class="thead-dark">
            <tr>
                <th>Título</th>
                <th>Codigo de Ejemplar</th>
                <th>Estado </th>
                <th>Condicion </th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tabla-libros">
            $lista_ejemplares
        </tbody>
    </table>
    </div>
    EOT;
}





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


if (isset($_POST['ejemplares']) && isset($_GET['id'])) {



    echo <<<EOT
    <div class="mt-3 ms-2 me-2" >
    <table class="table table-hover table-striped table-light sortable">
        <thead class="thead-dark">
            <tr>
                <th>Titulo</th>
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
if (isset($_GET['id_ejemplar'])) {

    echo <<<EOT
    <div class="mt-4 card">
    <form class="mt-5" action="" method="post" enctype="multipart/form-data" style="margin:auto; " ;>
    <div class="row">
        <div class="col-9">
            <div class="row">
                <div class="col-4">
                    <div class="mb-3">
                        <label for="id_ejemplar" class="form-label">ID:</label>
                        <input type="text" class="form-control" id="id_ejemplar" name="id_ejemplar" readonly value="$id_ejemplar">
                    </div>
                </div>
                <div class="col-4">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Titulo:</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" readonly value="$titulo_ejemplar">
                    </div>
                </div>
                <div class="col-4">
                    <div class="mb-3">
                        <label for="codigo" class="form-label">Código de Ejemplar:</label>
                        <input type="text" class="form-control" id="codigo" name="codigo" value="$codigo">
                    </div>
                </div>
                <div class="col-4">
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado:</label>
                        <input type="text" class="form-control" id="estado" name="estado" value="$estado">
                    </div>
                </div>
                <div class="col-4">
                    <div class="mb-3">
                        <label for="condicion" class="form-label">Condición:</label>
                        <input type="text" class="form-control" id="condicion" name="condicion" value="$condicion">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-12">
            <button type="submit" class="btn btn-primary" name="limpiar">Limpiar</button>
            <button type="submit" class="btn btn-primary" name="guardar">Guardar</button>
            <button type="submit" class="btn btn-danger" name="eliminar">Eliminar</button>
        </div>
    </div>
</form>
</div>
EOT;
}




















include('templates/footer.php');

?>