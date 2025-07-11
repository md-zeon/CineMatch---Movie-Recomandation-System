<?php include 'includes/header.php'; ?>

<!-- Banner Section Start -->
<section class="max-w-7xl flex justify-center items-center mx-auto sm:mx-0 pb-8 text-center sm:text-start">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center w-full">
        <!-- Left Content -->
        <div class="space-y-6 md:space-y-8" data-aos="fade-right">
            <h1 class="text-5xl sm:text-6xl md:text-7xl font-bold leading-none">
                Your Perfect Movie <span class="text-primary">Match</span>
            </h1>
            <p class="text-sm sm:text-lg">
                Discover movies you'll love with our intelligent recommendation system.
                From blockbusters to hidden gems, find your next favorite film.
            </p>
            <div class="flex flex-wrap gap-2 sm:gap-4 justify-center sm:justify-start">
                <button class="btn btn-primary hover:scale-105 transition-transform duration-300">Get
                    Suggestions</button>
                <a href="/cinematch/pages/browse.php"
                    class="btn btn-outline btn-primary hover:scale-105 transition-transform duration-300">Browse
                    Movies</a>
            </div>
            <ul
                class="list-disc sm:pl-5 space-x-6 text-sm sm:text-base flex flex-wrap text-gray-400 md:gap-8 sm:marker:text-sky-500 justify-center sm:justify-start">
                <li>Top Rated Movies</li>
                <li>Personalized Recommendations</li>
                <li>Advanced Search Filters</li>
            </ul>
        </div>
        <!-- Right Image -->
        <div class="hidden lg:block" data-aos="fade-left">
            <div class="relative w-full max-w-md mx-auto">
                <img src="https://image.tmdb.org/t/p/w500/3lwlJL8aW6Wor9tKvME8VoMnBkn.jpg" alt="Featured Movie"
                    class="rounded-lg shadow-2xl w-full max-w-md mx-auto transform rotate-3 hover:rotate-0 transition-transform duration-500" />
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent rounded-lg"></div>
            </div>
        </div>
    </div>
</section>
<!-- Banner Section End -->

<!-- Trending Now Section Start -->
<section class="space-y-8 py-12">
    <h2 class="text-3xl font-bold">🎬 Trending Now</h2>

    <div class="flex gap-6 overflow-x-auto scrollbar-hide pb-4">
        <?php
        require 'includes/db.php';
        $query = "SELECT * FROM movies ORDER BY popularity DESC LIMIT 20";
        $result = $conn->query($query);
        $i = 0;
        while ($movie = $result->fetch_assoc()):
            $genres = explode(',', $movie['genre']);
            $genre = $genres[0];
            $i++;
            ?>
            <div data-aos="fade-left" data-aos-delay="<?= $i * 100 ?>"
                class="min-w-[250px] card bg-base-100 shadow-xl hover:scale-105 transition-transform duration-300 cursor-pointer">
                <figure><img src="<?= $movie['poster'] ?>" alt="<?= $movie['title'] ?>" class="w-full h-60 object-cover" />
                </figure>
                <div class="card-body">
                    <h2 class="card-title"><?= $movie['title'] ?></h2>
                    <p><?= mb_strimwidth($movie['description'], 0, 60, "...") ?></p>
                    <div class="card-actions justify-between items-center pt-2">
                        <div class="flex flex-wrap gap-1">
                            <span class="badge badge-primary whitespace-nowrap"><?= trim($genre) ?></span>
                        </div>
                        <span class="text-yellow-400 font-bold">⭐ <?= number_format($movie['rating'], 2) ?></span>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>
<!-- Trending Now Section End -->
<!-- Top Rated Movies Section Start -->
<section class="space-y-8 py-12">
    <h2 class="text-3xl font-bold">🎬 Top Rated Movies</h2>

    <div class="flex gap-6 overflow-x-auto scrollbar-hide pb-4">
        <?php
        require 'includes/db.php';
        $query = "SELECT * FROM movies ORDER BY rating DESC LIMIT 20";
        $result = $conn->query($query);
        $i = 0;
        while ($movie = $result->fetch_assoc()):
            $genres = explode(',', $movie['genre']);
            $genre = $genres[0];
            $i++;
            ?>
            <div data-aos="fade-left" data-aos-delay="<?= $i * 100 ?>"
                class="min-w-[250px] card bg-base-100 shadow-xl hover:scale-105 transition-transform duration-300 cursor-pointer">
                <figure><img src="<?= $movie['poster'] ?>" alt="<?= $movie['title'] ?>" class="w-full h-60 object-cover" />
                </figure>
                <div class="card-body">
                    <h2 class="card-title"><?= $movie['title'] ?></h2>
                    <p><?= mb_strimwidth($movie['description'], 0, 60, "...") ?></p>
                    <div class="card-actions justify-between items-center pt-2">
                        <div class="flex flex-wrap gap-1">
                            <span class="badge badge-primary whitespace-nowrap"><?= trim($genre) ?></span>
                        </div>
                        <span class="text-yellow-400 font-bold">⭐ <?= number_format($movie['rating'], 2) ?></span>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>
<!-- Top Rated Movies Section End -->

<?php include 'includes/footer.php'; ?>