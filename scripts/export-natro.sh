#!/bin/sh
# Natro (cPanel) deneme kurulumu icin tasima paketi hazirlar. Yerel veritabani
# DEGISMEZ: islemler gecici bir kopya (wp_export) uzerinde yapilir.
#
#   DOMAIN=panel.kocist.com.tr ADMIN_EMAIL=ad@firma.com sh scripts/export-natro.sh
#
# Canliya cikacak siteler (domain mapping): MAP="istanbul-keresteci=istanbulkeresteci.com"
# Bu siteler https://alanadi adresine baglanir ve arama motorlarina acilir;
# alan adinin SSL sertifikasi sunucuda kurulu olmali.
#
# SSL henuz yoksa (yalnizca deneme): SCHEME=http ekleyin. Adresler http://
# yazilir ve yonetimde HTTPS zorunlulugu konmaz. Sertifika gelince paket
# SCHEME=https (varsayilan) ile yeniden uretilip kurulur.
#
# Proje kokunden, Docker calisirken. Git Bash'te MSYS_NO_PATHCONV=1 betik icinde
# ayarlanir. Cikti: dist/natro-<tarih>/ (Git'e girmez)
#
#   natro.sql            canliya hazir veritabani: adresler https://$DOMAIN,
#                        8 sitenin hepsi arama motorlarina KAPALI (deneme),
#                        MCP uygulama parolalari silinmis, MCP eklentisi kapali,
#                        yonetici parolasi yeni (asagidaki dosyada)
#   wp-content.tar.gz    temalar ve eklenti (Git'teki HEAD), yuklemeler, dil paketi
#   wp-config-ek.php     wp-config.php'ye eklenecek ag ve guvenlik ayarlari
#   htaccess.txt         .htaccess (cok siteli ag kurallari)
#   YONETICI-PAROLASI.txt  yeni yonetici parolasi (ekrana yazilmaz; paylasmayin)
#   KURULUM.md           cPanel adimlari
#   yerel-yedek.sql      yerel veritabaninin dokunulmamis yedegi

set -eu
export MSYS_NO_PATHCONV=1

DOMAIN="${DOMAIN:?DOMAIN gerekli, ornek: DOMAIN=panel.kocist.com.tr}"
ADMIN_EMAIL="${ADMIN_EMAIL:-}"
SCHEME="${SCHEME:-https}"
LOCAL_HOST="${LOCAL_HOST:-localhost:8080}"
STAMP="$(date +%Y%m%d-%H%M)"
OUT="dist/natro-$STAMP"
TMPDB="wp_export"

case "$SCHEME" in
	http|https) ;;
	*) echo "HATA: SCHEME http ya da https olmali." >&2; exit 1 ;;
esac

