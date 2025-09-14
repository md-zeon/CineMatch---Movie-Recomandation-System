<?php
include '../includes/header.php';
require '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle POST requests for adding/removing movies
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST received in watchlist.php: " . print_r($_POST, true));
    if (isset($_POST['add']) && isset($_POST['movie_id'])) {
        $movie_id = intval($_POST['movie_id']);
        $insertQuery = "INSERT INTO watchlist (user_id, movie_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE added_at = CURRENT_TIMESTAMP";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ii", $user_id, $movie_id);
        if ($stmt->execute()) {
            error_log("Movie added to watchlist: $movie_id");
        } else {
            error_log("Failed to add movie to watchlist: " . $stmt->error);
        }
    } elseif (isset($_POST['remove']) && isset($_POST['movie_id'])) {
        $movie_id = intval($_POST['movie_id']);
        $deleteQuery = "DELETE FROM watchlist WHERE user_id = ? AND movie_id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("ii", $user_id, $movie_id);
        if ($stmt->execute()) {
            error_log("Movie removed from watchlist: $movie_id");
        } else {
            error_log("Failed to remove movie from watchlist: " . $stmt->error);
        }
    }
    // Redirect back to movie details page
    $movie_id = intval($_POST['movie_id']);
    header("Location: /cinematch/pages/movie-details.php?id=" . $movie_id);
    exit();
}
?>

<section class="py-12 container mx-auto px-4">
    <h2 class="text-3xl font-bold mb-8">My Watchlist</h2>

    <?php
    $watchlistQuery = "SELECT m.*, w.added_at FROM watchlist w JOIN movies m ON w.movie_id = m.id WHERE w.user_id = ? ORDER BY w.added_at DESC";
    $stmt = $conn->prepare($watchlistQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $watchlistResult = $stmt->get_result();
    ?>

    <?php if ($watchlistResult->num_rows > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while ($movie = $watchlistResult->fetch_assoc()): ?>
                <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300">
                    <figure class="relative">
                        <img src="<?= $movie['poster'] ?>" alt="<?= htmlspecialchars($movie['title']) ?>"
                             class="w-full h-64 object-cover rounded-t-xl" />
                        <div class="absolute top-2 right-2 bg-black/70 text-white px-2 py-1 rounded-full text-sm">
                            ⭐ <?= number_format($movie['rating'], 1) ?>
                        </div>
                        <div class="absolute top-2 left-2 bg-primary text-white px-2 py-1 rounded-full text-xs">
                            Added <?= date('M j', strtotime($movie['added_at'])) ?>
                        </div>
                    </figure>
                    <div class="card-body p-4">
                        <h3 class="card-title text-lg font-semibold line-clamp-2">
                            <a href="/cinematch/pages/movie-details.php?id=<?= $movie['id'] ?>" class="hover:text-primary transition-colors">
                                <?= htmlspecialchars($movie['title']) ?>
                            </a>
                        </h3>
                        <p class="text-sm text-gray-600 line-clamp-3 mt-2">
                            <?= htmlspecialchars(substr($movie['description'], 0, 100)) ?>...
                        </p>
                        <div class="card-actions justify-between items-center mt-4">
                            <div class="flex flex-wrap gap-1">
                                <?php
                                $movieGenres = explode(',', $movie['genre']);
                                $displayGenres = array_slice($movieGenres, 0, 2);
                                foreach ($displayGenres as $genre): ?>
                                    <span class="badge badge-primary badge-sm"><?= trim($genre) ?></span>
                                <?php endforeach; ?>
                            </div>
                            <span class="text-xs text-gray-500">
                                <?= $movie['release_date'] ? date('Y', strtotime($movie['release_date'])) : 'N/A' ?>
                            </span>
                        </div>
                        <div class="card-actions justify-end mt-4">
                            <form action="/cinematch/watchlist.php" method="POST" class="inline">
                                <input type="hidden" name="movie_id" value="<?= $movie['id'] ?>">
                                <button type="submit" name="remove" value="1" class="btn btn-error btn-sm hover:scale-105 transition-transform duration-300">
                                    <i class="fa-solid fa-heart-broken mr-1"></i> Remove
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-12">
            <div class="text-6xl mb-4">📽️</div>
            <h3 class="text-2xl font-bold mb-2">Your watchlist is empty</h3>
            <p class="text-gray-400 mb-6">Start adding movies to keep track of what you want to watch!</p>
            <a href="/cinematch/pages/browse.php" class="btn btn-primary">Browse Movies</a>
        </div>
    <?php endif; ?>
</section>

<?php include '../includes/footer.php'; ?>