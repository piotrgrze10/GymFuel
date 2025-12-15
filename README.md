# Visit gymfuel.pl

# GymFuel 🔥

A comprehensive nutrition tracking web application designed to help users monitor their daily food intake, track macros, and achieve their fitness goals.

## Features

### Core Functionality
- **Daily Nutrition Tracking** - Log meals (breakfast, lunch, dinner, snacks) with detailed macro tracking
- **Food Database** - Search and add foods from an extensive database with nutritional information
- **Calorie Management** - Track calories against personalized TDEE goals
- **Macro Tracking** - Monitor protein, carbs, and fat intake
- **Water Intake** - Track daily hydration with customizable goals
- **Progress Charts** - Visualize nutrition trends and progress over time
- **BMI & FFMI Calculators** - Calculate body metrics for fitness assessment

### User Features
- **User Authentication** - Secure registration and login system
- **Personalized Goals** - Automatic BMR and TDEE calculation based on user profile
- **Profile Management** - Update personal information and fitness goals
- **Date Navigation** - View and edit nutrition logs for past and future dates
- **Meal Organization** - Flexible meal structure with categorized food entries

## Tech Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL (utf8mb4)
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Framework**: Bootstrap 5.3.2
- **Charts**: ApexCharts 3.44.0
- **Icons**: Font Awesome 6
- **Server**: Apache (XAMPP compatible)

## Requirements

- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache web server
- Modern web browser with JavaScript enabled

## Installation

1. Clone or download the repository to your web server directory (e.g., `htdocs` for XAMPP)

2. Create a `.env` file in the root directory:
```env
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=gymfuel
APP_ENV=development
APP_DEBUG=true
```

3. Import the database schema:
```bash
mysql -u root -p < database.sql
```

4. Configure your web server to point to the project directory

5. Access the application through your browser (e.g., `http://localhost/`)

## Project Structure

```
├── api/              # API endpoints for AJAX requests
├── auth/             # Authentication handlers (login, register, logout)
├── css/              # Stylesheets
├── includes/         # Shared PHP files (config, auth helpers)
├── img/              # Images and assets
├── js/               # JavaScript files
├── sessions/         # PHP session storage
├── index.php         # Homepage
├── dashboard.php     # Main dashboard
├── charts.php        # Progress charts
├── search_products.php # Food search
├── bmi_calculator.php # Calculator hub
├── profile.php       # User profile
└── database.sql      # Database schema
```

## Key Features Explained

### BMR & TDEE Calculation
- **BMR (Basal Metabolic Rate)**: Calculated using the Mifflin-St Jeor equation
- **TDEE (Total Daily Energy Expenditure)**: BMR × Activity Level multiplier
- Activity levels: Sedentary, Light, Moderate, Active, Very Active

### Nutrition Tracking
- Track calories, protein, carbs, fat, fiber, and sugar
- Support for multiple unit types (grams, pieces, cups, etc.)
- Real-time macro calculations
- Visual progress indicators

### Database
- User accounts with encrypted passwords
- Daily nutrition logs
- Food database with nutritional information
- Weight history tracking

## Security Features

- Password hashing (PHP `password_hash`)
- CSRF token protection
- SQL injection prevention (PDO prepared statements)
- Session management with secure cookies
- Input validation and sanitization

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## License

This project is open source and available for personal and educational use.

## Contributing

Contributions, issues, and feature requests are welcome!

---

**Note**: This application is designed for local development and educational purposes. For production deployment, ensure proper security configurations, SSL certificates, and environment variable management.

