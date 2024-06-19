<?php
session_start();
include("conexion.php");
include("templates/header.php");
if ($_SESSION['tipo_usuario'] == 'Administrativo') {
    include('templates/sidebar_administrativo.php');
} else {
    header('location: index.html');
}
var_dump($_POST);

$libros = '';
$query_libros = "SELECT id_libro,titulo,autor,editorial,fecha_publicacion,stock FROM libros;";
$lista_ejemplares = '';



if (isset($_POST['id']) && !isset($_POST['id_ejemplar'])) {

    $query_libro = "SELECT id_libro as 'id',titulo as 'Titulo', 
                 autor as 'Autor', 
                 editorial as 'Editorial', 
                 fecha_publicacion as 'Año de Publicación',
                 stock as 'Stock'
          FROM libros 
          WHERE libros.id_libro=" . $_POST['id'] . ";";
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


if (isset($_POST['id_ejemplar'])) {


    $id_ejemplar = $_POST['id_ejemplar'];
    $query_ejemplar = "SELECT id_ejemplar, id_libro,(SELECT titulo FROM libros WHERE libros.id_libro = ejemplares.id_libro) as titulo, codigo_ejemplar, estado, condicion FROM ejemplares WHERE id_ejemplar = $id_ejemplar;";
    $resultado = mysqli_query($connect, $query_ejemplar);
    $fila = mysqli_fetch_array($resultado);
    $id_libro = $fila['id_libro'];
    $titulo_ejemplar = mb_convert_encoding($fila['titulo'], "UTF-8", "ISO-8859-1");
    $codigo = mb_convert_encoding($fila['codigo_ejemplar'], "UTF-8", "ISO-8859-1");
    $estado = mb_convert_encoding($fila['estado'], "UTF-8", "ISO-8859-1");
    $condicion = mb_convert_encoding($fila['condicion'], "UTF-8", "ISO-8859-1");
} else {
    $id_ejemplar = '';
    $id_libro = '';
    $titulo_ejemplar = '';
    $codigo = '';
    $estado = '';
    $condicion = '';
}

if (isset($_POST['ejemplares'])) {
    $id = $_POST['id'];
    $query_ejemplares = "SELECT id_ejemplar, (SELECT titulo FROM libros WHERE libros.id_libro = ejemplares.id_libro) as titulo, codigo_ejemplar, estado, condicion FROM ejemplares WHERE id_libro = $id;";
    $resultado = mysqli_query($connect, $query_ejemplares);
    $ejemplares = '';
    while ($fila = mysqli_fetch_array($resultado)) {
        $ejemplares .= "<tr class='item'>";
        $ejemplares .= "<td>" . $fila['titulo'] . "</td>";
        $ejemplares .= "<td>" . $fila['codigo_ejemplar'] . "</td>";
        $ejemplares .= "<td>" . $fila['estado'] . "</td>";
        $ejemplares .= "<td>" . $fila['condicion'] . "</td>";
        $ejemplares .= "<td><form method='POST' action='libros.php'>
                    <input type='hidden' name='id_ejemplar' value='" . $fila['id_ejemplar'] . "'>
                    <button type='submit' class='btn btn-secondary'>ver</button>
                </form></td>";
        $ejemplares .= "</tr>";
    }
    $ejemplares = mb_convert_encoding($ejemplares, "UTF-8", "ISO-8859-1");
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
            $libros .= "<td><form method='POST' action='libros.php'>
                    <input type='hidden' name='id' value='" . $fila['id_libro'] . "'>
                    <button type='submit' class='btn btn-secondary'>ver</button>
                </form></td>";
            $libros .= "</tr>";
        }
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
    $libros = mb_convert_encoding($libros, "UTF-8", "ISO-8859-1");
}

