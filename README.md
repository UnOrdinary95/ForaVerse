<h1 align="center">
    <br>
    <img src="public/images/favicon/favicon_foraverse.png" width="120">
    <br>
    ForaVerse
    <br>
</h1>

<h4 align="center">A PHP MVC web application for building and moderating online communities.</h4>

<p align="center">
  ForaVerse enables users to create, join, and moderate communities, participate in discussions, vote, and manage favorites. The platform features robust authentication and a modular architecture.
</p>

<p align="center">
  <a href="#key-features">Key Features</a> •
  <a href="#installation--setup">Installation & Setup</a> •
  <a href="#architecture">Architecture</a> •
  <a href="#file-structure">File Structure</a> •
  <a href="#license">License</a>
</p>

## Key Features

- **Community Management** – Create, join, and moderate communities
- **Discussion System** – Post, view, and interact with discussions
- **Voting & Favorites** – Upvote/downvote and favorite publications
- **Role-Based Moderation** – Assign roles and moderate content
- **Session Authentication** – Secure login and session management
- **MVC Architecture** – Separation of concerns
- **Customizable UI** – Modular header, sidebar, and main content
- **Error Logging** – Centralized error handling and logging

## Installation & Setup

### Prerequisites

- PHP 8.0+
- Composer
- PostgreSQL (or compatible database)
- Web server (Apache/Nginx recommended)

### Installation Steps

```bash
# Clone the repository
git clone https://github.com/UnOrdinary95/ForaVerse.git

# Navigate to the project directory
cd ForaVerse

# Install PHP dependencies
composer install

# Configure your database in create.sql and import it
psql -U youruser -d yourdb -f create.sql

# Set up environment variables
cp .env-template.txt .env
# Edit .env with your database credentials

# Start your web server and point the root to the project directory
```

## Architecture

ForaVerse uses a custom MVC pattern:

- **Controllers** (`app/controllers/`) handle user actions and business logic.
- **Models** (`app/models/`) manage data access and entities.
- **Views** (`app/views/`) render HTML and interface components.
- **Core** (`app/core/`) includes routing and database connection logic.
- **Utils** (`app/utils/`) provides validation, logging, mailing, and file upload utilities.
- **Public** (`public/`) contains static assets, scripts, styles, and images.

Routing is managed by [`app/core/Routeur.php`](app/core/Routeur.php:1), mapping URL actions to controller methods. The entry point is [`index.php`](index.php:1).

## File Structure

```
ForaVerse/
├── index.php                   # Application entry point
├── composer.json               # PHP dependencies
├── create.sql                  # Database schema
├── app/
│   ├── controllers/            # Controllers (Accueil, Communaute, Profil, etc.)
│   ├── core/                   # Routing and DB connection
│   ├── models/                 # DAOs and entities
│   ├── utils/                  # Validators, Logger, Mailer, uploads
│   └── views/                  # Page templates and components
├── public/
│   ├── images/                 # Logo and favicon
│   ├── scripts/                # JS files for UI interactions
│   ├── styles/                 # CSS styles
│   └── fonts/                  # Custom fonts
├── .env-template.txt           # Environment variable template
├── README.md                   # Project documentation
└── README copy.md              # Reference README template
```

## License

MIT