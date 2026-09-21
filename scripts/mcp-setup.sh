#!/usr/bin/env bash
# Claude Code'u yerel WordPress MCP sunucusuna baglar.
#
# Calistirma (proje kokunden, Git Bash veya WSL):
#   bash scripts/mcp-setup.sh
#
# Yaptiklari:
#   1. MCP Adapter eklentisinin kurulu ve etkin oldugunu dogrular
#   2. admin kullanicisi icin yeni bir uygulama parolasi uretir
#      (ayni isimli eskiler silinir; parola yalnizca bu terminalde gorunur)
#   3. 'claude mcp add' ile sunucuyu yerel kapsamda kaydeder
#
# Uygulama parolasi WordPress'in kendi ozelligidir; ayri bir token sistemi yok.
# Kayit 'local' kapsamda yapilir, boylece kimlik bilgisi depoya girmez.

set -euo pipefail

cd "$(dirname "$0")/.."

WP_HOST="${WP_HOST:-localhost:8080}"
SERVER_NAME="${MCP_SERVER_NAME:-nwcs-studio}"
MCP_URL="http://${WP_HOST}/wp-json/nwcs/v1/mcp"
LABEL="Claude Code MCP"

compose() {
	MSYS_NO_PATHCONV=1 docker compose "$@"
}

echo "==> MCP Adapter eklentisi kontrol ediliyor"
if ! compose --profile cli run --rm wpcli --path=/var/www/html plugin is-installed mcp-adapter >/dev/null 2>&1; then
	echo "    kuruluyor (GitHub sürümünden)"
	compose --profile cli run --rm wpcli --path=/var/www/html plugin install \
		"https://github.com/WordPress/mcp-adapter/releases/download/v0.6.1/mcp-adapter.zip" >/dev/null
fi
compose --profile cli run --rm wpcli --path=/var/www/html plugin activate mcp-adapter --network >/dev/null 2>&1 || true

echo "==> uygulama parolası üretiliyor"
# Ayni isimli eski parolalari temizle ki liste sismesin.
compose --profile cli run --rm wpcli --path=/var/www/html eval \
	"\$user = get_user_by( 'login', getenv( 'WP_ADMIN_USER' ) ?: 'admin' );
	 foreach ( WP_Application_Passwords::get_user_application_passwords( \$user->ID ) as \$item ) {
	 	if ( '${LABEL}' === \$item['name'] ) { WP_Application_Passwords::delete_application_password( \$user->ID, \$item['uuid'] ); }
	 }" >/dev/null 2>&1 || true

APP_PASSWORD="$(compose --profile cli run --rm wpcli --path=/var/www/html \
	user application-password create "${WP_ADMIN_USER:-admin}" "${LABEL}" --porcelain 2>/dev/null | tr -d '\r\n')"

if [ -z "$APP_PASSWORD" ]; then
	echo "HATA: uygulama parolası üretilemedi. WordPress çalışıyor mu?" >&2
	exit 1
fi

AUTH_HEADER="Authorization: Basic $(printf '%s:%s' "${WP_ADMIN_USER:-admin}" "$APP_PASSWORD" | base64 -w0 2>/dev/null || printf '%s:%s' "${WP_ADMIN_USER:-admin}" "$APP_PASSWORD" | base64)"

echo "==> MCP sunucusu Claude Code'a ekleniyor: ${SERVER_NAME}"
if command -v claude >/dev/null 2>&1; then
	claude mcp remove "${SERVER_NAME}" --scope local >/dev/null 2>&1 || true
	claude mcp add --scope local --transport http "${SERVER_NAME}" "${MCP_URL}" --header "${AUTH_HEADER}"
	echo ""
	echo "Tamam. Claude Code'u yeniden başlatın, ardından /mcp ile bağlantıyı görebilirsiniz."
else
	echo ""
	echo "'claude' komutu PATH'te bulunamadı. Aşağıdaki komutu kendi terminalinizde çalıştırın:"
	echo ""
	echo "claude mcp add --scope local --transport http ${SERVER_NAME} ${MCP_URL} --header \"${AUTH_HEADER}\""
	echo ""
fi

echo "Sunulan araçlar: nwcs-list-sites, nwcs-describe-site, nwcs-get-field, nwcs-update-field"
echo "Örnek istek: \"Paletçi ana sayfasındaki hero başlığını 'X' yap\""
