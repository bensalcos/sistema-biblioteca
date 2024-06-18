<?php
session_start();
include('conexion.php');

if ($_POST) {
    //Asignamos los datos recibidos en variables locales
    $texto = $s = explode('=', $_POST['cadena'])[1];
    $tipo = $_POST['tipo'];
    //Creamos la consulta de BD


    $query = "SELECT id_libro, titulo, autor, editorial, fecha_publicacion,stock FROM libros WHERE titulo LIKE '%" . $texto . "%' ORDER BY titulo LIMIT 10;";


    //Ejecutamos la consulta sobre la conexión
    $sql_res = mysqli_query($connect, $query);

    while ($fila = mysqli_fetch_assoc($sql_res)) {
        $id = $fila['id_libro'];
        $titulo = mb_convert_encoding($fila['titulo'], "UTF-8", "ISO-8859-1");
        $autor = mb_convert_encoding($fila['autor'], "UTF-8", "ISO-8859-1");
        $editorial = mb_convert_encoding($fila['editorial'], "UTF-8", "ISO-8859-1");
        $fecha = $fila['fecha_publicacion'];
        $stock = $fila['stock'];


    if ($tipo == 'prestamo') {
        $link = "prestamos.php?id_libro=" . $id;
    } else {
        $link = "libros.php?id=" . $id;
    }

?>




        <a href="<?php echo $link ?>" style="text-decoration:none; ">
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <div style="margin-right:6px;"><b><?php echo "</b>Titulo: <b>" . $titulo . "</b>,   Autor: <b>" . $autor . "</b>,   Editorial: <b>" . $editorial; ?></b></div>
                </li>
            </ul>
        </a>
<?php
    } //Cerramos el while
} // Cerramos el if
?>