<?php include 'includes/header.php'; ?>

<div class="flex justify-center items-center min-h-screen">
    <div class="w-full max-w-md p-8 space-y-6 bg-base-200 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-center">Login</h1>
        <form action="auth.php" method="POST">
            <div class="space-y-4">
                <div>
                    <label for="username" class="text-sm font-medium">Username</label>
                    <input type="text" name="username" id="username"
                        class="w-full px-3 py-2 border rounded-md bg-base-100 border-gray-600 focus:outline-none focus:ring-primary focus:border-primary"
                        required>
                </div>
                <div>
                    <label for="password" class="text-sm font-medium">Password</label>
                    <input type="password" name="password" id="password"
                        class="w-full px-3 py-2 border rounded-md bg-base-100 border-gray-600 focus:outline-none focus:ring-primary focus:border-primary"
                        required>
                </div>
            </div>
            <button type="submit" name="login"
                class="w-full mt-6 px-4 py-2 text-white bg-primary rounded-md hover:bg-primary-focus focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">Login</button>
        </form>
        <p class="text-sm text-center">Don't have an account? <a href="register.php"
                class="text-primary hover:underline">Register here</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
