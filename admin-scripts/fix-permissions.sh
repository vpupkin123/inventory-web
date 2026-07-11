#!/bin/bash

# ============================================================================
# fix-permissions.sh - Universal script for fixing file permissions
# Usage: ./fix-permissions.sh [PROJECT_PATH] [OWNER] [GROUP] [WEB_USER]
# ============================================================================

# Default values (can be overridden via arguments or environment variables)
PROJECT_PATH="${1:-${PROJECT_PATH:-/volume1/web/inventory-web}}"
OWNER="${2:-${OWNER:-$(whoami)}}"
GROUP="${3:-${GROUP:-$(id -gn)}}"
WEB_USER="${4:-${WEB_USER:-http}}"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${YELLOW}=== Fixing permissions for Inventory Web ===${NC}"
echo -e "Project path: ${PROJECT_PATH}"
echo -e "Owner: ${OWNER}:${GROUP}"
echo -e "Web user: ${WEB_USER}"
echo ""

# Check if project path exists
if [ ! -d "$PROJECT_PATH" ]; then
    echo -e "${RED}Error: Project path $PROJECT_PATH does not exist${NC}"
    exit 1
fi

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    echo -e "${YELLOW}This script requires root privileges. Running via sudo...${NC}"
    exec sudo "$0" "$@"
fi

# 1. Set ownership for entire project
echo "1. Setting project owner: ${OWNER}:${GROUP}"
chown -R ${OWNER}:${GROUP} "$PROJECT_PATH"
echo -e "${GREEN}   ✓ Done${NC}"

# 2. Set ownership for data/ folder to web user
echo "2. Setting data/ owner: ${WEB_USER}:${WEB_USER}"
chown -R ${WEB_USER}:${WEB_USER} "$PROJECT_PATH/data"
echo -e "${GREEN}   ✓ Done${NC}"

# 3. Set permissions for all files (644)
echo "3. Setting file permissions: 644"
find "$PROJECT_PATH" -type f -exec chmod 644 {} \;
echo -e "${GREEN}   ✓ Done${NC}"

# 4. Set permissions for all directories (755)
echo "4. Setting directory permissions: 755"
find "$PROJECT_PATH" -type d -exec chmod 755 {} \;
echo -e "${GREEN}   ✓ Done${NC}"

# 5. Set permissions for data/ folder (775)
echo "5. Setting data/ permissions: 775"
chmod 775 "$PROJECT_PATH/data"
echo -e "${GREEN}   ✓ Done${NC}"

# 6. Set permissions for files inside data/ (664)
echo "6. Setting data/ file permissions: 664"
find "$PROJECT_PATH/data" -type f -exec chmod 664 {} \;
echo -e "${GREEN}   ✓ Done${NC}"

echo ""
echo -e "${GREEN}=== All permissions fixed! ===${NC}"
echo ""

# Show summary
echo "Current permissions:"
ls -la "$PROJECT_PATH/"
echo ""
echo "Data folder permissions:"
ls -la "$PROJECT_PATH/data/"