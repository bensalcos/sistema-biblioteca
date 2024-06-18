<?php
session_start();
include("conexion.php");
include("templates/header.php");

var_dump($_POST);
if (isset($_GET['id'])) {
    $query = "SELECT * FROM usuarios WHERE id_usuario = " . $_GET['id'];
    $resultado = mysqli_query($connect, $query);
    $fila = mysqli_fetch_array($resultado);

    $id = $fila['id_usuario'];
    $nombre = mb_convert_encoding($fila['nombre'], "UTF-8", "ISO-8859-1");
    $apellido = mb_convert_encoding($fila['apellido'], "UTF-8", "ISO-8859-1");
    $correo = mb_convert_encoding($fila['correo'], "UTF-8", "ISO-8859-1");
    $clave = mb_convert_encoding($fila['clave'], "UTF-8", "ISO-8859-1");
    $rut = $fila['rut'];
    $tipo_usuario = mb_convert_encoding($fila['tipo_usuario'], "UTF-8", "ISO-8859-1");
    $foto = $fila['foto'];
} else {

    $id = "";
    $nombre = "";
    $apellido = "";
    $correo = "";
    $clave = "";
    $rut = "";
    $tipo_usuario = "";
    $foto = "usuario.png";
}

if (isset($_POST['crear'])) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $rut = $_POST['rut'];
    $tipo_usuario = $_POST['tipo_usuario'];
    $foto = $_FILES['foto']['name'];
    $ruta = $_FILES['foto']['tmp_name'];
    $destino = "media/img/" . $foto;
    copy($ruta, $destino);
    $clave1 = $_POST['clave'];
    $clave2 = $_POST['clave2'];


    if ($clave1 == $clave2) {
        $clave = password_hash($clave1, PASSWORD_DEFAULT, ["cost" => 10]);
        $query = "INSERT INTO usuarios (nombre,apellido,rut,tipo_usuario,correo,clave,foto) VALUES ('$nombre','$apellido','$rut','$tipo_usuario','$correo','$clave','$foto')";
        $resultado = mysqli_query($connect, $query);
        if ($resultado) {
            echo "<script>alert('Usuario creado correctamente');</script>";
        } else {
            echo "<script>alert('Error al crear el usuario');</script>";
        }
    } else {
        $msg = "<div class='alert alert-danger'>Las Contraseñas no son iguales!</div>";
    }
}

if (isset($_POST['modificar'])) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $clave1 = $_POST['clave'];
    $clave2 = $_POST['clave2'];
    $rut = $_POST['rut'];
    $tipo_usuario = $_POST['tipo_usuario'];
    $foto = $_FILES['foto']['name'];
    $ruta = $_FILES['foto']['tmp_name'];
    $destino = "media/img/" . $foto;
    copy($ruta, $destino);

    if ($clave1 == $clave2) {
        $clave = password_hash($clave1, PASSWORD_DEFAULT, ["cost" => 10]);
        $query = "UPDATE usuarios SET nombre='$nombre',apellido='$apellido',rut='$rut',tipo_usuario='$tipo_usuario',correo='$correo',clave='$clave',foto='$foto' WHERE id_usuario=" . $_POST['id'];
        $resultado = mysqli_query($connect, $query);
        if ($resultado) {
            echo "<script>alert('Usuario modificado correctamente');</script>";
        } else {
            echo "<script>alert('Error al modificar el usuario');</script>";
        }
    } else {
        $msg = "<div class='alert alert-danger'>Las Contraseñas no son iguales!</div>";
    }
}


if (isset($_POST['eliminar'])) {
    $query = "DELETE FROM usuarios WHERE id_usuario=" . $_POST['id'];
    $resultado = mysqli_query($connect, $query);
    if ($resultado) {
        echo "<script>alert('Usuario eliminado correctamente');</script>";
        header("location: usuarios.php");
    } else {
        echo "<script>alert('Error al eliminar el usuario');</script>";
    }
}

if (isset($_POST['limpiar'])) {
    $id = "";
    $nombre = "";
    $apellido = "";
    $correo = "";
    $clave = "";
    $rut = "";
    $tipo_usuario = "";
    $foto = "usuario.png";
}


if (isset($_POST['agregar'])) {
    header("location: usuarios.php");
}




if (isset($_POST['subir_foto'])) {
    //Asignamos los datos recibidos en variables locales
    $id = intval($_POST['id']);
    //Estos datos se reciben del input que recoge la imagen
    $nombreImg = $_FILES['foto']['name'];
    $tipo = $_FILES['foto']['type'];
    $tamano = $_FILES['foto']['size'];
    $nombreTemp = $_FILES['foto']['tmp_name'];

    //Verificamos si existe imagen y tamaño correcto
    if (($nombreImg == !NULL) && ($tamano <= 3000000)) {
        //Ahora verificamos los formatos permitidos
        if (($tipo == "image/gif") || ($tipo == "image/jpeg") || ($tipo == "image/jpg") || ($tipo == "image/png")) {
            //Indicamos la ruta donde subiremos los archivos
            $ruta = 'media/img/';
            $nombreFoto = 'user' . $_POST['id'] . ".png";
            //Ahora movemos la imagen desde el directorio temporal al directorio definitivo
            move_uploaded_file($nombreTemp, $ruta . $nombreFoto);
            header("location: usuarios.php?id=" . $id);
            // Trabajamos en actualizar la foto en la BD
            $query = "UPDATE usuarios SET foto = '" . $nombreFoto . "' WHERE id = " . $id . ";";
            if (mysqli_query($connect, $query)) {
                $msg = "<div class='alert alert-success'>Fotografía actualizada correctamente!!!</div>";
            } else {
                $msg = "<div class='alert alert-danger'>No se ha podido actualizar la fotografia del usuario!!!</div>";
            }
        } else {
            //Si el formato no es permitido
            $msg = '<div class="alert alert-danger"><b>El formato del archivo no esta permitido.</b></div>';
        }
    } else {
        //Si el archivo es de tamaño mayor al permitido
        $msg = '<div class="alert alert-danger"><b>El archivo tiene un tamaño mayor al permitido.</b></div>';
    }
}



