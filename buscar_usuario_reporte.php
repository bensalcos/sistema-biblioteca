<?php
session_start();
include('conexion.php');

if ($_POST) {
    //Asignamos los datos recibidos en variables locales
    $texto = $_POST['palabra'];
    //Creamos la consulta de BD


    $query = "SELECT id_usuario,nombre,apellido,rut,tipo_usuario,correo FROM usuarios WHERE rut LIKE '%" . $texto . "%' ORDER BY nombre LIMIT 10;";


    //Ejecutamos la consulta sobre la conexión
    $sql_res = mysqli_query($connect, $query);

    while ($fila = mysqli_fetch_assoc($sql_res)) {
        $id_usuario = $fila['id_usuario'];
        $nombre = mb_convert_encoding($fila['nombre'], "UTF-8", "ISO-8859-1");
        $apellido = mb_convert_encoding($fila['apellido'], "UTF-8", "ISO-8859-1");
        $rut = mb_convert_encoding($fila['rut'], "UTF-8", "ISO-8859-1");
        $tipo = mb_convert_encoding($fila['tipo_usuario'], "UTF-8", "ISO-8859-1");
        $correo = mb_convert_encoding($fila['correo'], "UTF-8", "ISO-8859-1");

?>




        <form method="POST" action="<?php echo 'reportes.php'; ?>">
            <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>">
            <input type="hidden" name="nombre" value="<?php echo $nombre; ?>">
            <input type="hidden" name="apellido" value="<?php echo $apellido; ?>">
            <input type="hidden" name="tipo" value="<?php echo $tipo; ?>">
            <input type="hidden" name="rut" value="<?php echo $rut; ?>">
            <input type="hidden" name="correo" value="<?php echo $correo; ?>">
            <input type="hidden" name="detalles" value="">
            <button type="submit" style="border: none; background: none; padding: 0;">
                <ul class="list-group list-group-flush" style="text-decoration: none;">
                    <li class="list-group-item">
                        <div style="margin-right: 6px;">
                            <div style="margin-right:6px;"><b><?php echo "</b>Nombre: <b>" . $nombre . ' ' . $apellido . "</b>, </b>Rut:<b> $rut,   </b>Tipo de usuario: <b>" . $tipo. "</b>, </b>Correo:<b> $correo."; ?></b></div>
                        </div>
                    </li>
                </ul>
            </button>
        </form>

<?php
    } //Cerramos el while
} // Cerramos el if
?>