# Tree API

A simple Laravel API that manages tree data structures. Built for the coding challenge requirements.

## What it does

- Two API endpoints for managing trees
- Stores data in a SQLite database (persists between server restarts)
- Includes tests to verify everything works
- Returns nested JSON structure as specified

## The API

### GET /api/tree
Gets all trees from the database in nested format.

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
Creates a new node. You can create a root node (no parentId) or attach to an existing parent.

**Request:**
```json
{
  "label": "cat's child",
  "parentId": 4
}
```

**Response:**
```json
{
  "id": 8,
  "label": "cat's child",
  "parent_id": 4,
  "created_at": "2025-07-08T02:13:07.000000Z",
  "updated_at": "2025-07-08T02:13:07.000000Z"
}
```

## Quick Start

**Prerequisites:** PHP 8.2+, Composer

1. **Install dependencies:**
   ```bash
   composer install
   ```

2. **Set up environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Set up database:**
   ```bash
   touch database/database.sqlite
   php artisan migrate:fresh --seed
   ```

4. **Start the server:**
   ```bash
   php artisan serve
   ```
   
   Server runs at `http://localhost:8000`

## Testing

Run the tests to make sure everything works:
```bash
php artisan test
```

## Try it out

**Get all trees:**
```bash
curl http://localhost:8000/api/tree
```

**Create a root node:**
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

## Demo Script

For a complete demonstration, run:
```bash
./demo.sh
```

This script will set up everything, run tests, and demonstrate the API with examples.

## How it works

The API uses a simple database table with a `parent_id` column to create the tree structure. The GET endpoint recursively builds the nested JSON response, and the POST endpoint validates input and creates new nodes.

**Key files:**
- `app/Http/Controllers/TreeController.php` - The API logic
- `app/Models/Tree.php` - Database model
- `tests/Feature/TreeApiTest.php` - Tests
- `database/migrations/...create_trees_table.php` - Database schema

## Notes

- Data persists between server restarts (SQLite database)
- Input validation prevents empty labels and invalid parent IDs
- Tests cover all the main functionality
- The demo script shows everything working together
