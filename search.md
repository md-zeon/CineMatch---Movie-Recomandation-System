# CineMatch Search Functionality Documentation

This document provides a detailed explanation of the search functionality implemented in CineMatch, including both basic and advanced search features.

## Overview

CineMatch offers two types of search functionality:

1. *Basic Search*: Quick search from the navigation bar that searches by movie title, description, or genre
2. *Advanced Search*: Dedicated page with multiple filtering options including genre, rating range, and sorting preferences

## Basic Search Implementation

### Search Form (header.php)

The basic search form is located in the navigation bar and submits to search.php:

php
<form action="/cinematch/pages/search.php" method="GET" class="flex">
    <input type="text" name="query" placeholder="Search movies..." class="input input-bordered w-32 md:w-auto" required />
    <button type="submit" class="btn btn-primary ml-1">
        <i class="fa-solid fa-search"></i>
    </button>
</form>


### Search Results Page (search.php)

The search results page processes the query parameter and searches across multiple fields:

php
// Get search query
$search = isset($_GET['query']) ? trim($_GET['query']) : '';
$search = $conn->real_escape_string($search);

// If search query exists
if (!empty($search)) {
    // Get total count of matching movies
    $totalQuery = "SELECT COUNT(*) AS total FROM movies 
                  WHERE title LIKE '%$search%' 
                  OR description LIKE '%$search%' 
                  OR genre LIKE '%$search%'";
    $totalResult = $conn->query($totalQuery);
    $totalMovies = $totalResult->fetch_assoc()['total'];
    
    // Fetch matching movies with pagination
    $query = "SELECT * FROM movies 
              WHERE title LIKE '%$search%' 
              OR description LIKE '%$search%' 
              OR genre LIKE '%$search%' 
              ORDER BY rating DESC 
              LIMIT $limit OFFSET $offset";
    $result = $conn->query($query);
}


Key features of the basic search:
- Searches across multiple fields (title, description, genre)
- Uses SQL's LIKE operator with wildcards for partial matching
- Implements pagination for results
- Displays the total number of matching movies
- Shows a "No movies found" message when there are no results

## Advanced Search Implementation

### Advanced Search Page (advanced-search.php)

The advanced search page provides a form with multiple filtering options:

php
<form method="POST" action="" class="space-y-4">
    <!-- Title -->
    <div class="form-control">
        <label class="label">
            <span class="label-text">Movie Title</span>
        </label>
        <input type="text" name="title" placeholder="Enter movie title..." 
               class="input input-bordered w-full" 
               value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>">
    </div>
    
    <!-- Genre -->
    <div class="form-control">
        <label class="label">
            <span class="label-text">Genre</span>
        </label>
        <select name="genre" class="select select-bordered w-full">
            <option value="">All Genres</option>
            <?php foreach ($genres as $genre): ?>
                <option value="<?= $genre ?>" <?= (isset($_POST['genre']) && $_POST['genre'] === $genre) ? 'selected' : '' ?>>
                    <?= $genre ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <!-- Rating Range -->
    <div class="form-control">
        <label class="label">
            <span class="label-text">Rating Range</span>
        </label>
        <div class="flex items-center gap-2">
            <input type="number" name="min_rating" min="0" max="10" step="0.1" 
                   class="input input-bordered w-full" placeholder="Min" 
                   value="<?= isset($_POST['min_rating']) ? htmlspecialchars($_POST['min_rating']) : '0' ?>">
            <span>to</span>
            <input type="number" name="max_rating" min="0" max="10" step="0.1" 
                   class="input input-bordered w-full" placeholder="Max" 
                   value="<?= isset($_POST['max_rating']) ? htmlspecialchars($_POST['max_rating']) : '10' ?>">
        </div>
    </div>
    
    <!-- Sort Options -->
    <div class="form-control">
        <label class="label">
            <span class="label-text">Sort By</span>
        </label>
        <select name="sort_by" class="select select-bordered w-full">
            <option value="rating">Rating</option>
            <option value="title">Title</option>
            <option value="popularity">Popularity</option>
        </select>
    </div>
    
    <div class="form-control">
        <label class="label">
            <span class="label-text">Sort Order</span>
        </label>
        <select name="sort_order" class="select select-bordered w-full">
            <option value="DESC">Descending</option>
            <option value="ASC">Ascending</option>
        </select>
    </div>
    
    <!-- Submit Button -->
    <div class="form-control mt-6">
        <button type="submit" class="btn btn-primary">Search Movies</button>
    </div>
    
    <!-- Reset Button -->
    <div class="form-control">
        <button type="reset" class="btn btn-outline">Reset Filters</button>
    </div>
