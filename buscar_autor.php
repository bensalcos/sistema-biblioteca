<?php
session_start();
include('conexion.php');

if ($_POST) {
    //Asignamos los datos recibidos en variables locales
    $texto = $_POST['palabra'];
    //Creamos la consulta de BD


    $query= "SELECT id_autor,nombre,apellido,(SELECT count(*) FROM libros WHERE libros.autor = CONCAT(autores.nombre,' ',autores.apellido)) as publicaciones FROM autores WHERE CONCAT(nombre,' ',apellido) LIKE '%" . $texto . "%' ORDER BY nombre LIMIT 10;";

    //Ejecutamos la consulta sobre la conexión
    $sql_res = mysqli_query($connect, $query);

    while ($fila = mysqli_fetch_assoc($sql_res)) {
        $id = $fila['id_autor'];
        $nombre= mb_convert_encoding($fila['nombre'], "UTF-8", "ISO-8859-1");
        $apellido= mb_convert_encoding($fila['apellido'], "UTF-8", "ISO-8859-1");
        $publicaciones = $fila['publicaciones'];
?>




        <a href="autores.php?id=<?php echo $id; ?>" style="text-decoration:none; ">
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <div style="margin-right:6px;"><b><?php echo "</b>Nombre: <b>" . $nombre . ' '. $apellido . "</b>,   Publicaciones disponibles: <b>" . $publicaciones . "</b>" ; ?></b></div>
                </li>
            </ul>
        </a>
<?php
    }
}
?>