case "$DOMAIN" in
	*/*|*:*|"") echo "HATA: DOMAIN yalnizca alan adi olmali (https:// ve / olmadan)." >&2; exit 1 ;;
esac

[ -f docker-compose.yml ] || { echo "HATA: proje kokunden calistirin." >&2; exit 1; }

mkdir -p "$OUT"
echo "==> Paket: $OUT"

wpc() {
	docker compose --profile cli run --rm -T "$@" 2>/dev/null
}

dbroot() {
	docker exec -i dwm-db sh -c 'mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" '"$*"
}

cleanup() {
	dbroot -e "\"DROP DATABASE IF EXISTS $TMPDB;\"" >/dev/null 2>&1 || true
}
trap cleanup EXIT

echo "==> Yerel veritabani yedegi"
wpc wpcli db export - > "$OUT/yerel-yedek.sql"
tail -c 200 "$OUT/yerel-yedek.sql" | grep -q "Dump completed" || { echo "HATA: yedek eksik." >&2; exit 1; }

echo "==> Gecici kopya ($TMPDB)"
dbroot -e "\"DROP DATABASE IF EXISTS $TMPDB; CREATE DATABASE $TMPDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; GRANT ALL ON $TMPDB.* TO '\$MARIADB_USER'@'%';\""
dbroot "$TMPDB" < "$OUT/yerel-yedek.sql"

# Kopya uzerinde calisan wp-cli: once eski alan adiyla, sonra yenisiyle acilir.
old() { wpc -e WORDPRESS_DB_NAME="$TMPDB" -e WP_HOST="$LOCAL_HOST" wpcli "$@"; }
new() { wpc -e WORDPRESS_DB_NAME="$TMPDB" -e WP_HOST="$DOMAIN" wpcli "$@"; }

echo "==> Adresler: $LOCAL_HOST -> $SCHEME://$DOMAIN (serilestirilmis veri korunur)"
old search-replace "$LOCAL_HOST" "$DOMAIN" --network --all-tables-with-prefix --skip-columns=guid --report-changed-only | tail -1
if [ "$SCHEME" = "https" ]; then
	new search-replace "http://$DOMAIN" "https://$DOMAIN" --network --all-tables-with-prefix --skip-columns=guid --report-changed-only | tail -1
fi

echo "==> Deneme: butun siteler arama motorlarina kapali"
for url in $(new site list --field=url | tr -d '\r'); do
	new option update blog_public 0 --url="$url" >/dev/null
done

# Canliya cikan siteler: kendi alan adina baglanir (domain mapping), adresleri
# https://alanadi olur, arama motorlarina acilir. Diger siteler panel altinda
# ve kapali kalir. MAP="slug=alanadi slug=alanadi"
if [ -n "${MAP:-}" ]; then
	echo "==> Alan adina baglanan siteler: $MAP"
	for pair in $MAP; do
		slug="${pair%%=*}"
		dom="${pair#*=}"
		case "$dom" in */*|*:*|"") echo "HATA: MAP'te alan adi hatali: $pair" >&2; exit 1 ;; esac

		id="$(new db query "SELECT blog_id FROM wp_blogs WHERE path='/$slug/'" --skip-column-names | tr -d '\r')"
		[ -n "$id" ] || { echo "HATA: /$slug/ adli site yok." >&2; exit 1; }

		# Ag genelinde: bu sitenin panel adresleri, diger sitelerden verilen
		# baglantilar dahil, canli alan adina.
		new search-replace "$SCHEME://$DOMAIN/$slug/" "https://$dom/" --network --all-tables-with-prefix --skip-columns=guid --report-changed-only | tail -1
		new search-replace "$SCHEME://$DOMAIN/$slug" "https://$dom" --network --all-tables-with-prefix --skip-columns=guid --report-changed-only | tail -1
		new db query "UPDATE wp_blogs SET domain='$dom', path='/' WHERE blog_id=$id"

		new option update home "https://$dom" --url="https://$dom/" >/dev/null
		new option update siteurl "https://$dom" --url="https://$dom/" >/dev/null
		new option update blog_public 1 --url="https://$dom/" >/dev/null
		# Kayitli yeniden yazma kurallari alt dizin yoluyla uretilmisti; kok
		# adreste robots.txt kurali yok. Silinince ilk istekte yeniden uretilir.
		new option delete rewrite_rules --url="https://$dom/" >/dev/null 2>&1 || true

		# Yalnizca sitenin kendi tablolari: agin ortak tablolarinda (wp_blogs,
		# wp_site...) panel adresi olmasi dogru, ag panelde yasiyor.
		KALAN="$(new search-replace "$DOMAIN" x "wp_${id}_*" --url="https://$dom/" --skip-columns=guid --dry-run | tail -1)"
		case "$KALAN" in
			*" 0 replacements"*) echo "    /$slug/ -> https://$dom (site $id): panel adresi kalmadi, arama motorlarina ACIK" ;;
			*) echo "UYARI: https://$dom icinde panel adresi kaldi: $KALAN" >&2 ;;
		esac
	done
fi

echo "==> MCP: uygulama parolalari silinir, eklenti kapatilir"
for id in $(new user list --field=ID --network | tr -d '\r'); do
	new user meta delete "$id" _application_passwords >/dev/null 2>&1 || true
done
new plugin deactivate mcp-adapter --network >/dev/null 2>&1 || true

echo "==> Yonetici parolasi yenilenir (ekrana yazilmaz)"
PASS="$(od -An -tx1 -N18 /dev/urandom | tr -d ' \n')"
ADMIN_ID="$(new user list --role=administrator --field=ID --network | tr -d '\r' | head -1)"
[ -n "$ADMIN_ID" ] || ADMIN_ID=1
new user update "$ADMIN_ID" --user_pass="$PASS" --skip-email >/dev/null
if [ -n "$ADMIN_EMAIL" ]; then
	new user update "$ADMIN_ID" --user_email="$ADMIN_EMAIL" --skip-email >/dev/null
	new network meta update 1 admin_email "$ADMIN_EMAIL" >/dev/null
fi
ADMIN_LOGIN="$(new user get "$ADMIN_ID" --field=user_login | tr -d '\r')"
umask 077
printf 'Kullanici: %s\nParola: %s\n\nIlk giristen sonra degistirin. Bu dosyayi paylasmayin, kurulumdan sonra silin.\n' "$ADMIN_LOGIN" "$PASS" > "$OUT/YONETICI-PAROLASI.txt"
umask 022

