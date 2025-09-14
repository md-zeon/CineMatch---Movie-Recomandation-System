<?php
include '../includes/header.php';
require '../includes/db.php';

// Get all unique genres
$genresQuery = "SELECT DISTINCT TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(genre, ',', n.digit+1), ',', -1)) as genre_name
                FROM movies
                CROSS JOIN (
                    SELECT 0 as digit UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL 
                    SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL 
                    SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9
                ) as n
                WHERE 
                    LENGTH(genre) - LENGTH(REPLACE(genre, ',', '')) >= n.digit
                    AND TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(genre, ',', n.digit+1), ',', -1)) != ''
                ORDER BY genre_name";
$genresResult = $conn->query($genresQuery);

$allGenres = [];
while ($row = $genresResult->fetch_assoc()) {
    $allGenres[] = trim($row['genre_name']);
}
$allGenres = array_unique($allGenres);
sort($allGenres);

// Initialize
$results = [];
$totalMovies = 0;
$searchPerformed = false;
$errors = [];

// Process search
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchPerformed = true;

    // Input values
    $title = trim($_POST['title'] ?? '');
    $selectedGenre = $_POST['genre'] ?? '';
    $minRating = isset($_POST['min_rating']) ? floatval($_POST['min_rating']) : 0;
    $maxRating = isset($_POST['max_rating']) ? floatval($_POST['max_rating']) : 10;
    $sortBy = $_POST['sort_by'] ?? 'rating';
    $sortOrder = $_POST['sort_order'] ?? 'DESC';
    $releaseFrom = $_POST['release_from'] ?? '';
    $releaseTo = $_POST['release_to'] ?? '';

    // Validation
    if (strlen($title) > 100)
        $errors[] = "Title is too long (max 100 characters).";
    if ($minRating < 0 || $minRating > 10)
        $errors[] = "Min rating must be between 0 and 10.";
    if ($maxRating < 0 || $maxRating > 10)
        $errors[] = "Max rating must be between 0 and 10.";
    if ($minRating > $maxRating)
        $errors[] = "Min rating cannot be greater than max rating.";
    if ($releaseFrom && !preg_match('/^\d{4}$/', $releaseFrom))
        $errors[] = "Release From year must be 4 digits.";
    if ($releaseTo && !preg_match('/^\d{4}$/', $releaseTo))
        $errors[] = "Release To year must be 4 digits.";
    if ($releaseFrom && $releaseTo && $releaseFrom > $releaseTo)
        $errors[] = "Release From year cannot be greater than Release To year.";

    if (empty($errors)) {
        // Build query
        $query = "SELECT * FROM movies WHERE 1=1";
        $params = [];
        $types = '';

        if (!empty($title)) {
            $query .= " AND title LIKE ?";
            $params[] = "%$title%";
            $types .= 's';
        }

        if (!empty($selectedGenre)) {
            $query .= " AND genre LIKE ?";
            $params[] = "%$selectedGenre%";
            $types .= 's';
        }

        $query .= " AND rating BETWEEN ? AND ?";
        $params[] = $minRating;
        $params[] = $maxRating;
        $types .= 'dd';

        if ($releaseFrom) {
            $query .= " AND YEAR(release_date) >= ?";
            $params[] = $releaseFrom;
            $types .= 'i';
        }
        if ($releaseTo) {
            $query .= " AND YEAR(release_date) <= ?";
            $params[] = $releaseTo;
            $types .= 'i';
        }

        // Validate sort
        $validSortColumns = ['title', 'rating', 'popularity', 'release_date'];
        $validSortOrders = ['ASC', 'DESC'];
        if (!in_array($sortBy, $validSortColumns))
            $sortBy = 'rating';
        if (!in_array($sortOrder, $validSortOrders))
            $sortOrder = 'DESC';

        $query .= " ORDER BY $sortBy $sortOrder";

        // Execute
        $stmt = $conn->prepare($query);
        if (!empty($params))
            $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $totalMovies = $result->num_rows;
        while ($movie = $result->fetch_assoc())
            $results[] = $movie;
    }
}
?>

