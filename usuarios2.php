<?php
session_start();
include("conexion.php");

$msg = "";
// ****************** PARA CARGAR EL FORMULARIO ***********************
if(isset($_GET['id'])){
    $query = "SELECT id,nombre,apellido,correo,clave,perfil,foto FROM usuarios WHERE id=".$_GET['id'].";";
    $resultado = mysqli_query($connect, $query);
    $fila = mysqli_fetch_array($resultado);
    $id = $fila['id'];
    $nombre = $fila['nombre'];
    $apellido = $fila['apellido'];
    $correo = $fila['correo'];
    $clave = $fila['clave'];    
    $perfil = "<option selected>".$fila['perfil']."</option>";
    $foto = $fila['foto']; 
}else{
    $id = "";
    $nombre = "";
    $apellido = "";
    $correo = "";
    $clave = "";
    $perfil = "<option>Seleccione Perfil</option>";
    $foto = "usuario.png";
}

// ****************** PARA LIMPIAR EL FORMULARIO ***********************
if(isset($_POST['limpiar'])){
    $id = "";
    $nombre = "";
    $apellido = "";
    $correo = "";
    $clave = "";
    $perfil = "<option>Seleccione Perfil</option>";
    $msg = "";
    $foto = "usuario.png";
}

// ****************** PARA INGRESAR UN USUARIO NUEVO *******************
if (isset($_POST['crear'])) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $perfil = $_POST['perfil'];
    $clave1 = $_POST['clave1'];
    $clave2 = $_POST['clave2'];
    if($clave1 == $clave2){        
        $clave = password_hash($clave1, PASSWORD_DEFAULT, ["cost" => 10]);
        $query = "INSERT INTO usuarios(nombre,apellido,correo,clave,perfil) VALUES ('$nombre','$apellido','$correo','$clave','$perfil')";
        if(mysqli_query($connect, $query)){
            $msg = "<div class='alert alert-success'>Usuario registrado correctamente!!!</div>";
        }else{
            $msg = "<div class='alert alert-danger'>No se ha podido ingresar el Usuario!</div>";
        }        
    }else{
        $msg = "<div class='alert alert-danger'>Las Contraseñas no son iguales!</div>";
    }
}

// ****************** PARA MODIFICAR UN USUARIO *************************
if (isset($_POST['modificar'])) {
    //$query = "SELECT id,nombre,apellido,correo,clave,perfil FROM usuarios WHERE id=".$id.";";
    //$resultado = mysqli_query($connect, $query);
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $perfil = $_POST['perfil'];
    $clave1 = $_POST['clave1'];
    $clave2 = $_POST['clave2'];
    if($clave1 == $clave2){
        $clave = password_hash($clave1, PASSWORD_DEFAULT, ["cost" => 10]);
        $query = "UPDATE usuarios 
                  SET nombre = '".$nombre."', apellido = '".$apellido."', correo = '".$correo."', perfil = '".$perfil."', clave = '".$clave."' 
                  WHERE id = ".$id.";";
        if(mysqli_query($connect, $query)){
            $msg = "<div class='alert alert-success'>Usuario actualizado correctamente!!!</div>";
            header("location: usuarios.php?id=".$id);
        }else{
            $msg = "<div class='alert alert-danger'>No se ha podido actualizar el usuario!!!</div>";
        }
    }else{
        $msg = "<div class='alert alert-danger'>Las Contraseñas no son iguales!</div>";
    }    
}
// ****************** PARA ELIMINAR UN USUARIO *******************
if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];
    $query = "DELETE FROM usuarios WHERE id = ".$id.";";
    if(mysqli_query($connect, $query)){
        $msg = "<div class='alert alert-success'>Usuario eliminado correctamente!!!</div>";
        header("location: inicio.php");
    }else{
        $msg = "<div class='alert alert-danger'>No se ha podido eliminar el Usuario!</div>";
    } 
}

// **************************PARA SUBIR LA IMAGEN *****************************************
if (isset($_POST['subirFoto'])) {
    //Asignamos los datos recibidos en variables locales
    $id = intval($_POST['id']);
    //Estos datos se reciben del input que recoge la imagen
    $nombreImg = $_FILES['foto']['name'];
    $tipo = $_FILES['foto']['type'];
    $tamano = $_FILES['foto']['size'];
    $nombreTemp = $_FILES['foto']['tmp_name'];

    //Verificamos si existe imagen y tamaño correcto
    if(($nombreImg == !NULL) && ($tamano<=3000000)){
        //Ahora verificamos los formatos permitidos
        if(($tipo=="image/gif") || ($tipo=="image/jpeg") || ($tipo=="image/jpg") || ($tipo=="image/png")){
            //Indicamos la ruta donde subiremos los archivos
            $ruta = 'img/usuarios/';
            $nombreFoto = 'user'.$_POST['id'].".png";
            //Ahora movemos la imagen desde el directorio temporal al directorio definitivo
            move_uploaded_file($nombreTemp, $ruta.$nombreFoto);          
            header("location: usuarios.php?id=".$id);
// Trabajamos en actualizar la foto en la BD
            $query = "UPDATE usuarios SET foto = '".$nombreFoto."' WHERE id = ".$id.";";
            if(mysqli_query($connect, $query)){
                $msg = "<div class='alert alert-success'>Fotografía actualizada correctamente!!!</div>";
            }else{
                $msg = "<div class='alert alert-danger'>No se ha podido actualizar la fotografia del usuario!!!</div>";
            }
        }else{
            //Si el formato no es permitido
            $msg = '<div class="alert alert-danger"><b>El formato del archivo no esta permitido.</b></div>';
        }
    }else{
        //Si el archivo es de tamaño mayor al permitido
        $msg = '<div class="alert alert-danger"><b>El archivo tiene un tamaño mayor al permitido.</b></div>';
    }
}

