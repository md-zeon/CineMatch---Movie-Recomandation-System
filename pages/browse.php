<?php
include '../includes/header.php';
require '../includes/db.php';

// Pagination setup
$limit = 12;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Get total count of movies
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM movies");
$totalMovies = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalMovies / $limit);

// Fetch paginated movies
$query = "SELECT * FROM movies ORDER BY rating DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($query);
?>

<section class="py-12 max-w-7xl mx-auto px-4">
    <h2 class="text-3xl font-bold mb-8">Browse All Movies</h2>

    <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
        <?php while ($movie = $result->fetch_assoc()):
            $genres = explode(',', $movie['genre']);
            $displayGenres = array_slice($genres, 0, 2); ?>
            <div class="card bg-base-100 shadow-md hover:shadow-xl transition-shadow duration-300">
                <figure>
                    <img src="<?= $movie['poster'] ?>" alt="<?= $movie['title'] ?>"
                        class="h-64 w-full object-cover rounded-t-md" />
                </figure>
                <div class="card-body p-4">
                    <h3 class="card-title text-lg font-semibold"><?= $movie['title'] ?></h3>
                    <p class="text-sm text-gray-400 mb-2"><?= mb_strimwidth($movie['description'], 0, 100, "...") ?></p>
                    <div class="flex justify-between items-center flex-wrap gap-2">
                        <div class="flex flex-wrap gap-1">
                            <?php foreach ($displayGenres as $genre): ?>
                                <span class="badge badge-primary text-xs"><?= trim($genre) ?></span>
                            <?php endforeach; ?>
                            <?php if (count($genres) > 2): ?>
                                <span class="badge badge-outline text-xs">+<?= count($genres) - 2 ?> more</span>
                            <?php endif; ?>
                        </div>
                        <span class="text-yellow-400 font-bold text-sm">⭐ <?= number_format($movie['rating'], 2) ?></span>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Pagination -->
<div class="flex justify-center mt-10 space-x-2">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>" class="btn btn-outline btn-primary hover:scale-105 transition-transform duration-300"><i class="fa-solid fa-chevron-left"></i> Prev</a>
    <?php endif; ?>

    <?php
    $range = 2; // how many pages to show on either side of current page
    $start = max(1, $page - $range);
    $end = min($totalPages, $page + $range);

    // Show first page and dots if needed
    if ($start > 1) {
        echo '<a href="?page=1" class="btn btn-outline btn-primary hover:scale-105 transition-transform duration-300">1</a>';
        if ($start > 2) {
            echo '<span class="btn btn-disabled">...</span>';
        }
    }

    // Show page numbers around current page
    for ($i = $start; $i <= $end; $i++) {
        $activeClass = ($i == $page) ? 'btn-primary' : 'btn-outline btn-primary';
        echo "<a href=\"?page=$i\" class=\"btn $activeClass hover:scale-105 transition-transform duration-300\">$i</a>";
    }

    // Show last page and dots if needed
    if ($end < $totalPages) {
        if ($end < $totalPages - 1) {
            echo '<span class="btn btn-disabled">...</span>';
        }
        echo "<a href=\"?page=$totalPages\" class=\"btn btn-outline btn-primary hover:scale-105 transition-transform duration-300\">$totalPages</a>";
    }
    ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?>" class="btn btn-outline btn-primary hover:scale-105 transition-transform duration-300">Next <i class="fa-solid fa-chevron-right"></i></a>
    <?php endif; ?>
</div>

</section>

<?php include '../includes/footer.php'; ?>