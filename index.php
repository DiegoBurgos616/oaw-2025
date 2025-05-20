<?php
include 'db.php';

$order = $_GET['order'] ?? 'pub_date';
$direction = $_GET['direction'] ?? 'DESC';
$search = $_GET['search'] ?? '';
$selectedCategory = $_GET['category'] ?? '';

$query = "SELECT * FROM news WHERE title LIKE :search";
$params = [':search' => "%$search%"];

if ($selectedCategory) {
    $query .= " AND category = :category";
    $params[':category'] = $selectedCategory;
}

$query .= " ORDER BY $order $direction";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);

$uniqueNews = [];
foreach ($news as $item) {
    if (!isset($uniqueNews[$item['url']])) {
        $uniqueNews[$item['url']] = $item;
    }
}
$uniqueNews = array_values($uniqueNews);

$recordsPerPage = 9;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$totalNews = count($uniqueNews);
$totalPages = ceil($totalNews / $recordsPerPage);
$newsPage = array_slice($uniqueNews, ($page - 1) * $recordsPerPage, $recordsPerPage);

$categories = [];
foreach ($uniqueNews as $item) {
    if (!in_array($item['category'], $categories)) {
        $categories[] = $item['category'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>RSS Handler</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="./index.css">
</head>

<body>
    <div class="container-fluid">
        <div class="main-container">
            <!-- Barra Lateral -->
            <nav class="sidebar col-md-3 col-lg-2 min-vh-100">
                <div class="px-2">
                    <h3>Categorías</h3>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2">
                            <a class="nav-link <?= empty($selectedCategory) ? 'active' : '' ?>"
                                href="index.php?<?= http_build_query(array_merge($_GET, ['category' => ''])) ?>">Todas</a>
                        </li>
                        <?php foreach ($categories as $cat): ?>
                            <li class="nav-item mb-2">
                                <a class="nav-link <?= $selectedCategory === $cat ? 'active' : '' ?>"
                                    href="index.php?<?= http_build_query(array_merge($_GET, ['category' => $cat])) ?>">
                                    <?= htmlspecialchars($cat) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </nav>

            <!-- Contenido Principal -->
            <main class="flex-grow-1 px-md-4">
                <h1 class="mt-4">ManejadorRSS</h1>

                <!-- Formulario para actualizar noticias desde un RSS -->
                <form id="rssForm" class="mb-4">
                    <div class="input-group">
                        <input type="text" id="feedUrl" name="feed_url" class="form-control"
                            placeholder="URL del feed RSS" required>
                        <button type="submit" class="btn btn-primary">Actualizar Noticias</button>
                    </div>
                </form>

                <div id="alertMessage" class="alert d-none" role="alert"></div>

                <!-- Buscador y Filtros -->
                <form action="index.php" method="GET" class="mb-4">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Buscar noticias"
                                value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="order" class="form-select">
                                <option value="pub_date" <?= $order == 'pub_date' ? 'selected' : '' ?>>Fecha</option>
                                <option value="title" <?= $order == 'title' ? 'selected' : '' ?>>Título</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="direction" class="form-select">
                                <option value="ASC" <?= $direction == 'ASC' ? 'selected' : '' ?>>Ascendente</option>
                                <option value="DESC" <?= $direction == 'DESC' ? 'selected' : '' ?>>Descendente</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary w-100">Aplicar Filtros</button>
                        </div>
                    </div>
                    <?php if ($selectedCategory): ?>
                        <input type="hidden" name="category" value="<?= htmlspecialchars($selectedCategory) ?>">
                    <?php endif; ?>
                </form>


                <img src="./banner.jpg" class="BannerImage">
                <hr>
                <!-- Noticias en formato Card -->
                <div class="row">
                    <?php if (empty($newsPage)): ?>
                        <div class="col-12">
                            <p>No se encontraron noticias.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($newsPage as $item): ?>
                            <?php

                            /*
                            // Extraer la URL de la imagen desde la etiqueta <img> en la descripción
                            if (preg_match('/<img.*?src=["\']([^"\']+)["\']/si', $item['description'], $matches)) {
                                $imageUrl = $matches[1];
                            } 
                            elseif(preg_match('/<enclosure.*?url=["\']([^"\']+)["\']/i', $item['image_url'], $matches)){
                                $imageUrl = $matches[1];
                            }
                            /*elseif (isset($item['enclosure']) && is_string($item['enclosure']) && 
                                preg_match('/url=["\']([^"\']+)["\']/', $item['enclosure'], $matches)) {
                                $imageUrl = $matches[1];
                            }
                            else {
                                $imageUrl = 'https://placehold.co/300x200?text=No+Image';
                            }

                            */

                            if ($item['image_url'] != '') {
                                $imageUrl = $item['image_url'];
                            } else {
                                $imageUrl = 'https://placehold.co/300x200?text=No+Image';
                            }


                            /*
                            if (isset($imageUrl)) {
                                echo '<p>Imagen encontrada:</p>';
                                echo '<img src="' . htmlspecialchars($imageUrl) . '" alt="Test" style="max-width: 300px;">';
                            } else {
                                echo '<p>No se encontró imagen.</p>';
                            }
                            echo '<p>Intentando descargar desde: ' . htmlspecialchars($imageUrl) . '</p>';
                            */
                            // Quitar la etiqueta <img> para obtener el texto de la descripción
                            $descriptionWithoutImage = preg_replace('/<img[^>]+\>/', '', $item['description']);
                            $textDescription = strip_tags($descriptionWithoutImage);
                            $shortDescription = (strlen($textDescription) > 100) ? substr($textDescription, 0, 100) . '...' : $textDescription;
                            ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card news-card">
                                    <img src="<?= htmlspecialchars($imageUrl) ?>" class="card-img-top"
                                        alt="Imagen de <?= htmlspecialchars($item['title']) ?>">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">
                                            <a href="<?= htmlspecialchars($item['url']) ?>" target="_blank">
                                                <?= htmlspecialchars($item['title']) ?>
                                            </a>
                                        </h5>
                                        <h6 class="card-subtitle mb-2 text-muted"><?= $item['pub_date'] ?></h6>
                                        <p class="card-text"><?= htmlspecialchars($shortDescription) ?></p>
                                        <div class="mt-auto">
                                            <span class="badge bg-secondary"><?= htmlspecialchars($item['category']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Paginación -->
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link"
                                    href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>"
                                    aria-label="Anterior">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                                <a class="page-link"
                                    href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link"
                                    href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>"
                                    aria-label="Siguiente">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <div class="row">
                <h2>Integrantes</h2>
                <div class="col-md-6 col-lg-3">
                    <div class="card news-card">
                        <img src="./pato.jpg" class="card-img-top" alt="Imagen de Pato" />
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                                Pato
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card news-card">
                        <img src="./Martin.jpg" class="card-img-top" alt="Imagen de Martin" />
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                                Martin
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card news-card">
                        <img src="./Wil.jpg" class="card-img-top" alt="Imagen de Wil" />
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                                Wil
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card news-card">
                        <img src="./Diego.jpg" class="card-img-top" alt="Imagen de Diego" />
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                                Diego
                            </h5>
                        </div>
                    </div>
                </div>

            </main>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#rssForm").submit(function (event) {
                event.preventDefault();

                let feedUrl = $("#feedUrl").val();
                if (!feedUrl) return;

                $.ajax({
                    url: "feed_handler.php",
                    type: "POST",
                    data: { feed_url: feedUrl },
                    dataType: "json",
                    success: function (response) {
                        $("#feedUrl").val("");
                        let iconType = response.status === "success" ? "success" : (response.status === "info" ? "info" : "error");
                        Swal.fire({
                            title: response.message,
                            icon: iconType,
                            timer: 3000,
                            showConfirmButton: false
                        });
                    },
                    error: function () {
                        Swal.fire({
                            title: "Error al actualizar las noticias",
                            icon: "error",
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>