</form>


### Dynamic Genre List

The advanced search page dynamically loads all available genres from the database:

php
// Get all unique genres from the database
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

$genres = [];
while ($row = $genresResult->fetch_assoc()) {
    $genres[] = trim($row['genre_name']);
}
$genres = array_unique($genres);
sort($genres);


This query:
1. Splits the comma-separated genre field into individual genres
2. Extracts each genre as a separate row
3. Removes duplicates and sorts alphabetically

### Search Query Building

The advanced search builds a dynamic SQL query based on the selected filters:

php
// Build query
$query = "SELECT * FROM movies WHERE 1=1";

if (!empty($title)) {
    $query .= " AND title LIKE '%$title%'";
}

if (!empty($selectedGenre)) {
    $query .= " AND genre LIKE '%$selectedGenre%'";
}

$query .= " AND rating BETWEEN $minRating AND $maxRating";

// Add sorting
$validSortColumns = ['title', 'rating', 'popularity'];
$validSortOrders = ['ASC', 'DESC'];

if (in_array($sortBy, $validSortColumns) && in_array($sortOrder, $validSortOrders)) {
    $query .= " ORDER BY $sortBy $sortOrder";
} else {
    $query .= " ORDER BY rating DESC";
}


Key features of the advanced search:
- Filter by movie title (partial matching)
- Filter by specific genre
- Filter by rating range (min to max)
- Sort results by different criteria (rating, title, popularity)
- Choose sort order (ascending or descending)
- Maintains form state between submissions
- Displays the total number of matching movies
- Shows a "No movies found" message when there are no results

## Security Considerations

Both search implementations include security measures:

1. *Input Sanitization*: All user inputs are sanitized using $conn->real_escape_string() to prevent SQL injection
2. *Output Escaping*: All outputs are properly escaped using htmlspecialchars() to prevent XSS attacks
3. *Validation*: Numeric inputs are validated before being used in queries
4. *Whitelisting*: Sort columns and orders are validated against whitelists

## User Interface

The search functionality is accessible from multiple places:

1. *Main Navigation*: Search box in the header
2. *Mobile Menu*: Search box in the mobile dropdown menu
3. *Advanced Search Link*: Link below the search box
4. *Navigation Menu*: "Advanced Search" option in both desktop and mobile navigation

## Pagination Implementation

Both search pages implement pagination to handle large result sets:

php
// Pagination setup
$limit = 12;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Get total count of matching movies
$totalResult = $conn->query($totalQuery);
$totalMovies = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalMovies / $limit);


The pagination UI shows:
- Previous/Next buttons
- Current page number
- First and last page numbers
- Ellipsis (...) for skipped page numbers
- Highlights the current page

## Future Enhancements

Potential improvements for the search functionality:

1. *Full-Text Search*: Implement MySQL's FULLTEXT search for better relevance ranking
2. *Multiple Genre Selection*: Allow users to select multiple genres
3. *Year/Release Date Filtering*: Add filters for movie release years
4. *Actor/Director Search*: Expand search to include cast and crew
5. *Search History*: Save recent searches for users
6. *Saved Searches*: Allow logged-in users to save their search criteria
7. *Auto-suggestions*: Implement typeahead suggestions as users type
8. *Filter by User Ratings*: Allow filtering by user ratings (once implemented)

## Code Structure

The search functionality is implemented across several files:

- includes/header.php: Contains the search form in the navigation
- pages/search.php: Handles basic search functionality
- pages/advanced-search.php: Implements advanced search with filters

This modular approach allows for easy maintenance and future enhancements.