include("cabecera.php");
include("menu.php");
?>
<script>
$(document).ready(function() {
    $(".busca").keyup(function() //se crea la funcion keyup
        {
            var texto = $(this).val(); //se recupera el valor del input de texto y se guarda en la variable texto
            var cadenaBuscar = 'palabra=' + texto; //se guarda en una variable nueva para posteriormente pasarla a buscarCategoria.php
            if (texto == '') //si no tiene ningun valor el input de texto no realiza ninguna accion
            {} else {
                $.ajax({ //metodo ajax
                    type: "POST", //aqui puede  ser get o post
                    url: "buscar.php", //la url donde se va a mandar la cadena a buscar
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
<?php echo $msg; ?>
<div class="row mt-5">
    <div class="col-6 offset-3 shadow p-5">
        <form action="" method="post" enctype="multipart/form-data">
            <h3>Mantenedor de Usuarios</h3>
            <div class="row">
                <div class="col-9">
                    <div class="row">
                    <div class="col-4">
                    <div class="mb-3">
                        <label for="id" class="form-label">ID:</label>
                        <input type="text" class="form-control" id="id" name="id" readonly value="<?php echo $id; ?>">
                    </div>
                </div>
                <div class="col-4">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre:</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $nombre; ?>">
                    </div>
                </div>
                <div class="col-4">
                    <div class="mb-3">
                        <label for="apellido" class="form-label">Apellido:</label>
                        <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo $apellido; ?>">
                    </div>
                </div>
                <div class="col-4">
                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo:</label>
                        <input type="email" class="form-control" id="correo" name="correo" value="<?php echo $correo; ?>">
                    </div>
                </div>
                <div class="col-4">
                    <div class="mb-3">
                        <label for="clave1" class="form-label">Contraseña:</label>
                        <input type="password" class="form-control" id="clave1" name="clave1" value="<?php echo $clave; ?>">
                    </div>
                </div>
                <div class="col-4">
                    <div class="mb-3">
                        <label for="clave2" class="form-label">Repita Contraseña:</label>
                        <input type="password" class="form-control" id="clave2" name="clave2" value="<?php echo $clave; ?>">
                    </div>
                </div>
                <div class="col-4">
                    <div class="mb-3">
                        <label for="perfil" class="form-label">Perfil:</label>
                        <select class="form-control" name="perfil" id="perfil">
                            <?php echo $perfil; ?>
                            <option>Administrador</option>
                            <option>Administrativo</option>
                            <option>Cliente</option>
                            <option>Vendedor</option>
                        </select>
                    </div>
                </div>
                <div class="col-8">
                    <div class="row mb-3">
                        <label class="form-label" for="foto">Guardar Fotografía:</label>
                        <input type="file" class="form-control" id="foto" name="foto">
                    </div>
                </div>
                
                </div>            
                </div>
                <div class="col-3">
                    <div class="row">
                        <div class="col d-grid">
                            <img src="img/usuarios/<?php echo $foto; ?>" class="img-fluid" />
                            <input type="submit" class="btn btn-secondary mt-3" id="subirFoto" name="subirFoto" value="Guardar Foto">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
            <!-- *************  BUSCADOR ******************** -->
                <div class="col-12 d-flex mb-1" role="search"">
                    <input type="text" class="busca form-control me-2" placeholder="Ingrese nombre del Usuario a buscar" id="caja_busqueda">
                    <button class="btn btn-outline-secondary" type="submit" disabled>Buscar</button>
                </div>
            </div>
            <div class="row">
                <div class="col-12" id="mostrar"></div>
            </div>
            <!-- ************* FIN BUSCADOR ***************** -->
            <div class="d-grid gap-1 d-md-flex justify-content-md-end mt-5">
                <input type="submit" class="btn btn-info" id="limpiar" name="limpiar" value="Limpiar">
                <input type="submit" class="btn btn-success" id="crear" name="crear" value="Registrar">
                <input type="submit" class="btn btn-warning" id="modificar" name="modificar" value="Modificar">
                <input type="submit" class="btn btn-danger" id="eliminar" name="eliminar" value="Eliminar">
            </div>
        </form>
    </div>
</div>



<?php
include("footer.php");
?>