<div class="wrapper">

    <div id="sidebar" class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark">
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">


            <span class="fs-4">Sistema Biblioteca</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="inicio.php" class="nav-link active" aria-current="page">
                    <i class="fa-solid fa-house"></i>
                    Inicio
                </a>
            </li>

            <a href="usuarios.php" class="nav-link text-white">
                <i class="fa-solid fa-user"></i> Usuarios
            </a>

            </li>
            <li>
                <a href="libros.php" class="nav-link text-white">
                    <i class="fa-solid fa-book"></i>Libros
                </a>
            </li>
            <li>
                <a href="reportes.php" class="nav-link text-white">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Reportes
                </a>
            </li>
            <li>
                <a href="autores.php" class="nav-link text-white">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Autores
                </a>
            </li>
            <li>
                <a href="editoriales.php" class="nav-link text-white">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Editoriales
                </a>
            </li>
        </ul>
        <hr>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <?php
                echo "<img src='media/img/" . $_SESSION['foto'] . "'  width='32' height='32' class='rounded-circle me-2'>" . '<strong>' . mb_convert_encoding($_SESSION['usuario'], "UTF-8", "ISO-8859-1") . '</strong>';
                ?>

            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">

                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="cerrar.php">Salir</a></li>
            </ul>
        </div>
    </div>













    <div class="content mt-5">