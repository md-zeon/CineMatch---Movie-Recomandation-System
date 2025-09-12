<?php
require '../includes/db.php';

$movie_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['review_text']) && isset($_SESSION['user_id'])) {
    $review_text = trim($_POST['review_text']);
    $rating = isset($_POST['rating']) && $_POST['rating'] !== "" && is_numeric($_POST['rating']) ? floatval($_POST['rating']) : null;

    if ($movie_id > 0 && !empty($review_text)) {
        if ($rating === null) {
            $stmt = $conn->prepare("INSERT INTO reviews (movie_id, user_id, review_text) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $movie_id, $_SESSION['user_id'], $review_text);
        } else {
            $stmt = $conn->prepare("INSERT INTO reviews (movie_id, user_id, review_text, rating) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iisd", $movie_id, $_SESSION['user_id'], $review_text, $rating);
        }
        $stmt->execute();
        $stmt->close();
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}

include '../includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<div class="alert alert-error">Invalid movie ID.</div>';
    include '../includes/footer.php';
    exit;
}

// $movie_id = intval($_GET['id']);
$query = "SELECT * FROM movies WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo '<div class="alert alert-error">Movie not found.</div>';
    include '../includes/footer.php';
    exit;
}

$movie = $result->fetch_assoc();
?>