$query_usuarios = "SELECT id_usuario,nombre,apellido,rut,tipo_usuario,correo,clave,foto FROM usuarios";
if (isset($_POST['listar'])) {


    $resultado_usuarios = mysqli_query($connect, $query_usuarios);
    $usuarios = "";

    if (isset($_POST['listar'])) {
        if ($resultado = mysqli_query($connect, $query_usuarios)) {
            while ($fila = mysqli_fetch_array($resultado)) {

                $usuarios .= "<tr class='item'>";
                $usuarios .= "<td>" . $fila['id_usuario'] . "</td>";
                $usuarios .= "<td>" . $fila['nombre'] . "</td>";
                $usuarios .= "<td>" . $fila['apellido'] . "</td>";
                $usuarios .= "<td>" . $fila['rut'] . "</td>";
                $usuarios .= "<td>" . $fila['tipo_usuario'] . "</td>";
                $usuarios .= "<td>" . $fila['correo'] . "</td>";
                $usuarios .= "<td><img src='media/img/" . $fila['foto'] . "' width='50' height='50'></td>";
                $usuarios .= "<td><a href='usuarios.php?id=" . $fila['id_usuario'] . "'><button type='button' class='btn btn-primary'>Editar</button></a></td>";
                $usuarios .= "</tr>";
            }
        } else {
            echo "Error: " . $query_usuarios . "<br>" . mysqli_error($connect);
        }
        $usuarios = mb_convert_encoding($usuarios, "UTF-8", "ISO-8859-1");
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
        console.log("ready!");
        $(".input-buscar-usuarios").keyup(function() //se crea la funcion keyup
            {
                console.log("keyup!");
                var texto = $(this).val(); //se recupera el valor del input de texto y se guarda en la variable texto
                var cadenaBuscar = 'palabra=' + texto; //se guarda en una variable nueva para posteriormente pasarla a buscarCategoria.php
                console.log(cadenaBuscar);
                if (texto == '') //si no tiene ningun valor el input de texto no realiza ninguna accion
                {
                    $("#mostrar").empty();
                } else {
                    $.ajax({ //metodo ajax
                        type: "POST", //aqui puede  ser get o post
                        url: "buscar_usuario.php", //la url donde se va a mandar la cadena a buscar
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
        <input type="submit" class="btn btn-secondary" id="listar" name="listar" value="Listar Usuarios">
        <input type="submit" class="btn btn-success" id="agregar" name="agregar" value="Agregar Nuevo">
    </div>
</form>



<div class="mt-4">
    <input type="text" class="input-buscar-usuarios form-control me-2" placeholder="Buscar Usuario" id="caja_busqueda">


    <div class="card" id="mostrar">


    </div>

</div>



<div class="mt-4 card">
    <form class="mt-5 me-5 ms-5" action="" method="post" enctype="multipart/form-data" ;>
        <div class="row">


            <div class="col-4">
                <img class="rounded mx-auto d-block" src='media/img/<?php echo $foto; ?>' width='100' height='100' class='rounded-circle me-2'>
                <div class="mb-2">
                    <input class="form-control mt-5" type="file" id="foto" name="foto">
                    <input type="submit" class="btn btn-info form-control mt-2" id="subir_foto" name="subir_foto" value="Subir Foto">
                </div>

            </div>



            <div class="col-8">
                <div class="row">
                    <div class="col-2">
                        <div class="mb-3">
                            <label for="id" class="form-label">ID:</label>
                            <input type="text" class="form-control" id="id" name="id" readonly value="<?php echo $id; ?>">
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $nombre; ?>">
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="mb-3">
                            <label for="apellido" class="form-label">Apellido:</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo $apellido; ?>">
                        </div>
                    </div>


                </div>

                <div class="row">

                    <div class="col-4">
                        <div class="mb-3">
                            <label for="rut" class="form-label">Rut:</label>
                            <input type="text" class="form-control" id="rut" name="rut" value="<?php echo $rut; ?>">
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="mb-3">
                            <label for="clave" class="form-label">Clave:</label>
                            <input type="password" class="form-control" id="clave" name="clave" value="">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="clave2" class="form-label">Repetir Clave:</label>
                            <input type="password" class="form-control" id="clave2" name="clave2" value="">
                        </div>
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
                            <label for="tipo_usuario" class="form-label">Tipo de usuario:</label>
                            <select name="tipo_usuario" class="form-control" id="tipo_usuario" name="tipo_usuario" ;>
                                <option value="" <?php if (strtolower($tipo_usuario) != 'alumno' || strtolower($tipo_usuario) != 'docente') echo 'selected' ?>selected></option>
                                <option value="alumno" <?php if (strtolower($tipo_usuario) == 'alumno') echo 'selected' ?>>Alumno</option>
                                <option value="docente" <?php if (strtolower($tipo_usuario) == 'docente') echo 'selected' ?>>Docente</option>
                            </select>


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
if (isset($_POST['listar'])) {
    echo <<<EOT
    <div class="mt-3 ms-2 me-2" >
    <table class="table table-hover table-striped table-light sortable">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Rut</th>
                <th>Tipo de usuario</th>
                <th>Correo</th>
                <th>Foto</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tabla-usuarios">
            $usuarios
        </tbody>
    </table>


</div>
EOT;
}


?>





<?php


include("templates/footer.php");

?>