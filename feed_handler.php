<?php
include './db.php';
require './SimplePie/autoloader.php';

header('Content-Type: application/json');

$feed_url = $_POST['feed_url'] ?? '';

if (!$feed_url) {
    echo json_encode(['status' => 'error', 'message' => 'URL del feed no proporcionada.']);
    exit;
}

$feed = new SimplePie();
$feed->set_feed_url($feed_url);
$feed->init();

$addedNews = [];

foreach ($feed->get_items() as $item) {
    $title = $item->get_title();
    $url = $item->get_permalink();
    $description = strip_tags($item->get_description());
    $category = $item->get_category() ? $item->get_category()->get_label() : 'General';
    $pub_date = date('Y-m-d H:i:s', strtotime($item->get_date()));

    // Extraer imagen desde <enclosure> o desde el HTML del <description>
    $imageUrl = '';
    $enclosure = $item->get_enclosure();
    if ($enclosure && $enclosure->get_link()) {
        $imageUrl = $enclosure->get_link();
    } else {
        $descRaw = $item->get_description();
        if (preg_match('/<img.*?src=["\']([^"\']+)["\']/si', $descRaw, $matches)) {
            $imageUrl = $matches[1];
        }
    }

    // Evitar duplicados
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM news WHERE url = :url");
    $stmt->execute([':url' => $url]);
    if ($stmt->fetchColumn() > 0) {
        continue;
    }
    if (empty($imageUrl)) {
        continue; // Skip if there's no image
    }

    // Insertar en la base de datos incluyendo image_url
    $stmt = $pdo->prepare("INSERT INTO news (title, url, description, category, pub_date, source_url, image_url) 
                            VALUES (:title, :url, :description, :category, :pub_date, :source_url, :image_url)");
    $stmt->execute([
        ':title' => $title,
        ':url' => $url,
        ':description' => $description,
        ':category' => $category,
        ':pub_date' => $pub_date,
        ':source_url' => $feed_url,
        ':image_url' => $imageUrl
    ]);

    $addedNews[] = [
        'title' => $title,
        'url' => $url,
        'description' => $description,
        'category' => $category,
        'pub_date' => $pub_date,
        'image_url' => $imageUrl
    ];
}

if (count($addedNews) > 0) {
    echo json_encode(['status' => 'success', 'message' => 'Noticias agregadas correctamente.', 'news' => $addedNews]);
} else {
    echo json_encode(['status' => 'info', 'message' => 'No hay nuevas noticias para agregar.']);
}
?>
