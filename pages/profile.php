<?php
include '../includes/header.php';
require '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<section class="pb-12 container mx-auto px-4">
    <h2 class="text-4xl font-bold mb-12 text-center bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
        Your Profile
    </h2>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Profile Info -->
        <div class="lg:col-span-1">
            <div class="card shadow-2xl overflow-hidden">
                <!-- Card Header -->
                <div class="h-32 bg-gradient-to-r from-primary to-secondary relative">
                    <div class="absolute -bottom-12 left-1/2 transform -translate-x-1/2">
                        <div class="w-24 h-24 rounded-full border-4 border-white shadow-lg bg-gray-200 flex items-center justify-center text-3xl font-bold text-primary">
                            <?= strtoupper(substr($user['username'], 0, 1)) ?>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-16 text-center">
                    <h3 class="text-2xl font-semibold"><?= htmlspecialchars($user['username']) ?></h3>
                    <p class="text-gray-500"><?= htmlspecialchars($user['email'] ?? 'Not set') ?></p>
                    <p class="mt-2 text-sm text-gray-400">
                        <i class="fa-solid fa-calendar mr-1"></i>
                        Member since <?= date('F Y', strtotime($user['created_at'] ?? 'now')) ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Watch List -->
        <div class="lg:col-span-2">
            <div class="card bg-base-100 shadow-2xl">
                <div class="card-body">
                    <h3 class="text-2xl font-bold mb-6 flex items-center">
                        <i class="fa-solid fa-heart text-primary mr-3"></i> My Watchlist
                    </h3>

                    <?php
                    $watchlistQuery = "SELECT m.* FROM watchlist w JOIN movies m ON w.movie_id = m.id WHERE w.user_id = ? ORDER BY w.added_at DESC";
                    $stmt = $conn->prepare($watchlistQuery);
                    $stmt->bind_param("i", $_SESSION['user_id']);
                    $stmt->execute();
                    $watchlistResult = $stmt->get_result();
                    ?>

                    <?php if ($watchlistResult->num_rows > 0): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            <?php while ($movie = $watchlistResult->fetch_assoc()): ?>
                                <div class="card bg-base-200 shadow-lg hover:shadow-2xl transition-transform transform hover:scale-[1.02]">
                                    <figure class="relative">
                                        <img src="<?= $movie['poster'] ?>" alt="<?= htmlspecialchars($movie['title']) ?>"
                                            class="h-56 w-full object-cover rounded-t-xl" />
                                        <div class="absolute top-2 right-2 bg-black/70 text-white px-2 py-1 rounded-full text-xs">
                                            ⭐ <?= number_format($movie['rating'], 1) ?>
                                        </div>
                                    </figure>
                                    <div class="card-body p-4">
                                        <h4 class="font-semibold text-lg line-clamp-2">
                                            <a href="/cinematch/pages/movie-details.php?id=<?= $movie['id'] ?>" class="hover:text-primary">
                                                <?= htmlspecialchars($movie['title']) ?>
                                            </a>
                                        </h4>
                                        <p class="text-sm text-gray-500 mt-1">
                                            <?= $movie['release_date'] ? date('Y', strtotime($movie['release_date'])) : 'N/A' ?>
                                        </p>
                                        <div class="card-actions justify-end mt-4">
                                            <form action="/cinematch/watchlist.php" method="POST">
                                                <input type="hidden" name="movie_id" value="<?= $movie['id'] ?>">
                                                <button type="submit" name="remove" value="1"
                                                    class="btn btn-error btn-sm hover:scale-105 transition-transform">
                                                    <i class="fa-solid fa-heart-broken mr-1"></i> Remove
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-16">
                            <div class="text-7xl mb-4">🎬</div>
                            <h4 class="text-xl font-bold mb-2">No movies in your watchlist</h4>
                            <p class="text-gray-500 mb-6">Start adding movies you love and track them here!</p>
                            <a href="/cinematch/pages/browse.php" class="btn btn-primary">
                                <i class="fa-solid fa-film mr-2"></i> Browse Movies
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
