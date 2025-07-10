<?php include 'includes/header.php'; ?>

<!-- Banner Section Start -->
<section class="max-w-2xl min-h-[calc(100vh-4rem)] flex justify-center items-center mx-auto sm:mx-0 py-8 text-center sm:text-start">
    <div class="space-y-6 md:space-y-8">
        <h1 class="text-4xl sm:text-6xl md:text-7xl font-bold leading-none">
            Your Perfect Movie <span class="text-primary">Match</span>
        </h1>
        <p class="text-sm sm:text-lg">
            Discover movies you'll love with our intelligent recommendation system.
            From blockbusters to hidden gems, find your next favorite film.
        </p>
        <div class="flex flex-wrap gap-2 sm:gap-4 justify-center sm:justify-start">
            <button class="btn btn-primary">Get Suggestions</button>
            <a href="/cinematch/browse.php" class="btn btn-primary btn-outline">Browse Movies</a>
        </div>
        <ul class="list-disc sm:pl-5 space-x-6 text-sm sm:text-base flex flex-wrap text-gray-400 md:gap-8 sm:marker:text-sky-500 justify-center sm:justify-start">
            <li>Top Rated Movies</li>
            <li>Personalized Recommendations</li>
            <li>Advanced Search Filters</li>
        </ul>
    </div>
</section>
<!-- Banner Section End -->

<!-- Trending Now Section Start -->
<section class="space-y-8" id="trending">
    <h2 class="text-3xl font-bold">🎬 Trending Now</h2>

    <div class="flex gap-6 overflow-x-auto scrollbar-hide pb-4">
        <?php
        require 'includes/db.php';
        $query = "SELECT * FROM movies ORDER BY popularity DESC LIMIT 20";
        $result = $conn->query($query);
        while ($movie = $result->fetch_assoc()):
            $genres = explode(',', $movie['genre']);
            $displayGenres = array_slice($genres, 0, 1); // show max 3 genres
        ?>
        <div class="min-w-[250px] card bg-base-100 shadow-xl hover:scale-105 transition-transform duration-300 cursor-pointer">
            <figure><img src="<?= $movie['poster'] ?>" alt="<?= $movie['title'] ?>" class="w-full h-60 object-cover" /></figure>
            <div class="card-body">
                <h2 class="card-title"><?= $movie['title'] ?></h2>
                <p><?= mb_strimwidth($movie['description'], 0, 60, "...") ?></p>
                <div class="card-actions justify-between items-center pt-2">
                    <div class="flex flex-wrap gap-1">
                        <?php foreach ($displayGenres as $genre): ?>
                            <span class="badge badge-primary whitespace-nowrap"><?= trim($genre) ?></span>
                        <?php endforeach; ?>

                        <?php if (count($genres) > 3): ?>
                            <span class="badge badge-outline text-xs whitespace-nowrap">+<?= count($genres) - 3 ?> more</span>
                        <?php endif; ?>
                    </div>
                    <span class="text-yellow-400 font-bold">⭐ <?= number_format($movie['rating'], 2) ?></span>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</section>
<!-- Trending Now Section End -->

<?php include 'includes/footer.php'; ?>
