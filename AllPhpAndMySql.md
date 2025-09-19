# All PHP and MySQL Reference for CineMatch

A concise reference of the PHP functions, MySQLi methods, and SQL constructs used throughout this repository, with purpose, parameters, and examples from the codebase.

- Scope: MySQLi, PHPMailer, core PHP web helpers, and SQL used in: auth, search, advanced search, suggestions, watchlist, admin add-movie, and UI pages.
- Files scanned: includes/db.php, includes/header.php, includes/footer.php, includes/sendEmail.php, search_suggestions.php, subscribe.php, auth.php, index.php, pages/*.php, watchlist.php, admin/add-movie.php.

---

## 1) MySQLi (PHP’s MySQL client, object-oriented)

### new mysqli($host, $user, $pass, $dbname, $port)
Creates a new database connection object.

- **Parameters**:
  - **$host**: hostname or IP, e.g., 'localhost'
  - **$user**: database username
  - **$pass**: database password
  - **$dbname**: database name
  - **$port**: optional TCP port (3306 by default)
- **Returns**: mysqli object in `$conn`
- **Used in**: includes/db.php

```php
$conn = new mysqli($host, $user, $pass, $dbname, 3306);
```

### $conn->connect_error
String describing the last connection error. If non-empty, the connection failed.

- **Used for**: Fail-fast if DB connection couldn’t be established
- **Used in**: includes/db.php

```php
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
```

### $conn->query($sql)
Runs a raw SQL query (no bound parameters). Returns a mysqli_result or false.

- **Use when**: Query is static or safely constructed
- **Examples**: counts, simple lists

```php
$result = $conn->query("SELECT * FROM movies ORDER BY rating DESC LIMIT 20");
$row = $result->fetch_assoc();
```

### $conn->prepare($sql)
Creates a prepared statement for parameterized queries.

- **Why**: Prevents SQL injection, handles types safely
- **Returns**: mysqli_stmt

```php
$stmt = $conn->prepare("SELECT title, poster FROM movies WHERE title LIKE CONCAT('%', ?, '%') LIMIT 10");
```

### $stmt->bind_param($types, ...$vars)
Binds PHP variables to the prepared statement parameters.

- **$types**: string of type codes matching parameters count:
  - **s**: string
  - **i**: integer
  - **d**: double/float
  - **b**: blob

```php
$stmt->bind_param("s", $query);          // string
$stmt->bind_param("ii", $user_id, $id);   // two integers
$stmt->bind_param("iisd", $mId, $uId, $txt, $rating); // int, int, string, double
```

### $stmt->execute()
Executes the prepared statement.

```php
$stmt->execute();
```

### $stmt->get_result()
Fetches a mysqli_result for SELECT queries (requires mysqlnd driver).

```php
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) { /* ... */ }
```

### $result->fetch_assoc()
Fetches the next row as an associative array.

```php
$row = $result->fetch_assoc();
```

### $stmt->store_result(), $stmt->num_rows
Stores result set in memory and reads row count.

```php
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) { /* ... */ }
```

### $stmt->close()
Closes a prepared statement and frees resources.

```php
$stmt->close();
```

### $conn->real_escape_string($str)
Escapes special characters for safe embedding in string literals (use prepared statements when possible).

- **Used in**: pages/search.php (basic search)

```php
$search = $conn->real_escape_string($search);
```

---

## 2) SQL Patterns Used

### SELECT, WHERE, LIKE, CONCAT
Basic filtering and substring matches.

```sql
SELECT * FROM movies WHERE title LIKE '%Terminator%';
SELECT title FROM movies WHERE title LIKE CONCAT('%', ?, '%'); -- prepared
```

### ORDER BY, LIMIT, OFFSET
Sorting and pagination.

```sql
SELECT * FROM movies ORDER BY rating DESC LIMIT 12 OFFSET 24;
```

### COUNT, AVG, DISTINCT, COUNT(DISTINCT)
Aggregation and de-dup.

```sql
SELECT COUNT(*) AS total FROM movies;
SELECT AVG(rating) AS avg FROM movies WHERE rating > 0;
SELECT COUNT(DISTINCT genre) AS count FROM movies;
```

### JOIN
Combining rows from related tables.

```sql
SELECT r.*, u.username
FROM reviews r
JOIN users u ON r.user_id = u.id
WHERE r.movie_id = ?
ORDER BY r.created_at DESC;
```

### BETWEEN, YEAR()
Range and date helpers.

```sql
SELECT * FROM movies WHERE rating BETWEEN ? AND ?;
SELECT * FROM movies WHERE YEAR(release_date) >= ?;
```

### INSERT, NOW(), ON DUPLICATE KEY UPDATE, DELETE
Creating, upserting, and deleting rows.

```sql
INSERT INTO reviews (movie_id, user_id, review_text) VALUES (?, ?, ?);
INSERT INTO watchlist (user_id, movie_id) VALUES (?, ?) 
  ON DUPLICATE KEY UPDATE added_at = CURRENT_TIMESTAMP;
