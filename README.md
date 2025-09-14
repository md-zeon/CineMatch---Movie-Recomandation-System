# CineMatch 🎬

A comprehensive movie recommendation and discovery platform built with PHP and MySQL. CineMatch helps users discover their perfect movie matches through an intelligent system featuring user authentication, personalized watchlists, advanced search capabilities, and an admin panel for content management.

## 🌟 Features

### User Features
- **User Authentication**: Secure registration and login system with password hashing
- **Movie Discovery**: Browse trending, top-rated, and recently added movies
- **Advanced Search**: Find movies by title, genre, director, cast, and more
- **Movie Details**: Comprehensive movie information including ratings, cast, trailers
- **Personal Watchlist**: Save movies for later viewing
- **User Profile**: Manage personal information and preferences
- **Newsletter Subscription**: Stay updated with new movie releases
- **Responsive Design**: Optimized for desktop and mobile devices

### Admin Features
- **Movie Management**: Add new movies with detailed information
- **Content Administration**: Full control over movie database
- **Newsletter Management**: Send updates to subscribers
- **User Management**: Admin access control

### Technical Features
- **Real-time Statistics**: Dynamic movie count, ratings, and genre statistics
- **Pagination**: Efficient browsing through large movie collections
- **Image Integration**: Movie posters and backdrops from TMDB
- **Email Notifications**: Automated newsletter system using PHPMailer
- **Modern UI**: Built with Tailwind CSS and DaisyUI components
- **Animations**: Smooth transitions and AOS (Animate On Scroll) effects

## 🛠️ Tech Stack

### Backend
- **PHP 7.4+**: Server-side scripting
- **MySQL**: Database management
- **PHPMailer**: Email functionality

### Frontend
- **HTML5**: Semantic markup
- **Tailwind CSS**: Utility-first CSS framework
- **DaisyUI**: Component library for Tailwind CSS
- **Font Awesome**: Icon library
- **AOS (Animate On Scroll)**: Animation library

### Development Tools
- **XAMPP**: Local development environment
- **Git**: Version control
- **Composer**: PHP dependency management

### External Services
- **TMDB API**: Movie data and images
- **Zencoder**: Video encoding (for trailers)

## 📋 Prerequisites

Before running this project, ensure you have the following installed:

- **XAMPP** (or any Apache + MySQL + PHP stack)
- **PHP 7.4 or higher**
- **MySQL 5.7 or higher**
- **Composer** (for PHP dependencies)
- **Git** (for cloning the repository)
- **Web browser** (Chrome, Firefox, Safari, etc.)

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/yourusername/cinematch.git
cd cinematch
```

### 2. Set Up XAMPP
1. Install XAMPP from [apachefriends.org](https://www.apachefriends.org/)
2. Start Apache and MySQL services from XAMPP Control Panel
3. Copy the project folder to `C:\xampp\htdocs\` (Windows) or `/opt/lampp/htdocs/` (Linux/Mac)

### 3. Database Setup
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `cinematch`
3. Import the database schema (if provided) or create tables manually:

```sql
-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Movies table
CREATE TABLE movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    imdb_id VARCHAR(20),
    tmdb_id VARCHAR(20),
    title VARCHAR(255) NOT NULL,
    original_title VARCHAR(255),
    tagline VARCHAR(255),
    description TEXT,
    genre VARCHAR(255),
    language VARCHAR(10),
    country VARCHAR(100),
    release_date DATE,
    runtime INT,
    director VARCHAR(255),
    cast TEXT,
    rating DECIMAL(3,1),
    vote_count INT,
    popularity DECIMAL(10,3),
    poster VARCHAR(500),
    backdrop VARCHAR(500),
    trailer_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Watchlist table
CREATE TABLE watchlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    movie_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
);

-- Newsletter subscriptions table
CREATE TABLE newsletter_subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 4. Configure Database Connection
Update `includes/db.php` with your database credentials:

```php
$host = 'localhost';
$user = 'root'; // Your MySQL username
$pass = ''; // Your MySQL password
$dbname = 'cinematch';
```

### 5. Install PHP Dependencies
```bash
composer install
```

### 6. Configure Email Settings
Update `includes/sendEmail.php` with your SMTP settings for newsletter functionality:

```php
$mail->Host = 'smtp.gmail.com'; // Your SMTP host
$mail->Username = 'your-email@gmail.com'; // Your email
$mail->Password = 'your-app-password'; // Your email password
```

## 📖 Usage

### Accessing the Application
1. Start XAMPP (Apache and MySQL)
2. Open your browser and navigate to: `http://localhost/cinematch/`

