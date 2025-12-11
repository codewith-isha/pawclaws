<?php
header('Content-Type: application/json');

$file = 'blog-data.json';
$data = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

$blogId = $_GET['id'] ?? '';
$action = $_GET['action'] ?? '';
$comment = $_POST['comment'] ?? '';

if (!$blogId) {
    echo json_encode(['error' => 'Missing blog ID']);
    exit;
}

if (!isset($data[$blogId])) {
    $data[$blogId] = ['views' => 0, 'likes' => 0, 'comments' => []];
}

switch ($action) {
    case 'view':
        $data[$blogId]['views']++;
        break;
    case 'like':
        $data[$blogId]['likes']++;
        break;
    case 'comment':
        if ($comment) {
            $data[$blogId]['comments'][] = htmlspecialchars($comment);
        }
        break;
}
file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
echo json_encode($data[$blogId]);
?>