DELETE FROM watchlist WHERE user_id = ? AND movie_id = ?;
```

### ORDER BY RAND()
Random selection of a row (OK for small tables).

```sql
SELECT * FROM movies WHERE rating >= 7.0 ORDER BY RAND() LIMIT 1;
```

### Splitting CSV genres (numbers table trick)
Deriving unique genre tokens from a comma-separated column.

```sql
SELECT DISTINCT TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(genre, ',', n.digit+1), ',', -1)) AS genre_name
FROM movies
CROSS JOIN (
  SELECT 0 AS digit UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 
  UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9
) AS n
WHERE LENGTH(genre) - LENGTH(REPLACE(genre, ',', '')) >= n.digit
  AND TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(genre, ',', n.digit+1), ',', -1)) != ''
ORDER BY genre_name;
```

---

## 3) Core PHP Web Helpers

### session_start(), $_SESSION
Starts a session and accesses per-user state.

```php
session_start();
$_SESSION['user_id'] = $user['id'];
```

### superglobals: $_GET, $_POST, $_SERVER
Access HTTP input and environment.

```php
$search = $_GET['query'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') { /* ... */ }
```

### header($string)
Sends HTTP headers; used for redirects or content-type.

```php
header("Location: browse.php");
// For JSON APIs, consider:
// header('Content-Type: application/json');
```

### require / include
Loads PHP files; require halts on failure; include warns.

```php
require 'includes/db.php';
include '../includes/header.php';
```

### exit(), die($message)
Stops script execution (die optionally outputs a message first).

```php
if (!$email) die("Invalid email address.");
exit();
```

### filter_var($value, FILTER_VALIDATE_EMAIL)
Validates emails and other data types.

```php
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
```

### json_encode($value)
Converts a PHP value to JSON.

```php
echo json_encode($suggestions);
```

### password_hash($password, PASSWORD_DEFAULT), password_verify($plain, $hash)
Secure password hashing and verification.

```php
$hash = password_hash($password, PASSWORD_DEFAULT);
if (password_verify($password, $user['password'])) { /* login */ }
```

### htmlspecialchars($str)
Escapes HTML special chars to prevent XSS when outputting.

```php
<h3><?= htmlspecialchars($movie['title']) ?></h3>
```

### urlencode($str)
Encodes strings for safe use in URLs.

```php
<a href="?query=<?= urlencode($search) ?>&page=<?= $i ?>">Link</a>
```

### error_log($message), print_r($var, true)
Logs messages to the server error log (useful for debugging POST handlers).

```php
error_log("POST received: " . print_r($_POST, true));
```

---

## 4) General PHP Utilities Appearing in Code

- **trim($str)**: remove leading/trailing whitespace
- **strlen($str)**: string length check
- **isset($var)** / **empty($var)**: presence/emptiness checks
- **is_numeric($var)**, **intval($var)**, **floatval($var)**: numeric validation/conversion
- **explode($sep, $str)**: split strings into array
- **array_slice($arr, $offset, $len)**: take part of an array
- **array_unique($arr)**, **sort($arr)**: array de-dup and ordering
- **number_format($num, $decimals)**: format numbers for UI
- **mb_strimwidth($str, $start, $width, $trimMarker)**: multibyte-safe trimming for UI
- **date($format, $timestamp?)**, **strtotime($str)**: format/parse dates

Examples:
```php
$genres = explode(',', $movie['genre']);
$displayGenres = array_slice($genres, 0, 2);
$title = mb_strimwidth($movie['description'], 0, 100, "...");
$year = date('Y', strtotime($movie['release_date']));
```

---

## 5) PHPMailer (Email Sending)
Used for welcome/notification emails when subscribing or after admin adds a movie.

Key methods in includes/sendEmail.php:

```php
$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = '...';
$mail->Password = '...'; // app password
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

$mail->setFrom('no-reply@site', 'CineMatch');
$mail->addAddress($toEmail);

$mail->isHTML(true);
$mail->Subject = 'Subject';
$mail->Body = '<strong>Hello</strong>';

$mail->send();
```

- **Exceptions**: Wrap in try/catch (Exception $e); inspect `$mail->ErrorInfo` on failure
- **Tip**: Move credentials to environment variables; avoid hardcoding

---

## 6) Prepared Statements: Quick Guide

- Build SQL with placeholders `?`
- Prepare once, bind parameters with types, execute, then fetch

```php
$query = "INSERT INTO reviews (movie_id, user_id, review_text, rating) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param('iisd', $movieId, $userId, $text, $rating);
$stmt->execute();
```

- **bind_param types**:
  - **s**: string (e.g., title, email, genre)
  - **i**: integer (e.g., id, page, user_id)
  - **d**: double/float (e.g., rating, popularity)
  - **b**: blob

---

## 7) File-by-File Highlights (where each appears)

- **includes/db.php**: `new mysqli`, `connect_error`
- **includes/header.php**: `session_start`, navigation, search form, `urlencode`
- **includes/footer.php**: JS suggestions rendering (API is PHP/JSON at `/search_suggestions.php`)
- **search_suggestions.php**: `require db.php`, `isset($_GET['query'])`, `trim`, prepared `SELECT ... LIKE`, `bind_param('s')`, `execute`, `get_result`, `fetch_assoc`, `json_encode`
- **pages/search.php**: `real_escape_string`, raw `LIKE` query with pagination, `header('Location: ...')`
- **pages/advanced-search.php**: complex genre split query; POST validation; dynamic prepared query with multiple optional filters; `bind_param` with variadics
- **pages/movie-details.php**: mixed GET/POST; insert reviews with or without numeric `rating` using different `bind_param` signatures; `header` redirect after POST; prepared `SELECT` and JOIN for reviews
- **watchlist.php / pages/watchlist.php**: session check, add/remove watchlist with prepared `INSERT`/`DELETE`, optional `ON DUPLICATE KEY UPDATE`
- **auth.php**: register/login with `password_hash`/`password_verify`, prepared `INSERT`/`SELECT`, set `$_SESSION` values, redirect
- **subscribe.php**: `filter_var` for email, duplicate check via `SELECT ... store_result/num_rows`, prepared `INSERT`, call PHPMailer helper, plain text responses
- **includes/sendEmail.php**: PHPMailer SMTP config, HTML email composing, error handling
- **admin/add-movie.php**: large prepared `INSERT` with many fields (types string/double/int), then select subscriber emails and send notifications
- **index.php**: multiple raw queries for lists and KPIs; `ORDER BY RAND()` example

---

## 8) Common Pitfalls and Best Practices

- **Prefer prepared statements** over constructing SQL strings manually (avoid SQL injection).
- **Escape output** with `htmlspecialchars` when rendering user/content data in HTML.
- **Validate inputs** (e.g., `filter_var` for email, `is_numeric` for IDs, bounds checks for rating/year).
- **Set correct headers** for APIs returning JSON:
  ```php
  header('Content-Type: application/json');
  ```
- **Avoid ORDER BY RAND()** on very large tables (performance); consider alternative sampling.
- **Do not hardcode secrets** (SMTP credentials). Use environment variables/config.

---

## 9) Mini Cheat Sheet

- **Connect**: `new mysqli($h,$u,$p,$db,$port)` → check `$conn->connect_error`
- **Raw query**: `$conn->query($sql)` → `$result->fetch_assoc()`
- **Prepared**: `$stmt = $conn->prepare($sql); $stmt->bind_param('si', $s, $i); $stmt->execute(); $stmt->get_result();`
- **Counts**: `SELECT COUNT(*) AS total ...` → `$row['total']`
- **Pagination**: `LIMIT $limit OFFSET $offset`
- **Search**: `WHERE title LIKE CONCAT('%', ?, '%')`
- **Insert**: `INSERT INTO table (...) VALUES (...)` → `$stmt->execute()`
- **Delete**: `DELETE FROM table WHERE id = ?`
- **Session**: `session_start(); $_SESSION['user_id']=...;`
- **Redirect**: `header('Location: /path'); exit();`
- **Passwords**: `password_hash`, `password_verify`
- **Encode HTML**: `htmlspecialchars`
- **JSON**: `echo json_encode($data)`

---