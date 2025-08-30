<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en" data-theme="night">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineMatch - Your Movie Recommendation System</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://kit.fontawesome.com/8ca39e687b.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="/cinematch/css/index.css">
</head>

<body>
    <header class="max-w-7xl mx-auto px-4 sticky top-0 z-50 border-b border-zinc-500 bg-base-100">
        <!-- Navbar Start -->
        <nav class="">
            <div class="navbar">
                <div class="navbar-start">
                    <a class="text-xl font-bold flex items-center gap-2 hover:text-primary transition-colors duration-500"
                        href="#">
                        <i class="fa-solid fa-film text-primary text-3xl"></i>
                        CineMatch
                    </a>
                </div>
                <div class="navbar-center hidden lg:flex">
                    <ul class="menu menu-horizontal px-1 transition-colors duration-300">
                        <li class="hover:text-primary"><a href="/cinematch/index.php">Home</a></li>
                        <li class="hover:text-primary"><a href="/cinematch/pages/browse.php">Browse Movies</a></li>
                        <li class="hover:text-primary"><a href="#suggestions">Suggestions</a></li>
                    </ul>
                </div>
                <div class="navbar-end">
                    <div class="flex gap-2">
                        <input type="text" placeholder="Search" class="input input-bordered w-32 md:w-auto" />
                        <?php if (isset($_SESSION['username'])):
                             ?>
                            <div class="dropdown dropdown-end">
                                <div tabindex="0" role="button"
                                    class="btn btn-ghost btn-circle avatar border border-gray-300">
                                    <i class="fa-regular fa-user"></i>
                                </div>
                                <ul tabindex="0"
                                    class="menu menu-sm dropdown-content bg-base-100 rounded-box z-1 mt-3 w-52 p-2 shadow transition-colors duration-300">
                                    <li class="hover:text-primary">
                                        <a class="justify-between">
                                            Welcome, <?php echo $_SESSION['username']; ?>
                                        </a>
                                    </li>
                                    <li class="hover:text-primary"><a href="logout.php">Logout</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-primary">Login</a>
                            <a href="register.php" class="btn btn-outline btn-primary">Register</a>
                        <?php endif; ?>
                        <div class="dropdown dropdown-end">
                            <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                                <i class="fa-solid fa-bars text-lg"></i>
                            </div>
                            <ul tabindex="0"
                                class="menu menu-sm dropdown-content bg-base-100 rounded-box z-1 mt-3 w-52 p-2 shadow">
                                <li><a href="/cinematch/index.php">Home</a></li>
                                <li><a href="/cinematch/pages/browse.php">Browse Movies</a></li>
                                <li><a href="#suggestions">Suggestions</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
        </nav>
        <!-- Navbar End -->
    </header>

    <main class="max-w-7xl mx-auto px-4 py-12">