<!-- Back Button -->
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center">
        <a href="/cinematch/pages/browse.php"
            class="btn btn-outline btn-primary hover:scale-105 transition-transform duration-300">
            <i class="fa-solid fa-arrow-left mr-2"></i> Back to Browse
        </a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <?php
            // Check if movie is in user's watchlist
            $watchlistQuery = "SELECT id FROM watchlist WHERE user_id = ? AND movie_id = ?";
            $stmt = $conn->prepare($watchlistQuery);
            $stmt->bind_param("ii", $_SESSION['user_id'], $movie_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $inWatchlist = $result->num_rows > 0;

            ?>
            <form action="/cinematch/watchlist.php" method="POST" class="inline">
                <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
                <?php if ($inWatchlist): ?>
                    <button type="submit" name="remove" value="1"
                        class="btn btn-error hover:scale-105 transition-transform duration-300">
                        <i class="fa-solid fa-heart mr-2"></i> Remove from Watchlist
                    </button>
                <?php else: ?>
                    <button type="submit" name="add" value="1"
                        class="btn btn-success hover:scale-105 transition-transform duration-300">
                        <i class="fa-solid fa-heart mr-2"></i> Add to Watchlist
                    </button>
                <?php endif; ?>
            </form>
        <?php endif; ?>
    </div>
</div>

<section class="relative overflow-hidden">
    <!-- Hero Section -->
    <div class="relative h-[600px] bg-cover bg-center bg-no-repeat"
        style="background-image: url('<?= $movie['backdrop'] ?>');">
        <!-- Multiple gradient overlays for depth -->
        <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/60 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-black/30"></div>
        <!-- Animated particles effect -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-1/4 left-1/4 w-2 h-2 bg-white rounded-full animate-pulse"></div>
            <div class="absolute top-1/3 right-1/3 w-1 h-1 bg-white rounded-full animate-pulse delay-1000"></div>
            <div class="absolute bottom-1/4 left-1/2 w-1.5 h-1.5 bg-white rounded-full animate-pulse delay-500"></div>
        </div>
    </div>

    <!-- Movie Info Section -->
    <div class="container mx-auto px-4 relative -mt-48">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Poster with enhanced styling -->
            <div class="flex justify-center lg:justify-start">
                <div class="relative group">
                    <img src="<?= $movie['poster'] ?>" alt="<?= htmlspecialchars($movie['title']) ?>"
                        class="w-80 rounded-2xl shadow-2xl border-4 border-white/20 transform group-hover:scale-105 transition-all duration-500" />
                    <div
                        class="absolute inset-0 rounded-2xl bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    </div>
                </div>
            </div>

            <!-- Details with enhanced typography -->
            <div class="lg:col-span-2 text-white">
                <div class="space-y-4">
                    <h1
                        class="text-5xl lg:text-6xl font-black bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent leading-tight">
                        <?= htmlspecialchars($movie['title']) ?>
                        <?php if ($movie['release_date']): ?>
                            <span
                                class="text-gray-400 text-3xl block lg:inline lg:ml-4">(<?= date('Y', strtotime($movie['release_date'])) ?>)</span>
                        <?php endif; ?>
                    </h1>

                    <?php if ($movie['tagline']): ?>
                        <p class="text-xl italic text-gray-300 mt-4 font-light">"<?= htmlspecialchars($movie['tagline']) ?>"
                        </p>
                    <?php endif; ?>

                    <!-- Enhanced Quick Stats -->
                    <div class="flex flex-wrap items-center gap-6 mt-6">
                        <div class="flex items-center gap-2 bg-black/30 backdrop-blur-sm rounded-full px-4 py-2">
                            <i class="fa-solid fa-star text-yellow-400"></i>
                            <span class="text-lg font-semibold"><?= number_format($movie['rating'], 1) ?></span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-300">
                            <i class="fa-solid fa-users"></i>
                            <span><?= number_format($movie['vote_count']) ?> votes</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-300">
                            <i class="fa-solid fa-clock"></i>
                            <span><?= $movie['runtime'] ? $movie['runtime'] . ' min' : 'N/A' ?></span>
                        </div>
                    </div>

                    <!-- Enhanced Information Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-8">
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                            <div class="flex items-center gap-3 mb-2">
                                <i class="fa-solid fa-film text-primary"></i>
                                <span class="font-semibold">Genres</span>
                            </div>
                            <p class="text-gray-200"><?= htmlspecialchars($movie['genre']) ?></p>
                        </div>

                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                            <div class="flex items-center gap-3 mb-2">
                                <i class="fa-solid fa-calendar text-primary"></i>
                                <span class="font-semibold">Release Date</span>
                            </div>
                            <p class="text-gray-200">
                                <?= $movie['release_date'] ? date('F j, Y', strtotime($movie['release_date'])) : 'N/A' ?>
                            </p>
                        </div>

                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                            <div class="flex items-center gap-3 mb-2">
                                <i class="fa-solid fa-language text-primary"></i>
                                <span class="font-semibold">Language</span>
                            </div>
                            <p class="text-gray-200"><?= htmlspecialchars($movie['language']) ?></p>
                        </div>

                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                            <div class="flex items-center gap-3 mb-2">
                                <i class="fa-solid fa-user-tie text-primary"></i>
                                <span class="font-semibold">Director</span>
                            </div>
                            <p class="text-gray-200"><?= htmlspecialchars($movie['director']) ?: 'N/A' ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </div>

    <!-- Reviews Section -->
    <div class="mt-8">
        <div class="card bg-base-100 shadow-xl border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-3xl mb-6">
                    <i class="fa-solid fa-comments text-primary mr-3"></i>
                    Reviews
                </h2>

                <?php
                // Fetch reviews
                $reviewsQuery = "SELECT r.*, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.movie_id = ? ORDER BY r.created_at DESC";
                $stmt = $conn->prepare($reviewsQuery);
                $stmt->bind_param("i", $movie_id);
                $stmt->execute();
                $reviewsResult = $stmt->get_result();
                ?>

                <?php if ($reviewsResult->num_rows > 0): ?>
                    <div class="space-y-6">
                        <?php while ($review = $reviewsResult->fetch_assoc()): ?>
                            <div class="bg-gray-50 p-4 rounded-xl border-l-4 border-primary">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-user-circle text-2xl text-gray-600"></i>
                                        <span
                                            class="font-semibold text-gray-800"><?= htmlspecialchars($review['username']) ?></span>
                                        <?php if ($review['rating']): ?>
                                            <span class="text-yellow-500">
                                                <i class="fa-solid fa-star"></i> <?= number_format($review['rating'], 1) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <span
                                        class="text-sm text-gray-500"><?= date('M j, Y', strtotime($review['created_at'])) ?></span>
                                </div>
                                <p class="text-gray-700 leading-relaxed"><?= nl2br(htmlspecialchars($review['review_text'])) ?>
                                </p>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <i class="fa-solid fa-comments text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">No reviews yet. Be the first to review this movie!</p>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="mt-8 border-t pt-6">
                        <h3 class="text-xl font-semibold mb-4">Write a Review</h3>
                        <form method="POST" class="space-y-4">
                            <div>
                                <label for="review_text" class="block text-sm font-medium text-gray-700 mb-2">Your
                                    Review</label>
                                <textarea id="review_text" name="review_text" rows="4"
                                    class="textarea textarea-bordered w-full"
                                    placeholder="Share your thoughts about this movie..." required></textarea>
                            </div>
                            <div>
                                <label for="rating" class="block text-sm font-medium text-gray-700 mb-2">Rating
                                    (optional)</label>
                                <select id="rating" name="rating" class="select select-bordered w-full">
                                    <option value="">No rating</option>
                                    <option value="1">1 - Poor</option>
                                    <option value="2">2 - Fair</option>
                                    <option value="3">3 - Good</option>
                                    <option value="4">4 - Very Good</option>
                                    <option value="5">5 - Excellent</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-paper-plane mr-2"></i> Submit Review
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="mt-8 border-t pt-6 text-center">
                        <p class="text-gray-600">Please <a href="/cinematch/login.php"
                                class="text-primary hover:underline">log in</a> to write a review.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Enhanced Content Sections -->
<div class="container mx-auto px-4 mt-16">
    <!-- name of each tab group should be unique -->
    <div class="tabs tabs-box">
        <input type="radio" name="my_tabs_6" class="tab" aria-label="Overview" />
        <!-- Overview Section -->
        <div class="tab-content">
            <div class="card bg-base-100 shadow-xl border border-base-300">
                <div class="card-body">
                    <h2 class="card-title text-3xl mb-6">
                        <i class="fa-solid fa-book-open text-primary mr-3"></i>
                        Overview
                    </h2>
                    <div class="prose prose-lg max-w-none">
                        <p class="text-gray-700 leading-relaxed text-lg">
                            <?= nl2br(htmlspecialchars($movie['description'])) ?>
                        </p>
                    </div>

                    <!-- Additional Info Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                        <?php if ($movie['country']): ?>
                            <div class="stat bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl">
                                <div class="stat-figure text-white">
                                    <i class="fa-solid fa-globe text-2xl"></i>
                                </div>
                                <div class="stat-title text-blue-100">Country</div>
                                <div class="stat-value text-xl"><?= htmlspecialchars($movie['country']) ?></div>
                            </div>
                        <?php endif; ?>

                        <div class="stat bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl">
                            <div class="stat-figure text-white">
                                <i class="fa-solid fa-chart-line text-2xl"></i>
                            </div>
                            <div class="stat-title text-green-100">Popularity</div>
                            <div class="stat-value text-xl"><?= number_format($movie['popularity'], 1) ?></div>
                        </div>

                        <?php if ($movie['original_title'] && $movie['original_title'] != $movie['title']): ?>
                            <div class="stat bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl">
                                <div class="stat-figure text-white">
                                    <i class="fa-solid fa-language text-2xl"></i>
                                </div>
                                <div class="stat-title text-purple-100">Original Title</div>
                                <div class="stat-value text-lg text-center">
                                    <?= htmlspecialchars($movie['original_title']) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <input type="radio" name="my_tabs_6" class="tab" aria-label="Cast" />
        <!-- Cast Section -->
        <div class="tab-content">
            <div class="card bg-base-100 shadow-xl border border-base-300">
                <div class="card-body">
                    <h2 class="card-title text-3xl mb-6">
                        <i class="fa-solid fa-users text-primary mr-3"></i>
                        Cast & Crew
                    </h2>
                    <div class="bg-base-300 text-base-content p-6 rounded-xl border-l-4 border-primary">
                        <div class="flex items-start gap-4">
                            <i class="fa-solid fa-user-group text-3xl text-primary mt-1"></i>
                            <div class="flex-1">
                                <p class="leading-relaxed text-lg whitespace-pre-line">
                                    <?= htmlspecialchars($movie['cast']) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="radio" name="my_tabs_6" class="tab" aria-label="Trailer" checked="checked" />
        <!-- Trailer Section -->
        <div class="tab-content">
            <div class="card bg-base-100 shadow-xl border border-base-300">
                <div class="card-body">
                    <h2 class="card-title text-3xl mb-6">
                        <i class="fa-solid fa-play-circle text-primary mr-3"></i>
                        Official Trailer
                    </h2>
                    <div class="relative group">
                        <div class="aspect-video bg-black rounded-2xl overflow-hidden shadow-2xl">
                            <?php
                            function youtubeEmbedUrl($url)
                            {
                                if (strpos($url, 'watch?v=') !== false) {
                                    return str_replace("watch?v=", "embed/", $url);
                                }
                                return $url;
                            }
                            ?>

                            <iframe src="<?= htmlspecialchars(youtubeEmbedUrl($movie['trailer_url'])) ?>"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen class="w-full h-full rounded-2xl"></iframe>
                        </div>
                        <div
                            class="absolute inset-0 rounded-2xl bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Similar Movies Section -->
    <div class="mt-16">
        <h2 class="text-3xl font-bold mb-8 text-center">
            <i class="fa-solid fa-film text-primary mr-3"></i>
            Similar Movies
        </h2>

        <?php
        // Get similar movies based on genre
        $genres = explode(',', $movie['genre']);
        $genreCondition = "'" . implode("','", array_map('trim', $genres)) . "'";
        $similarQuery = "SELECT * FROM movies WHERE id != ? AND (";
        $params = [$movie_id];
        $conditions = [];

        foreach ($genres as $genre) {
            $conditions[] = "genre LIKE ?";
            $params[] = '%' . trim($genre) . '%';
        }

        $similarQuery .= implode(' OR ', $conditions) . ") ORDER BY rating DESC LIMIT 6";
        $stmt = $conn->prepare($similarQuery);
        $types = 'i' . str_repeat('s', count($genres)); // movie_id is int, genres are strings
        $stmt->bind_param($types, ...$params);
        if (!$stmt->execute()) {
            error_log("Similar movies query failed: " . $stmt->error);
        }
        $similarResult = $stmt->get_result();
        ?>

        <?php if ($similarResult->num_rows > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while ($similarMovie = $similarResult->fetch_assoc()): ?>
                    <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105">
                        <figure class="relative">
                            <img src="<?= $similarMovie['poster'] ?>" alt="<?= htmlspecialchars($similarMovie['title']) ?>"
                                class="w-full h-64 object-cover rounded-t-xl" />
                            <div class="absolute top-2 right-2 bg-black/70 text-white px-2 py-1 rounded-full text-sm">
                                ⭐ <?= number_format($similarMovie['rating'], 1) ?>
                            </div>
                        </figure>
                        <div class="card-body p-4">
                            <h3 class="card-title text-lg font-semibold line-clamp-2">
                                <a href="/cinematch/pages/movie-details.php?id=<?= $similarMovie['id'] ?>"
                                    class="hover:text-primary transition-colors">
                                    <?= htmlspecialchars($similarMovie['title']) ?>
                                </a>
                            </h3>
                            <p class="text-sm text-gray-600 line-clamp-3 mt-2">
                                <?= htmlspecialchars(substr($similarMovie['description'], 0, 100)) ?>...
                            </p>
                            <div class="card-actions justify-between items-center mt-4">
                                <div class="flex flex-wrap gap-1">
                                    <?php
                                    $movieGenres = explode(',', $similarMovie['genre']);
                                    $displayGenres = array_slice($movieGenres, 0, 2);
                                    foreach ($displayGenres as $genre): ?>
                                        <span class="badge badge-primary badge-sm"><?= trim($genre) ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <span class="text-xs text-gray-500">
                                    <?= $similarMovie['release_date'] ? date('Y', strtotime($similarMovie['release_date'])) : 'N/A' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🎬</div>
                <h3 class="text-2xl font-bold mb-2">No Similar Movies Found</h3>
                <p class="text-gray-400">We couldn't find similar movies at this time.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>