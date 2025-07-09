#!/bin/bash

# Laravel Tree API Demo Script
# This script demonstrates the tree API functionality by:
# 1. Setting up the environment
# 2. Starting the development server
# 3. Running comprehensive tests
# 4. Demonstrating API endpoints with real examples

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_step() {
    echo -e "${BLUE}[STEP]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_info() {
    echo -e "${YELLOW}[INFO]${NC} $1"
}

# Function to check if a command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Function to wait for server to be ready
wait_for_server() {
    local url="$1"
    local max_attempts=30
    local attempt=1
    
    print_info "Waiting for server to be ready at $url..."
    
    while [ $attempt -le $max_attempts ]; do
        if curl -s -f "$url" >/dev/null 2>&1; then
            print_success "Server is ready!"
            return 0
        fi
        
        echo -n "."
        sleep 1
        attempt=$((attempt + 1))
    done
    
    print_error "Server failed to start after $max_attempts seconds"
    return 1
}

# Function to make API calls with proper error handling
api_call() {
    local method="$1"
    local endpoint="$2"
    local data="$3"
    local description="$4"
    
    print_info "API Call: $description"
    echo -e "  ${BLUE}$method${NC} $endpoint"
    
    if [ -n "$data" ]; then
        echo -e "  ${YELLOW}Data:${NC} $data"
        response=$(curl -s -X "$method" "http://localhost:8000$endpoint" \
                  -H "Content-Type: application/json" \
                  -d "$data" \
                  -w "\n%{http_code}")
    else
        response=$(curl -s -X "$method" "http://localhost:8000$endpoint" \
                  -w "\n%{http_code}")
    fi
    
    # Extract body and status code
    body=$(echo "$response" | head -n -1)
    status_code=$(echo "$response" | tail -n 1)
    
    if [ "$status_code" -ge 200 ] && [ "$status_code" -lt 300 ]; then
        print_success "Response ($status_code):"
        echo "$body" | python3 -m json.tool 2>/dev/null || echo "$body"
    else
        print_error "Failed ($status_code):"
        echo "$body"
    fi
    
    echo
}

# Main script starts here
echo "========================================="
echo "🌳 Laravel Tree API Demo Script"
echo "========================================="
echo

# Step 1: Check prerequisites
print_step "1. Checking prerequisites..."

if ! command_exists php; then
    print_error "PHP is not installed. Please install PHP 8.2 or higher."
    exit 1
fi

if ! command_exists composer; then
    print_error "Composer is not installed. Please install Composer."
    exit 1
fi

if ! command_exists curl; then
    print_error "curl is not installed. Please install curl."
    exit 1
fi

php_version=$(php -v | head -n 1 | cut -d " " -f 2 | cut -d "." -f 1-2)
print_success "PHP version: $php_version"
print_success "Composer version: $(composer --version --no-ansi | head -n 1)"

# Step 2: Environment setup
print_step "2. Setting up environment..."

# Check if .env exists, if not copy from .env.example
if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        cp .env.example .env
        print_success "Created .env file from .env.example"
    else
        print_error ".env.example file not found"
        exit 1
    fi
fi

# Install dependencies
print_info "Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

# Generate app key if not exists
if ! grep -q "APP_KEY=base64:" .env; then
    php artisan key:generate --no-interaction
    print_success "Generated application key"
fi

# Step 3: Database setup
print_step "3. Setting up database..."

# Create SQLite database if it doesn't exist
if [ ! -f "database/database.sqlite" ]; then
    touch database/database.sqlite
    print_success "Created SQLite database"
fi

# Fresh migration and seed (reset database to avoid conflicts)
print_info "Setting up fresh database..."
php artisan migrate:fresh --seed --force

print_success "Database setup complete"

# Step 4: Start development server
print_step "4. Starting development server..."

# Kill any existing server on port 8000
if lsof -Pi :8000 -sTCP:LISTEN -t >/dev/null 2>&1; then
    print_warning "Port 8000 is already in use. Attempting to kill existing process..."
    kill -9 $(lsof -Pi :8000 -sTCP:LISTEN -t) 2>/dev/null || true
    sleep 2
fi

# Start the server in background
print_info "Starting Laravel development server..."
php artisan serve --port=8000 > /dev/null 2>&1 &
SERVER_PID=$!

# Store PID for cleanup
echo $SERVER_PID > .server_pid

# Wait for server to be ready
if ! wait_for_server "http://localhost:8000"; then
    print_error "Failed to start server"
    exit 1
fi

print_success "Development server started on http://localhost:8000 (PID: $SERVER_PID)"

# Step 5: Run tests
print_step "5. Running comprehensive tests..."

print_info "Running PHPUnit tests..."
php artisan test --stop-on-failure

print_success "All tests passed!"

# Step 6: API demonstration
print_step "6. Demonstrating API endpoints..."

echo "🚀 Live API Demonstration"
echo "========================="
echo

# GET all trees (should show seeded data)
api_call "GET" "/api/tree" "" "Get all trees (initial seeded data)"

# Create a new root node
api_call "POST" "/api/tree" '{"label": "Demo Root Node"}' "Create a new root node"

# Create a child node (assuming parent ID 1 exists from seeding)
api_call "POST" "/api/tree" '{"label": "Demo Child Node", "parentId": 1}' "Create a child node under root"

# Create another child
api_call "POST" "/api/tree" '{"label": "Another Child", "parentId": 1}' "Create another child under root"

# Create a grandchild (assuming the demo child node got ID 2 or higher)
api_call "POST" "/api/tree" '{"label": "Demo Grandchild", "parentId": 2}' "Create a grandchild node"

# Get all trees again to show the updated structure
api_call "GET" "/api/tree" "" "Get all trees (after adding demo nodes)"

# Test validation by sending invalid data
print_info "Testing validation with invalid data..."
api_call "POST" "/api/tree" '{"label": ""}' "Test validation - empty label (should fail)"
api_call "POST" "/api/tree" '{"label": "Valid Label", "parentId": 99999}' "Test validation - invalid parent ID (should fail)"

# Step 7: Performance demonstration
print_step "7. Performance demonstration..."

print_info "Creating multiple nodes to demonstrate performance..."
for i in {1..5}; do
    api_call "POST" "/api/tree" "{\"label\": \"Performance Test Node $i\", \"parentId\": 1}" "Create performance test node $i"
done

# Final tree structure
api_call "GET" "/api/tree" "" "Final tree structure"

# Step 8: Cleanup
print_step "8. Cleanup..."

# Function to cleanup on exit
cleanup() {
    if [ -f ".server_pid" ]; then
        SERVER_PID=$(cat .server_pid)
        if kill -0 "$SERVER_PID" 2>/dev/null; then
            print_info "Stopping development server (PID: $SERVER_PID)..."
            kill "$SERVER_PID"
        fi
        rm -f .server_pid
    fi
}

# Set up cleanup on script exit
trap cleanup EXIT

print_success "Demo completed successfully!"
echo
echo "📋 Summary:"
echo "==========="
echo "✅ Environment setup completed"
echo "✅ Database migrated and seeded"
echo "✅ Development server started"
echo "✅ All tests passed"
echo "✅ API endpoints demonstrated"
echo "✅ Validation tested"
echo "✅ Performance demonstrated"
echo
echo "🔗 API Endpoints:"
echo "  GET  http://localhost:8000/api/tree"
echo "  POST http://localhost:8000/api/tree"
echo
echo "📖 For more information, see README.md"
echo

# Keep server running for manual testing
read -p "Press Enter to stop the server and exit..."
