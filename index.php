<?php include 'includes/header.php'; ?>

<?php if (isset($_GET['subscribed'])): ?>
<div class="alert alert-success shadow-lg mb-4">
    <div>
        <i class="fa-solid fa-check-circle"></i>
        <span>Successfully subscribed to the newsletter! You'll receive updates on new movies.</span>
    </div>
</div>
<?php endif; ?>

<!-- Banner Section Start -->
<section class="container flex justify-center items-center mx-auto sm:mx-0 pb-8 text-center sm:text-start">
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

<!-- Genres Showcase Section Start -->
<!-- <section class="py-16">
    <div class="container mx-auto px-4">
        <h2 class="text-4xl font-bold text-center mb-12" data-aos="fade-up">Explore by Genre</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
            <?php
            $genres = [
                ['name' => 'Action', 'icon' => 'fa-gun', 'color' => 'from-red-500 to-red-600'],
                ['name' => 'Comedy', 'icon' => 'fa-laugh', 'color' => 'from-yellow-500 to-yellow-600'],
                ['name' => 'Drama', 'icon' => 'fa-theater-masks', 'color' => 'from-blue-500 to-blue-600'],
                ['name' => 'Horror', 'icon' => 'fa-ghost', 'color' => 'from-purple-500 to-purple-600'],
                ['name' => 'Romance', 'icon' => 'fa-heart', 'color' => 'from-pink-500 to-pink-600'],
                ['name' => 'Sci-Fi', 'icon' => 'fa-rocket', 'color' => 'from-indigo-500 to-indigo-600']
            ];

            foreach ($genres as $index => $genre):
                ?>
            <div class="card bg-gradient-to-br <?= $genre['color'] ?> text-white shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer"
                 data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                <div class="card-body text-center p-6">
                    <i class="fa-solid <?= $genre['icon'] ?> text-4xl mb-4"></i>
                    <h3 class="card-title text-lg font-bold justify-center"><?= $genre['name'] ?></h3>
                    <p class="text-sm opacity-90">Discover amazing <?= strtolower($genre['name']) ?> movies</p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section> -->
<!-- Genres Showcase Section End -->

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
                <a href="/cinematch/pages/movie-details.php?id=<?= $movie['id'] ?>">
                    <figure><img src="<?= $movie['poster'] ?>" alt="<?= $movie['title'] ?>"
                            class="w-full h-60 object-cover" />
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
                </a>
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
                <a href="/cinematch/pages/movie-details.php?id=<?= $movie['id'] ?>">
                    <figure><img src="<?= $movie['poster'] ?>" alt="<?= $movie['title'] ?>"
                            class="w-full h-60 object-cover" />
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
                </a>
            </div>
        <?php endwhile; ?>
    </div>
</section>
<!-- Top Rated Movies Section End -->

<!-- Recently Added Section Start -->
<section class="space-y-8 py-12">
    <h2 class="text-3xl font-bold">Recently Added</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php
        require 'includes/db.php';
        $query = "SELECT * FROM movies ORDER BY created_at DESC LIMIT 8";
        $result = $conn->query($query);
        $i = 0;
        while ($movie = $result->fetch_assoc()):
            $genres = explode(',', $movie['genre']);
            $genre = $genres[0];
            $i++;
            ?>
            <div data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>"
                class="card bg-base-100 shadow-xl hover:scale-105 transition-transform duration-300">
                <a href="/cinematch/pages/movie-details.php?id=<?= $movie['id'] ?>">
                    <figure><img src="<?= $movie['poster'] ?>" alt="<?= $movie['title'] ?>"
                            class="w-full h-60 object-cover" />
                    </figure>
                    <div class="card-body">
                        <h2 class="card-title text-lg"><?= $movie['title'] ?></h2>
                        <p class="text-sm text-gray-600"><?= mb_strimwidth($movie['description'], 0, 80, "...") ?></p>
                        <div class="card-actions justify-between items-center pt-2">
                            <div class="flex flex-wrap gap-1">
                                <span class="badge badge-primary whitespace-nowrap text-xs"><?= trim($genre) ?></span>
                            </div>
                            <span class="text-yellow-400 font-bold text-sm">⭐
                                <?= number_format($movie['rating'], 1) ?></span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endwhile; ?>
    </div>
</section>
<!-- Recently Added Section End -->

<!-- Movie of the Day Section Start -->
<section class="py-16 bg-gradient-to-r from-primary to-secondary text-white">
    <div class="container mx-auto px-4">
        <?php
        // Get a random highly-rated movie for "Movie of the Day"
        $query = "SELECT * FROM movies WHERE rating >= 7.0 ORDER BY RAND() LIMIT 1";
        $result = $conn->query($query);
        $featuredMovie = $result->fetch_assoc();
        ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            <div data-aos="fade-up">
                <h2 class="text-3xl font-bold mb-4">🎬 Movie of the Day</h2>
                <h3 class="text-2xl mb-4 font-semibold"><?= htmlspecialchars($featuredMovie['title']) ?></h3>
                <p class="mb-6 text-lg leading-relaxed opacity-90">
                    <?= mb_strimwidth($featuredMovie['description'], 0, 200, "...") ?>
                </p>
                <div class="flex items-center gap-4 mb-6">
                    <span class="badge badge-lg badge-white text-primary font-bold">
                        ⭐ <?= number_format($featuredMovie['rating'], 1) ?>
                    </span>
                    <span class="text-white/80">
                        <?= $featuredMovie['runtime'] ? $featuredMovie['runtime'] . ' min' : 'N/A' ?>
                    </span>
                </div>
                <a href="/cinematch/pages/movie-details.php?id=<?= $featuredMovie['id'] ?>"
                    class="btn btn-outline btn-white hover:scale-105 transition-transform duration-300">
                    <i class="fa-solid fa-play mr-2"></i>View Details
                </a>
            </div>
            <div class="text-center" data-aos="fade-up">
                <img src="<?= $featuredMovie['poster'] ?>" alt="Movie of the Day"
                    class="rounded-lg shadow-2xl max-w-sm mx-auto transform hover:scale-105 transition-transform duration-500" />
            </div>
        </div>
    </div>
