#!/bin/bash

# ============================================================================
# reset-inventory.sh - Reset database and uploaded files
# Usage: ./reset-inventory.sh [PROJECT_PATH]
# ============================================================================

# Default values
PROJECT_PATH="${1:-${PROJECT_PATH:-/volume1/web/inventory-web}}"
DB_FILE="$PROJECT_PATH/data/inventory.db"
UPLOADS_DIR="$PROJECT_PATH/data/uploads"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${RED}⚠️  WARNING: This will delete ALL computers, reports, and uploaded files!${NC}"
echo -e "${YELLOW}The users table (admin, warehouse) will be preserved.${NC}"
echo ""
read -p "Are you sure? (type 'yes' to confirm): " confirm

if [[ $confirm != "yes" ]]; then
    echo "Cancelled."
    exit 1
fi

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    echo -e "${YELLOW}This script requires root privileges. Running via sudo...${NC}"
    exec sudo "$0" "$@"
fi

# Check if database exists
if [ ! -f "$DB_FILE" ]; then
    echo -e "${RED}Error: Database file $DB_FILE does not exist${NC}"
    exit 1
fi

echo "Cleaning database tables..."
sqlite3 "$DB_FILE" "
    DELETE FROM transfers; 
    DELETE FROM computers; 
    DELETE FROM processed_uploads; 
    DELETE FROM sqlite_sequence WHERE name IN ('computers', 'processed_uploads', 'transfers');
"

echo "Deleting uploaded JSON files..."
if [ -d "$UPLOADS_DIR" ]; then
    rm -f "$UPLOADS_DIR"/*
fi

echo -e "${GREEN}✅ Database and uploads folder successfully cleaned!${NC}"