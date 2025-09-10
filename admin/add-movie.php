<?php
require '../includes/db.php';
require '../includes/sendEmail.php'; // PHPMailer function
include '../includes/header.php';
// Only admin can access
if (!isset($_SESSION['email']) || $_SESSION['email'] !== 'zeonrahaman5870@gmail.com') {
    header("Location: ../index.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $imdb_id = $_POST['imdb_id'] ?? '';
    $tmdb_id = $_POST['tmdb_id'] ?? '';
    $original_title = $_POST['original_title'] ?? '';
    $tagline = $_POST['tagline'] ?? '';
    $description = $_POST['description'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $language = $_POST['language'] ?? '';
    $country = $_POST['country'] ?? '';
    $release_date = $_POST['release_date'] ?? '';
    $runtime = $_POST['runtime'] ?? '';
    $director = $_POST['director'] ?? '';
    $cast = $_POST['cast'] ?? '';
    $rating = $_POST['rating'] ?? 0;
    $vote_count = $_POST['vote_count'] ?? 0;
    $popularity = $_POST['popularity'] ?? 0;
    $poster = $_POST['poster'] ?? '';
    $backdrop = $_POST['backdrop'] ?? '';
    $trailer_url = $_POST['trailer_url'] ?? '';

    $stmt = $conn->prepare("INSERT INTO movies (imdb_id, tmdb_id, title, original_title, tagline, description, genre, language, country, release_date, runtime, director, cast, rating, vote_count, popularity, poster, backdrop, trailer_url, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssssssssisssddsss", $imdb_id, $tmdb_id, $title, $original_title, $tagline, $description, $genre, $language, $country, $release_date, $runtime, $director, $cast, $rating, $vote_count, $popularity, $poster, $backdrop, $trailer_url);

    if ($stmt->execute()) {
        $message = "Movie added successfully! Sending notifications to subscribers...";

        // Send email to all subscribers
        $subscribers = $conn->query("SELECT email FROM newsletter_subscriptions");
        while ($row = $subscribers->fetch_assoc()) {
            sendNewsletterEmail($row['email'], $title); // send movie title in email
        }
        $message .= " Emails sent.";
    } else {
        $message = "Error: " . $stmt->error;
    }
}
?>


<div class="container mx-auto sm:px-4 py-12">
    <h1 class="text-4xl font-bold mb-8 text-center text-primary">🎬 Add New Movie</h1>
    
    <?php if ($message): ?>
        <div class="alert alert-info shadow-lg mb-6">
            <span><?= $message ?></span>
        </div>
    <?php endif; ?>

    <form action="" method="POST" 
          class="bg-base-200 rounded-2xl shadow-xl p-8 grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Title -->
        <div class="form-control md:col-span-2">
            <label class="label font-semibold">Title</label>
            <input type="text" name="title" placeholder="Movie Title" 
                   class="input input-bordered w-full" required>
        </div>

        <!-- IDs -->
        <div>
            <label class="label font-semibold">IMDB ID</label>
            <input type="text" name="imdb_id" class="input input-bordered w-full">
        </div>
        <div>
            <label class="label font-semibold">TMDB ID</label>
            <input type="text" name="tmdb_id" class="input input-bordered w-full">
        </div>

        <!-- Other Titles -->
        <div>
            <label class="label font-semibold">Original Title</label>
            <input type="text" name="original_title" class="input input-bordered w-full">
        </div>
        <div>
            <label class="label font-semibold">Tagline</label>
            <input type="text" name="tagline" class="input input-bordered w-full">
        </div>

        <!-- Genre, Language, Country -->
        <div>
            <label class="label font-semibold">Genre</label>
            <input type="text" name="genre" placeholder="e.g. Action, Drama" class="input input-bordered w-full">
        </div>
        <div>
            <label class="label font-semibold">Language</label>
            <input type="text" name="language" class="input input-bordered w-full">
        </div>
        <div>
            <label class="label font-semibold">Country</label>
            <input type="text" name="country" class="input input-bordered w-full">
        </div>

        <!-- Release Date + Runtime -->
        <div>
            <label class="label font-semibold">Release Date</label>
            <input type="date" name="release_date" class="input input-bordered w-full">
        </div>
        <div>
            <label class="label font-semibold">Runtime (minutes)</label>
            <input type="number" name="runtime" class="input input-bordered w-full">
        </div>

        <!-- Director + Cast -->
        <div>
            <label class="label font-semibold">Director</label>
            <input type="text" name="director" class="input input-bordered w-full">
        </div>
        <div>
            <label class="label font-semibold">Cast</label>
            <input type="text" name="cast" placeholder="Comma separated" class="input input-bordered w-full">
        </div>

        <!-- Rating + Vote Count + Popularity -->
        <div>
            <label class="label font-semibold">Rating</label>
            <input type="number" step="0.1" name="rating" class="input input-bordered w-full">
        </div>
        <div>
            <label class="label font-semibold">Vote Count</label>
            <input type="number" name="vote_count" class="input input-bordered w-full">
        </div>
        <div>
            <label class="label font-semibold">Popularity</label>
            <input type="number" step="0.01" name="popularity" class="input input-bordered w-full">
        </div>

        <!-- Poster + Backdrop + Trailer -->
        <div>
            <label class="label font-semibold">Poster URL</label>
            <input type="text" name="poster" class="input input-bordered w-full">
        </div>
        <div>
            <label class="label font-semibold">Backdrop URL</label>
            <input type="text" name="backdrop" class="input input-bordered w-full">
        </div>
        <div>
            <label class="label font-semibold">Trailer URL</label>
            <input type="text" name="trailer_url" class="input input-bordered w-full">
        </div>

        <!-- Description -->
        <div class="md:col-span-2">
            <label class="label font-semibold">Description</label>
            <textarea name="description" rows="5" 
                      class="textarea textarea-bordered w-full"></textarea>
        </div>

        <!-- Submit -->
        <div class="md:col-span-2 flex justify-center">
            <button type="submit" class="btn btn-primary w-full text-lg">
                <i class="fa-solid fa-plus mr-2"></i> Add Movie
            </button>
        </div>
    </form>
</div>


<?php include '../includes/footer.php'; ?>