</section>
<!-- Movie of the Day Section End -->

<!-- Statistics Section Start -->
<section class="py-16 bg-base-100">
  <div class="container mx-auto px-4">
    <h2 class="text-4xl font-bold text-center mb-12" data-aos="fade-up">CineMatch by Numbers</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">

      <?php
      // Get statistics
      $totalMovies = $conn->query("SELECT COUNT(*) as count FROM movies")->fetch_assoc()['count'];
      $avgRating = $conn->query("SELECT AVG(rating) as avg FROM movies WHERE rating > 0")->fetch_assoc()['avg'];
      $totalGenres = $conn->query("SELECT COUNT(DISTINCT genre) as count FROM movies")->fetch_assoc()['count'];
      $topRatedCount = $conn->query("SELECT COUNT(*) as count FROM movies WHERE rating >= 8.0")->fetch_assoc()['count'];
      ?>

      <!-- Card 1 -->
      <div class="bg-base-300 shadow-lg rounded-xl p-8 text-center transform transition duration-300 hover:scale-105" data-aos="fade-up" data-aos-delay="100">
        <div class="text-5xl font-bold text-primary mb-2"><?= number_format($totalMovies) ?>+</div>
        <p class="text-gray-600 font-medium">Movies in Database</p>
      </div>

      <!-- Card 2 -->
      <div class="bg-base-300 shadow-lg rounded-xl p-8 text-center transform transition duration-300 hover:scale-105" data-aos="fade-up" data-aos-delay="200">
        <div class="text-5xl font-bold text-secondary mb-2"><?= number_format($avgRating, 1) ?></div>
        <p class="text-gray-600 font-medium">Average Rating</p>
      </div>

      <!-- Card 3 -->
      <div class="bg-base-300 shadow-lg rounded-xl p-8 text-center transform transition duration-300 hover:scale-105" data-aos="fade-up" data-aos-delay="300">
        <div class="text-5xl font-bold text-accent mb-2"><?= $totalGenres ?>+</div>
        <p class="text-gray-600 font-medium">Genres Available</p>
      </div>

      <!-- Card 4 -->
      <div class="bg-base-300 shadow-lg rounded-xl p-8 text-center transform transition duration-300 hover:scale-105" data-aos="fade-up" data-aos-delay="400">
        <div class="text-5xl font-bold text-info mb-2"><?= $topRatedCount ?>+</div>
        <p class="text-gray-600 font-medium">Top Rated Movies</p>
      </div>

    </div>
  </div>
</section>
<!-- Statistics Section End -->


<!-- Newsletter Section Start -->
<?php if (isset($_SESSION['username'])): ?>
<section class="py-20 bg-gradient-to-r from-primary to-accent text-white relative overflow-hidden">
    <div class="container mx-auto px-4 text-center relative z-10">
        <!-- Heading -->
        <h2 class="text-5xl md:text-6xl font-extrabold mb-4" data-aos="fade-up">
            Stay Updated with CineMatch
        </h2>
        <!-- Subheading -->
        <p class="text-lg md:text-xl mb-10 opacity-90" data-aos="fade-up" data-aos-delay="100">
            Subscribe to our newsletter and get notified about new movies, updates, and exclusive recommendations.
        </p>
        <!-- Form -->
        <form action="subscribe.php" method="POST" class="max-w-lg mx-auto" data-aos="fade-up" data-aos-delay="200">
            <div class="flex flex-col sm:flex-row gap-3">
                <input 
                    type="email" 
                    name="email" 
                    placeholder="Enter your email" 
                    class="flex-1 px-5 py-3 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-white transition-all duration-300" 
                    required
                >
                <button 
                    type="submit" 
                    class="btn btn-outline btn-white hover:bg-white hover:text-primary font-bold px-6 py-3 h-full rounded-lg transform hover:scale-105 transition-transform duration-300"
                >
                    Subscribe
                </button>
            </div>
        </form>
        <!-- small note -->
        <p class="mt-4 text-sm opacity-80" data-aos="fade-up" data-aos-delay="300">
            No spam, unsubscribe anytime.
        </p>
    </div>

    <!-- Decorative Background -->
    <div class="absolute -top-20 -right-20 w-72 h-72 bg-white opacity-10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-20 -left-20 w-96 h-96 bg-indigo-300 opacity-20 rounded-full blur-3xl pointer-events-none"></div>
</section>
<?php endif; ?>
<!-- Newsletter Section End -->


<?php include 'includes/footer.php'; ?>
