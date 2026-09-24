#!/bin/sh
# Multisite agini ve gercek siteleri kurar. Tekrar calistirilabilir: var olan
# bir sitenin icerigine dokunmaz (seed yalnizca site ilk kez olusurken calisir).
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

# Gercek siteler: <slug>|<tema>|<baslik>|<seed betigi>
# Yeni bir site eklerken bu listeye bir satir eklemek yeterli. Seed yerine "-"
# yazilirsa tema kendi kurulumunu yapar (sayfalarini after_switch_theme ile acar).
SITES='
ahsapkasa|ahsapkasa-theme|Koçist Orman Ürünleri|seed-ahsapkasa.php
istanbulpaletci|istanbulpaletci-theme|İstanbul Paletçi|seed-istanbulpaletci.php
ithalkeresteci|ithalkeresteci-theme|İthal Keresteci|seed-ithalkeresteci.php
kavakkeresteci|kavakkeresteci-theme|Kavak Keresteci|seed-kavakkeresteci.php
kocist|kocist-theme|Koçist Orman Ürünleri|-
istanbul-keresteci|istanbul-keresteci-theme|İstanbul Keresteci|-
sanayi-palet|sanayi-palet-theme|Sanayi Palet|-
ahsapambalaj|ahsapambalaj-theme|Ahşap Ambalaj|-
istanbulpaletcivi|istanbulpaletcivi-theme|İstanbul Palet Çivi|-
'

echo "==> Turkce dil paketi"
wpc language core install tr_TR >/dev/null 2>&1 || echo "    atlandi: indirilemedi"

echo "$SITES" | while IFS='|' read -r slug theme title seed; do
	[ -n "$slug" ] || continue
	site_url="${BASE_URL}/${slug}/"

	# Var olan sitenin icerigine dokunulmaz: seed alanlari varsayilana geri yazar.
	if wpc site list --field=url | grep -q "$site_url"; then
		echo "==> /${slug}/ zaten var; icerige dokunulmuyor"
		continue
	fi

	echo "==> /${slug}/ kuruluyor"
	wpc site create --slug="$slug" --title="$title" --email="$ADMIN_EMAIL" >/dev/null
	wpc theme enable "$theme" --network >/dev/null
	wpc theme activate "$theme" --url="$site_url" >/dev/null
	wpc site switch-language tr_TR --url="$site_url" >/dev/null 2>&1 || true
	wpc option update timezone_string Europe/Istanbul --url="$site_url" >/dev/null
	wpc rewrite structure '/%postname%/' --url="$site_url" >/dev/null
	if [ "$seed" = "-" ]; then
		# Tema etkinlestikten sonraki ilk yuklemede WordPress after_switch_theme
		# kancasini calistirir; tema sayfalarini o anda acar.
		wpc eval 'echo "";' --url="$site_url" >/dev/null
		# WordPress'in ornek yazisi ve sayfasi (seed'li sitelerde seed siler).
		wpc eval 'foreach ( array( "post" => "hello-world", "page" => "sample-page" ) as $t => $n ) { $p = get_page_by_path( $n, OBJECT, $t ); if ( $p ) { wp_delete_post( $p->ID, true ); } }' --url="$site_url"
	else
		wpc eval-file "/scripts/${seed}" --url="$site_url"
	fi
done

echo ""
echo "Kurulum tamam."
echo "  ahsapkasa      : ${BASE_URL}/ahsapkasa/"
echo "  istanbulpaletci: ${BASE_URL}/istanbulpaletci/"
echo "  ithalkeresteci : ${BASE_URL}/ithalkeresteci/"
echo "  kavakkeresteci : ${BASE_URL}/kavakkeresteci/"
echo "  kocist         : ${BASE_URL}/kocist/"
echo "  istanbul-keresteci: ${BASE_URL}/istanbul-keresteci/"
echo "  sanayi-palet   : ${BASE_URL}/sanayi-palet/"
echo "  ahsapambalaj   : ${BASE_URL}/ahsapambalaj/"
echo "  istanbulpaletcivi: ${BASE_URL}/istanbulpaletcivi/"
echo "  Ağ yönetimi    : ${BASE_URL}/wp-admin/network/"
echo "  Giriş          : ${BASE_URL}/wp-login.php  (kullanıcı: ${ADMIN_USER})"
