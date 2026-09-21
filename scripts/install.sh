#!/bin/sh
# Multisite agini ve iki demo siteyi kurar. Tekrar calistirilabilir.
# Calistirma (proje kokunden):
#   docker compose --profile cli run --rm --entrypoint sh wpcli /scripts/install.sh

set -eu

WP_PATH=/var/www/html
WP_HOST="${WP_HOST:-localhost:8080}"
BASE_URL="http://${WP_HOST}"
ADMIN_USER="${WP_ADMIN_USER:-admin}"
ADMIN_PASSWORD="${WP_ADMIN_PASSWORD:-admin123}"
ADMIN_EMAIL="${WP_ADMIN_EMAIL:-admin@example.test}"

wpc() {
	wp --path="$WP_PATH" "$@"
}

echo "==> wp-config.php bekleniyor"
i=0
while [ ! -f "$WP_PATH/wp-config.php" ]; do
	i=$((i + 1))
	if [ "$i" -gt 60 ]; then
		echo "HATA: wp-config.php olusmadi. 'docker compose up -d' calisiyor mu?" >&2
		exit 1
	fi
	sleep 2
done

echo "==> veritabani bekleniyor"
i=0
until wpc db check >/dev/null 2>&1; do
	i=$((i + 1))
	if [ "$i" -gt 60 ]; then
		echo "HATA: veritabanina baglanilamadi." >&2
		exit 1
	fi
	sleep 2
done

if wpc core is-installed 2>/dev/null; then
	echo "==> WordPress zaten kurulu"
else
	echo "==> WordPress kuruluyor"
	wpc core install \
		--url="$BASE_URL" \
		--title="Demo Ağı" \
		--admin_user="$ADMIN_USER" \
		--admin_password="$ADMIN_PASSWORD" \
		--admin_email="$ADMIN_EMAIL" \
		--skip-email
fi

if wpc core is-installed --network 2>/dev/null; then
	echo "==> Multisite agi zaten kurulu"
else
	echo "==> Multisite agina cevriliyor (alt dizin modu)"
	wpc core multisite-convert --title="Demo Ağı" --skip-config

	# wp-config sabitleri docker-compose icindeki WORDPRESS_CONFIG_EXTRA ile
	# tanimlanir; bu isaret dosyasi onlari devreye alir.
	touch "$WP_PATH/.multisite-ready"

	cat > "$WP_PATH/.htaccess" <<'HTACCESS'
# WordPress Multisite (alt dizin) yeniden yazma kurallari
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteBase /
RewriteRule ^index\.php$ - [L]

# wp-admin icin sondaki slash
RewriteRule ^([_0-9a-zA-Z-]+/)?wp-admin$ $1wp-admin/ [R=301,L]

RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]
RewriteRule ^([_0-9a-zA-Z-]+/)?(wp-(content|admin|includes).*) $2 [L]
RewriteRule ^([_0-9a-zA-Z-]+/)?(.*\.php)$ $2 [L]
RewriteRule . index.php [L]
HTACCESS
fi

echo "==> temalar ag genelinde etkinlestiriliyor"
wpc theme enable kocist-theme --network >/dev/null
wpc theme enable paletci-theme --network >/dev/null

echo "==> eklenti ag genelinde etkinlestiriliyor"
wpc plugin activate network-content-studio --network >/dev/null

# MCP Adapter (resmi WordPress eklentisi) — Claude Code baglantisi icin.
# Internet erisimi yoksa kurulum atlanir; panel ve siteler bundan etkilenmez.
if wpc plugin is-installed mcp-adapter >/dev/null 2>&1; then
	wpc plugin activate mcp-adapter --network >/dev/null 2>&1 || true
else
	echo "==> MCP Adapter kuruluyor (istege bagli)"
	if wpc plugin install "https://github.com/WordPress/mcp-adapter/releases/download/v0.6.1/mcp-adapter.zip" >/dev/null 2>&1; then
		wpc plugin activate mcp-adapter --network >/dev/null 2>&1 || true
	else
		echo "    atlandi: indirilemedi (MCP olmadan da her sey calisir)"
	fi
fi

create_site() {
	slug="$1"
	title="$2"

	if wpc site list --field=url | grep -q "${BASE_URL}/${slug}/"; then
		echo "==> /${slug}/ sitesi zaten var"
	else
		echo "==> /${slug}/ sitesi olusturuluyor"
		wpc site create --slug="$slug" --title="$title" --email="$ADMIN_EMAIL" >/dev/null
	fi
}

create_site kocist "Koçist"
create_site paletci "İstanbul Paletçi"

setup_site() {
	slug="$1"
	theme="$2"
	site_url="${BASE_URL}/${slug}/"

	echo "==> ${slug}: tema atanyor ve icerik seed ediliyor"
	wpc theme activate "$theme" --url="$site_url" >/dev/null
	wpc rewrite structure '/%postname%/' --url="$site_url" >/dev/null
	wpc eval-file /scripts/seed.php --url="$site_url"
}

setup_site kocist kocist-theme
setup_site paletci paletci-theme

echo ""
echo "Kurulum tamam."
echo "  Koçist        : ${BASE_URL}/kocist/"
echo "  İstanbul Paletçi: ${BASE_URL}/paletci/"
echo "  Ağ yönetimi   : ${BASE_URL}/wp-admin/network/"
echo "  Giriş         : ${BASE_URL}/wp-login.php  (kullanıcı: ${ADMIN_USER})"