if (isset($_POST['listar_ejemplares']) || isset($_POST['listar_ejemplares_eliminados'])) {
    $query_ejemplares = "SELECT id_ejemplar, (SELECT titulo FROM libros WHERE libros.id_libro = ejemplares.id_libro) as titulo, codigo_ejemplar, estado, condicion FROM ejemplares ";
    isset($_POST['listar_ejemplares']) ? $query_ejemplares .= " WHERE estado != 'Eliminado';" : $query_ejemplares .= " WHERE estado = 'Eliminado';";


    if ($resultado = mysqli_query($connect, $query_ejemplares)) {
        while ($fila = mysqli_fetch_array($resultado)) {

  
                $lista_ejemplares .= "<tr class='item'>";
                $lista_ejemplares .= "<td>" . $fila['titulo'] . "</td>";
                $lista_ejemplares .= "<td>" . $fila['codigo_ejemplar'] . "</td>";
                $lista_ejemplares .= "<td>" . ucfirst($fila['estado']) . "</td>";
                $lista_ejemplares .= "<td>" . ucfirst($fila['condicion']) . "</td>";
                $lista_ejemplares .= "<td><form method='POST' action='libros.php'>
                    <input type='hidden' name='id_ejemplar' value='" . $fila['id_ejemplar'] . "'>
                    <button type='submit' class='btn btn-secondary'>ver</button>
                </form></td>";
                $lista_ejemplares .= "</tr>";

        }
    } else {
        echo "Error: " . $query_ejemplares . "<br>" . mysqli_error($connect);
    }
    $lista_ejemplares = mb_convert_encoding($lista_ejemplares, "UTF-8", "ISO-8859-1");
}