new transient delete --all --network >/dev/null 2>&1 || true

echo "==> Canliya hazir veritabani"
new db export - > "$OUT/natro.sql"
tail -c 200 "$OUT/natro.sql" | grep -q "Dump completed" || { echo "HATA: canli dokumu eksik." >&2; exit 1; }
# Yazilarin guid sutunu bilerek degistirilmez (WordPress onerisi: guid kalici
# kimliktir, adres degildir). Baska yerde yerel adres kalmadigini dogrula.
LEFT="$(new search-replace "$LOCAL_HOST" x --network --all-tables-with-prefix --skip-columns=guid --dry-run | tail -1)"
case "$LEFT" in
	*" 0 replacements"*) echo "    guid disinda yerel adres kalmadi." ;;
	*) echo "UYARI: guid disinda yerel adres kaldi: $LEFT" >&2 ;;
esac

echo "==> wp-content arsivi (temalar ve eklenti Git'ten, yuklemeler ve dil paketi yerelden)"
STAGE="$OUT/.stage"
mkdir -p "$STAGE/wp-content/themes" "$STAGE/wp-content/plugins"
git archive HEAD wp-content/themes wp-content/plugins/network-content-studio | tar -x -C "$STAGE"
HOSTDIR="$(pwd -W 2>/dev/null || pwd)"  # Git Bash: Windows yolu (C:/...)
docker run --rm -v demo-wordpress-multisite_wp_core:/src:ro -v "$HOSTDIR/$STAGE/wp-content:/dst" alpine \
	sh -c 'cp -r /src/wp-content/uploads /dst/ && { [ -d /src/wp-content/languages ] && cp -r /src/wp-content/languages /dst/ || true; }'
tar -czf "$OUT/wp-content.tar.gz" -C "$STAGE" wp-content
rm -rf "$STAGE"

echo "==> wp-config eki ve .htaccess"
cat > "$OUT/wp-config-ek.php" <<PHP
/* ---- Network Content Studio agi (Natro). "That's all, stop editing!"
   satirindan ONCE ekleyin; veritabani bilgileri ve guvenlik anahtarlari
   Natro'nun olusturdugu wp-config.php'de kalir. ---- */
define( 'WP_ALLOW_MULTISITE', true );
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', false );
define( 'DOMAIN_CURRENT_SITE', '$DOMAIN' );
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );

define( 'WP_ENVIRONMENT_TYPE', 'production' );
define( 'DISALLOW_FILE_EDIT', true );
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_DISPLAY', false );
PHP
if [ "$SCHEME" = "https" ]; then
	echo "define( 'FORCE_SSL_ADMIN', true );" >> "$OUT/wp-config-ek.php"
else
	echo "/* SSL yok (deneme): FORCE_SSL_ADMIN bilerek eklenmedi. Sertifika gelince https paketi kurulur. */" >> "$OUT/wp-config-ek.php"
fi

