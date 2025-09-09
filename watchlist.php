<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /cinematch/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$movie_id = $_POST['movie_id'] ?? 0;

if (!$movie_id) {
    header("Location: /cinematch/index.php");
    exit();
}

if (isset($_POST['add'])) {
    // Add to watchlist
    $query = "INSERT INTO watchlist (user_id, movie_id, added_at) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $movie_id);
    $stmt->execute();
} elseif (isset($_POST['remove'])) {
    // Remove from watchlist
    $query = "DELETE FROM watchlist WHERE user_id = ? AND movie_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $movie_id);
    $stmt->execute();
}

// Redirect back to movie details
header("Location: /cinematch/pages/movie-details.php?id=" . $movie_id);
exit();
?>
