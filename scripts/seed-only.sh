#!/bin/sh
# Demo icerigini yeniden uretir (kurulumu bozmadan).
#   docker compose --profile cli run --rm --entrypoint sh wpcli /scripts/seed-only.sh

set -eu

WP_PATH=/var/www/html
BASE_URL="http://${WP_HOST:-localhost:8080}"

wp --path="$WP_PATH" eval-file /scripts/seed.php --url="${BASE_URL}/kocist/"
wp --path="$WP_PATH" eval-file /scripts/seed.php --url="${BASE_URL}/paletci/"