if (isset($_POST['guardar_ejemplar'])) {

    $query_codigo_ejemplar = "SELECT MAX(codigo_ejemplar) as codigo FROM ejemplares WHERE id_libro = " . $_POST['id_libro'] . ";";
    $res_codigo_ejemplar = mysqli_query($connect, $query_codigo_ejemplar);
    $fila_codigo_ejemplar = mysqli_fetch_array($res_codigo_ejemplar);

    $temp =  explode('-', $fila_codigo_ejemplar['codigo']);
    $codigo_nuevo_ejemplar = $temp[2] + 1;
    $codigo = mb_convert_encoding(strtoupper(substr($autor, 0, 2) . '-' . substr($titulo, 0, 2) . $id . '-' . $codigo_nuevo_ejemplar), "UTF-8", "ISO-8859-1");
    echo "CODIGO: " . $codigo . "<br>";


    $id_ejemplar = $_POST['id_ejemplar'];
    $codigo = $_POST['codigo'];
    $estado = ucfirst($_POST['estado']);
    $condicion = ucfirst($_POST['condicion']);
    if ($id_ejemplar == "") {
        $query = "INSERT INTO ejemplares (id_libro,codigo_ejemplar,estado,condicion) VALUES ('$id_libro','$codigo','$estado','$condicion');";
    } else {
        $query = "UPDATE ejemplares SET estado='$estado',condicion='$condicion' WHERE id_ejemplar=$id_ejemplar;";
    }

    if (mysqli_query($connect, $query)) {
        echo "Registro guardado";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
}


if (isset($_POST['guardar_libro'])) {
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
    $query = "DELETE FROM libros WHERE id=$id;";
    if (mysqli_query($connect, $query)) {
        echo "Registro eliminado";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
}
function esta_en_prestamo($id_ejemplar)
{
    include('conexion.php');
    $query = "SELECT id_prestamo FROM prestamos WHERE id_ejemplar=$id_ejemplar AND estado='activo';";
    $resultado = mysqli_query($connect, $query);
    if (mysqli_num_rows($resultado) > 0) {
        return true;
    } else {
        return false;
    }
}



if (isset($_POST['eliminar_ejemplar'])) {
    $id_ejemplar = $_POST['id_ejemplar'];

    if (esta_en_prestamo($id_ejemplar)) {
        echo "No se puede eliminar el ejemplar porque esta en prestamo";
        return;
    } else {
        $query = "UPDATE ejemplares SET estado='eliminado' WHERE id_ejemplar=$id_ejemplar;";
        if (mysqli_query($connect, $query)) {
            echo "Registro eliminado";
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($connect);
        }
    }
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
                        data: {
                            cadena: cadenaBuscar,
                            tipo: 'libro'
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
        <input type="submit" class="btn btn-secondary" id="listar_ejemplares" name="listar_ejemplares" value="Listar Ejemplares">
        
        <?php (isset($_POST['listar_ejemplares']) || isset($_POST['listar_ejemplares_eliminados']) )? $btn = '<button type="submit" class="btn btn-secondary" id="listar_ejemplares_eliminados" name="listar_ejemplares_eliminados" value="Listar Ejemplares Eliminados">Listar Eliminados</button>': $btn = '';?>
        <?php echo $btn; ?>
        <button type="submit" class="btn btn-success" id="listar" name="listar" value="Listar Libros">Listar Libros</button>
      <button type="submit" class="btn btn-success" id="agregar_libro" name="agregar_libro" value="Agregar Libro">Agregar Libro</button>



    </div>
</form>



<div class="mt-4">


    <input type="text" class="input-buscar-libros form-control me-2" placeholder="Buscar Libros" id="caja_busqueda">
    <div class="card mt-3" id="mostrar">
    </div>






</div>




<?php


if (isset($_POST['agregar_libro']) || isset($_POST['id'])) {




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
                            <label for="titulo"s class="form-label">Titulo:</label>
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
                <button type="submit" class="btn btn-primary" name="agregar_ejemplar">Agregar Ejemplar</button>
                <button type="submit" class="btn btn-primary" name="guardar_libro">Guardar Libro</button>
                <button type="submit" class="btn btn-danger" name="eliminar">Eliminar</button>
            </div>
        </div>
    </form>
    </div>
    EOT;
}

if (isset($_POST['listar_ejemplares']) || isset($_POST['listar_ejemplares_eliminados'])) {

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
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tabla-ejemplares">
            $ejemplares
        </tbody>
    </table>




EOT;
}
if (isset($_POST['id_ejemplar']) || isset($_POST['agregar_ejemplar'])) {
    if (isset($_POST['agregar_ejemplar'])) {
        $id_ejemplar = '';
        $titulo_ejemplar = '';
        $codigo = '';
        $estado = '';
        $condicion = '';
    }


    $sel = '';
    $estado = strtolower($estado);
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
                        <label for="id_libro" class="form-label">ID Libro:</label>
                        <input type="text" class="form-control" id="id_libro" name="id_libro" readonly value="$id_libro">
                    </div>
                </div>
                
                <div class="col-4">
                    <div class="mb-3">
                        <label for="codigo" class="form-label">Código de Ejemplar:</label>
                        <input type="text" class="form-control" id="codigo" name="codigo" readonly value="$codigo">
                    </div>
                </div>
            <div class="row">
            <div class="col-4">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Titulo:</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" readonly value="$titulo_ejemplar">
                    </div>
                </div>
             <div class="col-4">
                    <div class="mb-3">
                        <label for="condicion" class="form-label">Condición:</label>
                        <select name="condicion" class="form-control" id="condicion" name="condicion" ;>
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
                            <select name="estado" class="form-control" id="estado" name="estado" ;>
    EOT;


    ($estado) == 'no disponible' ? $sel = 'selected' : $sel = "";
    echo '<option value="no-disponible" ' . $sel . '>No Disponible</option>';
    ($estado) == 'disponible' ? $sel = 'selected' : $sel = "";
    echo '<option value="disponible" ' . $sel . '>Disponible</option>';
    ($estado) == 'en prestamo' ? $sel = 'selected' : $sel = "";
    echo '<option value="en-prestamo" ' . $sel . '>En Prestamo</option>';
    ($estado) == 'eliminado' ? $sel = 'selected' : $sel = "";
    echo '<option value="eliminado" ' . $sel . '>Eliminado</option>';
    echo <<<EOT
                </select>
                </div>
               
            </div>
        </div>
    </div>
    </div>
    <div class="row mb-4">
        <div class="col-12">
            <button type="submit" class="btn btn-primary" name="limpiar">Limpiar</button>
            <button type="submit" class="btn btn-primary" name="guardar_ejemplar">Guardar Ejemplar</button>
            <button type="submit" class="btn btn-danger" name="eliminar_ejemplar">Eliminar</button>
        </div>
        </div>
        </div>
    </form>
    </div>
 EOT;
}




















include('templates/footer.php');

?>