### User Registration
1. Click on "Register" or "Sign Up"
2. Fill in username, email, and password
3. Click "Register" to create your account

### User Login
1. Click on "Login"
2. Enter your username and password
3. Click "Login" to access your account

### Browsing Movies
1. From the homepage, explore "Trending Now", "Top Rated", and "Recently Added" sections
2. Click "Browse Movies" for the full catalog with pagination
3. Use the search functionality to find specific movies

### Managing Watchlist
1. Log in to your account
2. Navigate to movie details
3. Click "Add to Watchlist" to save movies
4. Access your watchlist from the profile menu

### Admin Functions
1. Log in with admin credentials (email: zeonrahaman5870@gmail.com)
2. Navigate to `/admin/add-movie.php`
3. Fill in movie details and submit
4. System will automatically notify newsletter subscribers

## 📸 Screenshots

### Homepage
![Homepage](/images/Homepage.jpg)

### Movie Browse Page
![Browse Movies](https://via.placeholder.com/800x600?text=Browse+Movies+Screenshot)

### Movie Details Page
![Movie Details](https://via.placeholder.com/800x600?text=Movie+Details+Screenshot)

### Admin Panel
![Admin Panel](https://via.placeholder.com/800x600?text=Admin+Panel+Screenshot)

### Mobile View
![Mobile Responsive](https://via.placeholder.com/400x600?text=Mobile+View+Screenshot)

## 📁 Project Structure

```
cinematch/
├── .gitignore
├── admin/
│   └── add-movie.php
├── css/
│   └── index.css
├── includes/
│   ├── db.php
│   ├── footer.php
│   ├── header.php
│   ├── PHPMailer/
│   │   ├── src/
│   │   ├── language/
│   │   └── ...
│   └── sendEmail.php
├── pages/
│   ├── advanced-search.php
│   ├── browse.php
│   ├── movie-details.php
│   ├── profile.php
│   ├── search.php
│   └── watchlist.php
├── auth.php
├── index.php
├── login.php
├── logout.php
├── register.php
├── subscribe.php
└── README.md
```

## 🔗 API & External Services

### TMDB (The Movie Database)
- **Purpose**: Movie data, posters, backdrops, and trailers

## 🤝 Contributing

We welcome contributions to CineMatch! Here's how you can help:

### Development Setup
1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature-name`
3. Make your changes
4. Test thoroughly
5. Commit your changes: `git commit -m 'Add some feature'`
6. Push to the branch: `git push origin feature/your-feature-name`
7. Open a Pull Request

### Coding Standards
- Follow PSR-12 coding standards for PHP
- Use meaningful variable and function names
- Add comments for complex logic
- Test all new features

### Reporting Issues
- Use the GitHub Issues tab
- Provide detailed descriptions
- Include steps to reproduce
- Add screenshots if applicable

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📞 Contact

**Project Developer**: Zeanur Rahaman Zeon
- **Email**: zeon.cse@gmail.com
- **GitHub**: [md-zeon](https://github.com/md-zeon)
- **LinkedIn**: [https://linkedin.com/in/zeanur-rahaman-zeon](https://linkedin.com/in/zeanur-rahaman-zeon)

## 🤝 Collaborators

- **MD. SOJIB** ([SOJIBAHAMMAD](https://github.com/SOJIBAHAMMAD))
- **Md Zihadul Islam** ([csezihad](https://github.com/csezihad))
- **Md Kamal Hossain** ([kamal-hossin](https://github.com/kamal-hossin))
- **Samiha Islam Chaity** ([Samiha7231](https://github.com/Samiha7231))
- **Joti** ([miskatulmomotaj01-ai](https://github.com/miskatulmomotaj01-ai))


## 🙏 Acknowledgments

- **TMDB**: For providing movie data and images
- **Tailwind CSS & DaisyUI**: For the beautiful UI components
- **PHPMailer**: For email functionality
- **Font Awesome**: For icons
- **AOS**: For smooth animations

## 🔄 Version History

### Version 1.0.0
- Initial release
- Basic movie browsing functionality
- User authentication
- Admin panel
- Newsletter system

## 🚀 Future Enhancements

- [ ] User movie ratings and reviews
- [ ] Advanced recommendation algorithm
- [ ] Social features (following users, sharing lists)
- [ ] Mobile app development
- [ ] Integration with streaming services
- [ ] Multi-language support
- [ ] Dark mode toggle
- [ ] Movie trailer streaming
- [ ] Advanced filtering options

---

**Made with ❤️ for movie lovers everywhere**

*Find your perfect movie match with CineMatch!*
