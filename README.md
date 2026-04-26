# Mapa Cidadão API

## 📋 Description

**Mapa Cidadão API** is a backend application developed in Laravel that enables the registration and monitoring of urban incidents on an interactive map. Focused on citizen participation and public management, the application allows users to report various types of problems affecting their neighborhoods.

Examples of incidents that can be registered:
- 🗑️ Garbage accumulation
- 💡 Lack of public lighting
- 🛣️ Pavement problems
- 🌊 Flooding
- 🚧 Other types of incidents

Each registered incident contains:
- 📍 The precise geographic location of the problem (via GPS coordinates)
- 📝 A brief description of what is happening
- 🏷️ A pre-defined incident type
- 👍 Likes system for prioritization

The main objective of the project is to provide a georeferenced database so that city halls, public managers, and citizens can visualize, track, and prioritize urban maintenance actions.

Mapa Cidadão API serves as the backend of a web application and offers a RESTful interface for integration with modern frontends.

## ✨ Features

- **Incident Registration**: Users can register incidents with geographic location.
- **Incident Types**: Categorization system with enums for different types of urban problems.
- **Likes System**: Users can like incidents to indicate priority.
- **Authentication**: Integration with Laravel Sanctum for user authentication.
- **Geolocation**: Uses PostGIS for geospatial storage and queries.
- **RESTful API**: Well-defined endpoints for frontend integration.
- **Documentation**: Automatic API documentation with Scribe.

## 🛠️ Technologies

<img align="left" alt="PHP" title="PHP" width="30px" style="padding-right: 10px;" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/php/php-original.svg" />
<img align="left" alt="Laravel" title="Laravel" width="30px" style="padding-right: 10px;" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/laravel/laravel-original.svg" />
<img align="left" alt="Docker" title="Docker" width="30px" style="padding-right: 10px;" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/docker/docker-original.svg" />
<img align="left" alt="PostgreSQL" title="PostgreSQL" width="30px" style="padding-right: 10px;" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/postgresql/postgresql-original.svg" />

<br><br>

- **PHP 8.4**
- **Laravel 12**
- **Docker & Docker Compose**
- **PostgreSQL with PostGIS**
- **Laravel Sanctum** (for authentication)
- **Scribe** (for API documentation)
- **PHPUnit** (for testing)

## 📋 Prerequisites

Before you begin, make sure you have the following installed on your machine:

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [Git](https://git-scm.com/)

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/netoandre/mapa_cidadao_api.git
cd mapa_cidadao_api
```

### 2. Configure Docker Containers

Copy the Docker Compose example file and start the containers:

```bash
cp docker-compose.example.yml docker-compose.yml
docker compose up -d
```

### 3. Configure Environment

Copy the environment configuration files:

```bash
cp .env.example .env
cp .env.example.testing .env.testing
```

### 4. Enter the Application Container

```bash
docker compose exec app bash
```

### 5. Configure Application Keys

Generate the keys for production and test environments:

```bash
php artisan key:generate
php artisan key:generate --env=testing
```

### 6. Install Dependencies

```bash
composer install
```

### 7. Run Migrations and Seeds

```bash
php artisan migrate
php artisan db:seed
```

### 8. Run Tests

```bash
composer test
```

The application will be running at `http://localhost:8000/`.

## 📖 Usage

### Main Endpoints

The API offers the following main endpoints:

#### Authentication
- `POST /api/register` - Register new user
- `POST /api/login` - User login
- `POST /api/logout` - User logout

#### Incidents
- `GET /api/ocurrences` - List incidents
- `POST /api/ocurrences` - Create new incident
- `GET /api/ocurrences/{id}` - Get specific incident
- `PUT /api/ocurrences/{id}` - Update incident
- `DELETE /api/ocurrences/{id}` - Delete incident
- `POST /api/ocurrences/{id}/like` - Like an incident

#### Incident Types
- `GET /api/types-ocurrence` - List incident types

### API Documentation

The complete API documentation can be accessed at `http://localhost:8000/docs` (using Scribe).

## 🧪 Testing

To run tests:

```bash
composer test
```

Or using PHPUnit directly:

```bash
./vendor/bin/phpunit
```

## 🤝 Contributing

Contributions are welcome! To contribute:

1. Fork the project
2. Create a branch for your feature (`git checkout -b feature/new-feature`)
3. Commit your changes (`git commit -am 'Add new feature'`)
4. Push to the branch (`git push origin feature/new-feature`)
5. Open a Pull Request

## 📄 License

This project is under the MIT license. See the [LICENSE](LICENSE) file for more details.

## 📞 Contact

For questions or suggestions, please contact the development team.

---

Developed with ❤️ using Laravel