#!/bin/sh
set -e

host="$(php -r 'echo parse_url((string) getenv("APP_URL"), PHP_URL_HOST) ?: "localhost";')"

curl --fail --silent --show-error --max-time 5 \
    --header "Host: ${host}" \
    http://127.0.0.1/up >/dev/null
