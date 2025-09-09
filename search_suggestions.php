<?php
require 'includes/db.php';

if (isset($_GET['query'])) {
    $query = trim($_GET['query']);
    if (strlen($query) > 0) {
        $stmt = $conn->prepare("SELECT title, poster FROM movies WHERE title LIKE CONCAT('%', ?, '%') ORDER BY rating DESC LIMIT 10");
        $stmt->bind_param("s", $query);
        $stmt->execute();
        $result = $stmt->get_result();

        $suggestions = [];
        while ($row = $result->fetch_assoc()) {
            $suggestions[] = [
                'title' => $row['title'],
                'poster' => $row['poster']
            ];
        }

        echo json_encode($suggestions);
    } else {
        echo json_encode([]);
    }
}
?>
