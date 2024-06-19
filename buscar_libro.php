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
            $link = "prestamos.php";
        } else {
            $link = "libros.php";
        }

?>


        <form method="POST" action="<?php echo $link; ?>">
            <input type="hidden" name="id_libro" value="<?php echo $id; ?>">
            <input type="hidden" name="titulo" value="<?php echo $titulo; ?>">
            <input type="hidden" name="autor" value="<?php echo $autor; ?>">
            <input type="hidden" name="editorial" value="<?php echo $editorial; ?>">
            <button type="submit" style="border: none; background: none; padding: 0;">
                <ul class="list-group list-group-flush" style="text-decoration: none;">
                    <li class="list-group-item">
                        <div style="margin-right: 6px;">
                            <b><?php echo "Titulo: <b>" . $titulo . "</b>, Autor: <b>" . $autor . "</b>, Editorial: <b>" . $editorial; ?></b>
                        </div>
                    </li>
                </ul>
            </button>
        </form>
<?php
    } //Cerramos el while
} // Cerramos el if
?>