#!/usr/bin/env bash
# Backs up the Railway Postgres database, uploaded files and service variables.
# Requires: railway CLI (logged in, `railway link` to this project/environment),
# pg_dump >= 18, tar, base64.
set -euo pipefail

APP_SERVICE="${APP_SERVICE:-sincere-balance}"
DB_SERVICE="${DB_SERVICE:-Postgres}"
UPLOAD_PATH="${UPLOAD_PATH:-/var/www/html/storage/app/public}"
OUT="${1:-backup-$(date +%Y%m%d-%H%M%S)}"

for bin in railway pg_dump tar base64; do
  command -v "$bin" >/dev/null || { echo "missing: $bin" >&2; exit 1; }
done

mkdir -p "$OUT"
umask 077

echo "==> Database"
DB_URL="$(railway variables --service "$DB_SERVICE" --kv | sed -n 's/^DATABASE_PUBLIC_URL=//p')"
[ -n "$DB_URL" ] || { echo "DATABASE_PUBLIC_URL not found: enable the TCP proxy on $DB_SERVICE" >&2; exit 1; }
pg_dump "$DB_URL" --format=custom --no-owner --no-acl -f "$OUT/database.dump"

echo "==> Uploaded files ($UPLOAD_PATH)"
railway ssh --service "$APP_SERVICE" -- "tar czf - -C '$UPLOAD_PATH' . | base64" \
  | tr -d '\r' | base64 -d > "$OUT/uploads.tar.gz"
tar tzf "$OUT/uploads.tar.gz" >/dev/null

echo "==> Variables"
railway variables --service "$APP_SERVICE" --kv > "$OUT/app.env"
railway variables --service "$DB_SERVICE" --kv > "$OUT/postgres.env"

echo "Done: $OUT (contains secrets, keep it private, do not commit)"
echo "Restore: pg_restore --no-owner --no-acl -d <NEW_DB_URL> $OUT/database.dump"
echo "         tar xzf $OUT/uploads.tar.gz -C <new storage/app/public>"
