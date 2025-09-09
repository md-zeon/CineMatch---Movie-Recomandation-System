<?php
session_start();
require 'includes/db.php'; // DB connection
require 'includes/sendEmail.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $user_id = $_SESSION['user_id'] ?? null; // if logged in, save user_id

    if (!$email) {
        die("Invalid email address.");
    }

    // Check if already subscribed
    $stmt = $conn->prepare("SELECT id FROM newsletter_subscriptions WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "You are already subscribed.";
        exit;
    }

    // Insert new subscription
    $stmt = $conn->prepare("INSERT INTO newsletter_subscriptions (user_id, email) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $email);

    if ($stmt->execute()) {
        if (sendNewsletterEmail($email)) {
            echo "Subscription successful! Please check your inbox.";
        } else {
            echo "Subscribed, but failed to send confirmation email.";
        }
    } else {
        echo "Something went wrong. Please try again.";
    }
}
?>
