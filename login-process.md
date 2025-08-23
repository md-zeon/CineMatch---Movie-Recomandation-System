# CineMatch Authentication Process

This document provides a detailed explanation of the authentication system in CineMatch, including the login, registration, and logout processes.

## Overview

CineMatch uses a custom PHP session-based authentication system. The authentication flow is handled by several key files:

- `login.php`: Frontend form for user login
- `register.php`: Frontend form for user registration
- `auth.php`: Backend processing for both login and registration
- `logout.php`: Handles user logout
- `includes/db.php`: Database connection used by auth processes

## Registration Process

### 1. User Interface (register.php)

The registration process begins when a user visits `register.php`, which displays a form with the following fields:
- Username
- Password
- Confirm Password

The form submits to `auth.php` with a POST method and includes a hidden field named 'register' that triggers the registration logic.

### 2. Backend Processing (auth.php)

When the form is submitted, `auth.php` processes the registration as follows:

1. Checks if the 'register' POST parameter is set
2. Retrieves the username, password, and confirm_password from POST data
3. Validates that password and confirm_password match
4. Hashes the password using PHP's `password_hash()` function with the DEFAULT algorithm
5. Prepares an SQL statement to insert the new user into the 'users' table
6. Executes the prepared statement with the username and hashed password
7. If successful, redirects the user to the login page
8. If unsuccessful, displays an error message

```php
// Registration logic in auth.php
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        die("Passwords do not match!");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $hashed_password);

    if ($stmt->execute()) {
        header("Location: login.php");
    } else {
        die("Error: " . $stmt->error);
    }
}
```

### 3. Database Storage

User data is stored in the 'users' table with the following structure:
- `id`: Auto-incremented primary key
- `username`: Unique username
- `password`: Hashed password string

## Login Process

### 1. User Interface (login.php)

The login process begins when a user visits `login.php`, which displays a form with:
- Username
- Password

The form submits to `auth.php` with a POST method and includes a hidden field named 'login' that triggers the login logic.

### 2. Backend Processing (auth.php)

When the form is submitted, `auth.php` processes the login as follows:

1. Checks if the 'login' POST parameter is set
2. Retrieves the username and password from POST data
3. Prepares an SQL statement to select the user with the provided username
4. Executes the prepared statement
5. Checks if exactly one user is found
6. If a user is found, verifies the password using `password_verify()`
7. If password verification succeeds:
   - Sets session variables for user_id and username
   - Redirects to index.php
8. If password verification fails, displays an error message
9. If no user is found, displays a "User not found" message

```php
// Login logic in auth.php
elseif (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
        } else {
            die("Incorrect password!");
        }
    } else {
        die("User not found!");
    }
}
```

### 3. Session Management

Upon successful login, the application:
1. Starts a PHP session (if not already started)
2. Stores the user's ID and username in session variables
3. These session variables are then available throughout the application to identify the logged-in user

## Logout Process

The logout process is handled by `logout.php` and typically performs these actions:

1. Starts the session (if not already started)
2. Unsets all session variables
3. Destroys the session
4. Redirects the user to the login page or homepage

```php
// Typical logout.php implementation
session_start();
$_SESSION = array();
session_destroy();
header("Location: index.php");
exit();
```

## Session Validation

Throughout the application, the user's login status is checked by verifying the existence of session variables:

```php
// Example from header.php
<?php if (isset($_SESSION['username'])): ?>
    // Display logged-in user menu
<?php else: ?>
    // Display login/register buttons
<?php endif; ?>
```

## Security Considerations

The CineMatch authentication system implements several security best practices:

1. **Password Hashing**: Uses PHP's built-in `password_hash()` function with the DEFAULT algorithm (currently bcrypt)
2. **Prepared Statements**: Prevents SQL injection by using prepared statements for all database queries
3. **Input Validation**: Validates user input before processing
4. **Session Management**: Properly manages user sessions

## Authentication Flow Diagram

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│  register.php│     │   auth.php  │     │  Database   │
│  (Form UI)   │────▶│ (Processing)│────▶│ (users table)│
└─────────────┘     └─────────────┘     └─────────────┘
                           │
┌─────────────┐           │
│   login.php  │◀──────────┘
│   (Form UI)  │
└─────────────┘
       │
       ▼
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   login.php  │     │   auth.php  │     │  Database   │
│   (Form UI)  │────▶│ (Processing)│────▶│ (users table)│
└─────────────┘     └─────────────┘     └─────────────┘
                           │
┌─────────────┐           │
│   index.php  │◀──────────┘
│ (Logged in)  │
└─────────────┘
       │
       ▼
┌─────────────┐     ┌─────────────┐
│  logout.php  │     │   index.php  │
│ (Processing) │────▶│  (Logged out)│
└─────────────┘     └─────────────┘
```