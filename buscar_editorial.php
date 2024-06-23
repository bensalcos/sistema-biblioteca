<?php
session_start();
include('conexion.php');

if ($_POST) {
    //Asignamos los datos recibidos en variables locales
    $texto = $_POST['palabra'];
    //Creamos la consulta de BD

    $query = "SELECT id_editorial,nombre,(SELECT count(*) FROM libros WHERE libros.editorial = editoriales.nombre) as publicaciones FROM editoriales WHERE nombre LIKE '%" . $texto . "%' ORDER BY nombre LIMIT 10;";



    //Ejecutamos la consulta sobre la conexión
    $sql_res = mysqli_query($connect, $query);

    while ($fila = mysqli_fetch_assoc($sql_res)) {
        $id_editorial = $fila['id_editorial'];
        $nombre = mb_convert_encoding($fila['nombre'], "UTF-8", "ISO-8859-1");
        $publicaciones = $fila['publicaciones'];

?>

        <form method="POST" action="<?php echo 'editoriales.php'; ?>">
            <input type="hidden" name="id_editorial" value="<?php echo $id_editorial; ?>">
            <input type="hidden" name="nombre" value="<?php echo $nombre; ?>">
            <input type="hidden" name="publicaciones" value="<?php echo $publicaciones; ?>">
            <button type="submit" style="border: none; background: none; padding: 0;">
                <ul class="list-group list-group-flush" style="text-decoration: none;">
                    <li class="list-group-item">
                        <div style="margin-right: 6px;">
                            <b><?php echo "</b>Nombre: <b>" . $nombre . "</b>,   Publicaciones disponibles: <b>" . $publicaciones . "</b>"; ?></b>
                        </div>
                    </li>
                </ul>
            </button>
        </form>

<?php
    } //Cerramos el while
} // Cerramos el if
?>