: > "$OUT/htaccess.txt"
if [ -n "${MAP:-}" ]; then
	# Canli alan adlari tek adreste: http -> https, www -> www'suz (301). Ayni
	# sayfa iki adreste acilmasin (kopya icerik). Yalnizca bu alan adlari;
	# SSL'i olmayan panel adresi etkilenmez.
	HOSTS="$(for pair in $MAP; do printf '%s|' "${pair#*=}"; done | sed 's/|$//; s/\./\\./g')"
	cat >> "$OUT/htaccess.txt" <<HT
# Canli alan adlari: tek adres (https, www'suz)
RewriteEngine On
RewriteCond %{HTTP_HOST} ^www\.($HOSTS)$ [NC]
RewriteRule ^ https://%1%{REQUEST_URI} [R=301,L]
RewriteCond %{HTTPS} !=on
RewriteCond %{HTTP:X-Forwarded-Proto} !=https
RewriteCond %{HTTP_HOST} ^($HOSTS)$ [NC]
RewriteRule ^ https://%1%{REQUEST_URI} [R=301,L]

HT
fi
# Kullanilmayan kapilar kapali: xmlrpc (toplu parola denemesi), surum bilgisi
# veren dosyalar. X-Frame-Options bilerek yok: panel, alan adina bagli siteyi
# onizleme cercevesinde aciyor.
cat >> "$OUT/htaccess.txt" <<'HT'
# Guvenlik
Options -Indexes
RewriteEngine On
RewriteRule ^([_0-9a-zA-Z-]+/)?(xmlrpc\.php|readme\.html|license\.txt|wp-config-sample\.php)$ - [F,L]
<IfModule mod_headers.c>
Header always set X-Content-Type-Options "nosniff"
Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

HT
sed -n "/^# WordPress Multisite/,/^RewriteRule \. index.php \[L\]/p" scripts/install.sh >> "$OUT/htaccess.txt"
grep -q "RewriteRule \. index.php" "$OUT/htaccess.txt" || { echo "HATA: .htaccess kurallari install.sh'ten okunamadi." >&2; exit 1; }

cat > "$OUT/KURULUM.md" <<MD
# Natro deneme kurulumu ($SCHEME://$DOMAIN)

Paket: $STAMP. Butun siteler arama motorlarina KAPALI kurulur (deneme).
$( [ "$SCHEME" = "http" ] && printf '%s' '**SSL yok (http paketi):** giris parolasi sifresiz gider; sertifika gelince https paketini kurun ve parolayi degistirin. 8. adimi (SSL) atlayin.' )

0. cPanel -> PHP Surumu Sec / MultiPHP Yoneticisi: \`$DOMAIN\` icin PHP 8.1 ya da uzeri (8.2/8.3 onerilir).

1. cPanel -> Alan adlari: \`$DOMAIN\` kok klasoru bos bir klasor olsun (ornek \`public_html/panel\`).
2. cPanel -> MySQL veritabanlari: veritabani + kullanici olusturun, kullaniciya TUM yetkileri verin.
   (Natro'nun WordPress kurucusunu kullandiysaniz olusturdugu veritabanini kullanin.)
3. WordPress cekirdegi o klasorde olsun (kurucu ya da wordpress.org zip). Kurucu kullandiysaniz
   yonetici hesabi onemli degil; veritabani bizimkiyle degisecek.
4. Dosya Yoneticisi: \`wp-content\` klasorunu SILIN, \`wp-content.tar.gz\` yukleyip ayni yere acin.
5. phpMyAdmin: veritabanindaki tablolari silin (ya da bos veritabani), \`natro.sql\` ice aktarin.
   Tablo oneki \`wp_\` olmali; farkliysa wp-config.php'deki \`\$table_prefix\` degerini \`wp_\` yapin.
6. wp-config.php: \`wp-config-ek.php\` icerigini "That's all, stop editing!" satirindan once ekleyin.
7. .htaccess: \`htaccess.txt\` icerigini klasordeki .htaccess'e yazin (eskisini degistirin).
8. SSL: cPanel -> SSL/TLS Status -> \`$DOMAIN\` icin AutoSSL calistirin.
9. Giris: $SCHEME://$DOMAIN/wp-login.php (kullanici ve parola: YONETICI-PAROLASI.txt).
   Ilk giriste parolayi degistirin, sonra o dosyayi silin.
10. Kontrol: $SCHEME://$DOMAIN/wp-admin/network/ -> Siteler (8 site), her sitenin ana sayfasi,
    SEO ve GEO sekmesi (puanlar), Yonlendirmeler sayfasi.

Sonra (site site, deneme bitince): alan adini siteye bagla (Ag Yonetimi -> Siteler -> Duzenle ->
Site Adresi), cPanel'de alan adini ayni klasore yonlendir, SSL, DNS, yonlendirmeleri eski
adreslerle test et, en son o sitenin "arama motorlarina acik" ayarini ac.
MD

if [ -n "${MAP:-}" ]; then
	{
		echo ""
		echo "## Canliya gecis (alan adina bagli siteler)"
		echo ""
		echo "Bu pakette su siteler kendi alan adina bagli ve arama motorlarina ACIK:"
		echo ""
		for pair in $MAP; do echo "- /${pair%%=*}/ -> https://${pair#*=}"; done
		echo ""
		echo "Veritabani ice aktarildiginda bu alan adlari HENUZ eski siteyi gosterir. Gecis,"
		echo "her alan adinin belge kokunun (document root) \`/home/u7198936/panel.sanayipalet.com\`"
		echo "yapilmasiyla olur (cPanel -> Etki Alanlari -> Yonet; bu hesapta kapaliysa Natro destek)."
		echo "Once yedek: eski site klasorunun eski yolunu not alin; geri donus = eski yolu geri yazmak."
		echo ""
		echo "Gecisten sonra kontrol: her sayfa 200, eski adresler 301, sertifika gecerli,"
		echo "sayfa kaynaginda noindex YOK, /llms.txt ve /wp-sitemap.xml acik."
	} >> "$OUT/KURULUM.md"
fi

echo ""
echo "Paket hazir: $OUT"
ls -la "$OUT" | sed -n '2,20p'