<section class="py-12 container mx-auto px-4">
    <h2 class="text-3xl font-bold mb-8">Advanced Movie Search</h2>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Search Form -->
        <div class="lg:col-span-1 sticky top-20 self-start">
            <div class="bg-base-200 p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-4">Search Filters</h3>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error mb-4">
                        <ul class="list-disc pl-4">
                            <?php foreach ($errors as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="space-y-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Movie Title</span></label>
                        <input type="text" name="title" placeholder="Enter movie title..."
                            class="input input-bordered w-full" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Genre</span></label>
                        <select name="genre" class="select select-bordered w-full">
                            <option value="">All Genres</option>
                            <?php foreach ($allGenres as $genre): ?>
                                <option value="<?= $genre ?>" <?= (($_POST['genre'] ?? '') === $genre) ? 'selected' : '' ?>>
                                    <?= $genre ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Rating Range</span></label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="min_rating" min="0" max="10" step="0.1"
                                class="input input-bordered w-full" placeholder="Min"
                                value="<?= htmlspecialchars($_POST['min_rating'] ?? '0') ?>">
                            <span>to</span>
                            <input type="number" name="max_rating" min="0" max="10" step="0.1"
                                class="input input-bordered w-full" placeholder="Max"
                                value="<?= htmlspecialchars($_POST['max_rating'] ?? '10') ?>">
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Release Year</span></label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="release_from" min="1900" max="<?= date('Y') ?>"
                                placeholder="From" class="input input-bordered w-full"
                                value="<?= htmlspecialchars($_POST['release_from'] ?? '') ?>">
                            <span>to</span>
                            <input type="number" name="release_to" min="1900" max="<?= date('Y') ?>" placeholder="To"
                                class="input input-bordered w-full"
                                value="<?= htmlspecialchars($_POST['release_to'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Sort By</span></label>
                        <select name="sort_by" class="select select-bordered w-full">
                            <option value="rating" <?= (($_POST['sort_by'] ?? '') === 'rating') ? 'selected' : '' ?>>Rating
                            </option>
                            <option value="title" <?= (($_POST['sort_by'] ?? '') === 'title') ? 'selected' : '' ?>>Title
                            </option>
                            <option value="popularity" <?= (($_POST['sort_by'] ?? '') === 'popularity') ? 'selected' : '' ?>>Popularity</option>
                            <option value="release_date" <?= (($_POST['sort_by'] ?? '') === 'release_date') ? 'selected' : '' ?>>Release Date</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Sort Order</span></label>
                        <select name="sort_order" class="select select-bordered w-full">
                            <option value="DESC" <?= (($_POST['sort_order'] ?? '') === 'DESC') ? 'selected' : '' ?>>
                                Descending</option>
                            <option value="ASC" <?= (($_POST['sort_order'] ?? '') === 'ASC') ? 'selected' : '' ?>>Ascending
                            </option>
                        </select>
                    </div>

                    <div class="form-control mt-6">
                        <button type="submit" class="btn btn-primary w-full">Search Movies</button>
                    </div>
                    <div class="form-control">
                        <a href="advanced-search.php" class="btn btn-outline w-full">Reset Filters</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Section -->
        <div class="lg:col-span-2">
            <?php if ($searchPerformed): ?>
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold">Search Results</h3>
                    <span class="text-gray-400"><?= $totalMovies ?> movies found</span>
                </div>

                <?php if ($totalMovies > 0): ?>
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3">
                        <?php foreach ($results as $movie):
                            $movieGenres = explode(',', $movie['genre']);
                            $displayGenres = array_slice($movieGenres, 0, 2); ?>
                            <div class="card bg-base-100 shadow-md hover:shadow-xl transition-shadow duration-300">
                                <figure>
                                    <img src="<?= $movie['poster'] ?>" alt="<?= $movie['title'] ?>"
                                        class="h-64 w-full object-cover rounded-t-md" />
                                </figure>
                                <div class="card-body p-4">
                                    <a href="movie-details.php?id=<?= $movie['id'] ?>"
                                        class="hover:text-primary transition-colors duration-300">
                                        <h3 class="card-title text-lg font-semibold"><?= $movie['title'] ?></h3>
                                    </a>
                                    <p class="text-sm text-gray-400 mb-2"><?= mb_strimwidth($movie['description'], 0, 100, "...") ?>
                                    </p>
                                    <div class="flex justify-between items-center flex-wrap gap-2 mb-2">
                                        <div class="flex flex-wrap gap-1">
                                            <?php foreach ($displayGenres as $genre): ?>
                                                <span class="badge badge-primary text-xs"><?= trim($genre) ?></span>
                                            <?php endforeach; ?>
                                            <?php if (count($movieGenres) > 2): ?>
                                                <span class="badge badge-outline text-xs">+<?= count($movieGenres) - 2 ?> more</span>
                                            <?php endif; ?>
                                        </div>
                                        <span class="text-yellow-400 font-bold text-sm">⭐
                                            <?= number_format($movie['rating'], 2) ?></span>
                                    </div>
                                    <div class="flex justify-between text-sm text-gray-500">
                                        <span>📅 <?= date('Y', strtotime($movie['release_date'])) ?></span>
                                        <span>⏱ <?= $movie['runtime'] ?> min</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12 bg-base-200 rounded-lg">
                        <div class="text-6xl mb-4">🎬</div>
                        <h3 class="text-2xl font-bold mb-2">No movies found</h3>
                        <p class="text-gray-400 mb-6">Try adjusting your search filters to find more movies.</p>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-12 bg-base-200 rounded-lg">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-2xl font-bold mb-2">Find Your Perfect Movie</h3>
                    <p class="text-gray-400 mb-6">Use the filters to discover movies that match your preferences.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>