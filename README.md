# Laravel Tree API

A Laravel-based HTTP server that handles tree data structures with full CRUD operations, persistence, and comprehensive testing.

## Features

- **RESTful API endpoints** for tree data management
- **Hierarchical tree structure** with parent-child relationships
- **Database persistence** using SQLite (development) or MySQL/PostgreSQL (production)
- **Comprehensive testing** with PHPUnit
- **Input validation** and error handling
- **Nested JSON response format** for easy consumption
- **Production-ready** with proper error handling and logging

## API Endpoints

### GET /api/tree
Returns an array of all trees that exist in the database in nested format.

**Response Example:**
```json
[
  {
    "id": 1,
    "label": "root",
    "children": [
      {
        "id": 3,
        "label": "bear",
        "children": [
          {
            "id": 4,
            "label": "cat",
            "children": []
          }
        ]
      },
      {
        "id": 7,
        "label": "frog",
        "children": []
      }
    ]
  }
]
```

### POST /api/tree
Creates a new node and attaches it to the specified parent node in the tree.

**Request Body:**
```json
{
  "label": "cat's child",
  "parentId": 4
}
```

**Response Example:**
```json
{
  "id": 8,
  "label": "cat's child",
  "parent_id": 4,
  "created_at": "2025-07-08T02:13:07.000000Z",
  "updated_at": "2025-07-08T02:13:07.000000Z"
}
```

**Validation Rules:**
- `label`: Required, string, max 255 characters
- `parentId`: Optional, integer, must exist in trees table

## Technology Stack

- **Laravel 12**: Modern PHP framework
- **SQLite**: Database (development)
- **PHPUnit**: Testing framework
- **Eloquent ORM**: Database interactions
- **JSON API**: RESTful responses

## Installation & Setup

### Prerequisites
- PHP 8.2 or higher
- Composer
- SQLite extension for PHP

### Installation Steps

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd laravel-api-tree/example-api
   ```

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Environment setup:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup:**
   ```bash
   # Create SQLite database
   touch database/database.sqlite
   
   # Run migrations
   php artisan migrate
   
   # Seed with sample data (optional)
   php artisan db:seed
   ```

## Running the Server

### Development Server
```bash
php artisan serve
```
The server will be available at `http://localhost:8000`

### Production Deployment
For production, use a proper web server like Nginx or Apache with PHP-FPM. Configure your web server to point to the `public/` directory.

## Testing

### Run All Tests
```bash
php artisan test
```

### Run Tree API Tests Specifically
```bash
php artisan test --filter TreeApiTest
```

### Test Coverage
The test suite includes:
- ✅ GET endpoint returns nested tree structure
- ✅ POST endpoint creates new nodes
- ✅ POST endpoint creates root nodes (without parent)
- ✅ Validation error handling
- ✅ Empty tree handling
- ✅ Multiple root nodes support

## API Usage Examples

### Using cURL

**Get all trees:**
```bash
curl -X GET http://localhost:8000/api/tree
```

**Create a new root node:**
```bash
curl -X POST http://localhost:8000/api/tree \
  -H "Content-Type: application/json" \
  -d '{"label": "new root"}'
```

**Create a child node:**
```bash
curl -X POST http://localhost:8000/api/tree \
  -H "Content-Type: application/json" \
  -d '{"label": "child node", "parentId": 1}'
```

### Using JavaScript (fetch)

```javascript
// Get all trees
const trees = await fetch('/api/tree').then(r => r.json());

// Create a new node
const newNode = await fetch('/api/tree', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ label: 'new node', parentId: 1 })
}).then(r => r.json());
```

## Database Schema

### Trees Table
```sql
CREATE TABLE trees (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    label VARCHAR(255) NOT NULL,
    parent_id INTEGER NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES trees(id) ON DELETE CASCADE
);
```

## Project Structure

```
app/
├── Http/Controllers/
│   └── TreeController.php      # API controller
├── Models/
│   └── Tree.php               # Tree model with relationships
database/
├── migrations/
│   └── *_create_trees_table.php
├── seeders/
│   └── TreeSeeder.php         # Sample data seeder
tests/
├── Feature/
│   └── TreeApiTest.php        # API endpoint tests
routes/
└── api.php                    # API routes definition
```

## Error Handling

The API includes comprehensive error handling:

- **422 Validation Error**: Invalid input data
- **404 Not Found**: Parent node doesn't exist
- **500 Server Error**: Database or server issues

Example error response:
```json
{
  "error": "Validation failed",
  "messages": {
    "label": ["The label field is required."]
  }
}
```

## Performance Considerations

- Uses database indexes on `parent_id` for efficient queries
- Implements proper foreign key constraints
- Uses Eloquent relationships for optimized queries
- Includes database connection pooling in production

## Security Features

- Input validation and sanitization
- SQL injection protection via Eloquent ORM
- CSRF protection (disabled for API endpoints)
- Rate limiting (can be enabled in production)

## Contributing

1. Fork the repository
2. Create a feature branch
3. Write tests for new functionality
4. Ensure all tests pass
5. Submit a pull request

## License

MIT License - see LICENSE file for details.

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
