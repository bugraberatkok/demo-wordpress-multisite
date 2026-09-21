#!/bin/sh
# WordPress cekirdegini container imajindaki surume gunceller.
#
# Resmi wordpress imaji, dolu bir kuruluma dokunmaz; yalnizca eksik dosyalari
# tamamlar. Imaj etiketi yukseltildiginde cekirdegi bu betikle esitliyoruz.
# Indirme yapmaz: dosyalar imajin icindeki /usr/src/wordpress'ten kopyalanir.
#
#   docker compose exec wordpress sh /scripts/update-core.sh
#   docker compose --profile cli run --rm --entrypoint sh wpcli \
#     -c "wp --path=/var/www/html core update-db --network"

set -eu

SRC=/usr/src/wordpress
DEST=/var/www/html

if [ ! -f "$SRC/wp-includes/version.php" ]; then
	echo "HATA: imajda kaynak WordPress bulunamadi ($SRC)." >&2
	exit 1
fi

image_version=$(grep "wp_version =" "$SRC/wp-includes/version.php" | cut -d"'" -f2)
current_version=$(grep "wp_version =" "$DEST/wp-includes/version.php" | cut -d"'" -f2)

echo "Imajdaki surum : $image_version"
echo "Kurulu surum   : $current_version"

if [ "$image_version" = "$current_version" ]; then
	echo "Cekirdek zaten guncel."
	exit 0
fi

echo "==> cekirdek dosyalari kopyalaniyor (wp-content ve wp-config.php korunur)"

# wp-content disindaki her seyi kopyala.
for item in "$SRC"/*; do
	name=$(basename "$item")

	if [ "$name" = "wp-content" ]; then
		continue
	fi

	cp -R "$item" "$DEST/"
done

# Varsayilan tema/eklentiler eksikse tamamla (mevcutlarin uzerine yazma).
if [ -d "$SRC/wp-content" ]; then
	cp -Rn "$SRC/wp-content/." "$DEST/wp-content/" 2>/dev/null || true
fi

chown -R www-data:www-data "$DEST" 2>/dev/null || true

echo "==> tamam. Simdi veritabani yukseltmesini calistirin:"
echo "    docker compose --profile cli run --rm wpcli --path=/var/www/html core update-db --network"
