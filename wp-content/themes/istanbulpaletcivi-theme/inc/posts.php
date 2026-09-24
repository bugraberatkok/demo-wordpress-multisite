<?php
/**
 * Eski sitenin 4 blog yazisi: ayni baslik, ayni adres (slug), ayni tarih.
 *
 * Kurulum (inc/setup.php) yazilari yalnizca yoksa olusturur; sonradan
 * panelden yapilan duzenlemelerin ustune yazmaz.
 *
 * Metinler eski sitedeki yazilardan. Teknik olarak yanlis anlatimlar
 * duzeltildi: "dokme civi" dizisiz (toplu satilan) cividir, eritilip kaliba
 * dokulen civi degil; "tele dizili civi" serit tabancasiyla cakilan tel
 * dizili cividir, betonarmeyi depreme karsi guclendiren bir malzeme degil.
 * Kalan anlatim ve konu sirasi korundu. Gorseller tema klasorundedir.
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array<int, array{slug:string, title:string, date:string, image:string, excerpt:string, content:string}>
 */
function pc_seed_posts(): array {
	$p = static fn( string ...$paragraphs ): string => implode(
		"\n\n",
		array_map(
			static fn( string $text ): string => str_starts_with( $text, '## ' )
				? "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">" . esc_html( substr( $text, 3 ) ) . "</h2>\n<!-- /wp:heading -->"
				: "<!-- wp:paragraph -->\n<p>" . esc_html( $text ) . "</p>\n<!-- /wp:paragraph -->",
			$paragraphs
		)
	);

	return array(
		array(
			'slug'    => 'palet-civisi-insaat-ve-ahsap-islerinde-vazgecilmez-bir-malzeme',
			'title'   => 'Palet Çivisi: İnşaat ve Ahşap İşlerinde Vazgeçilmez Bir Malzeme',
			'date'    => '2023-07-13 10:00:00',
			'image'   => 'hero.jpg',
			'excerpt' => 'Palet çivisinin ne olduğu, nasıl kullanıldığı ve avantajları.',
			'content' => $p(
				'Palet çivisi, inşaat ve ahşap işlerinde kullanılan bir çivi türüdür. Çiviler birleştirme ve sabitleme işlerinin temel malzemesidir ve palet çivisi bu alanda önemli bir yer tutar. Bu yazıda palet çivisinin ne olduğunu, nasıl kullanıldığını ve avantajlarını ele alıyoruz.',
				'Palet çivisi, adından da anlaşılacağı gibi, en çok paletlerin bir arada tutulması için kullanılır. Paletler yük taşıma ve depolama işlerinde kullanılan platformlardır; taşıma sırasında ürünlerin yerinden oynamaması için tahtaları çivilerle birbirine tutturulur. Palet çivileri paletlerin yanı sıra sandık, kafes, ahşap zemin, çit ve mobilya gibi pek çok ahşap yapıda da kullanılır.',
				'Palet çivileri farklı boy ve kalınlıklarda bulunur. Çelik telden üretilir; çıplak (parlak) ya da galvaniz kaplamalı olabilir. Galvaniz kaplama çiviyi paslanmaya karşı korur, nemli ortamda ve dış mekânda tercih edilir. Gövde düz, halkalı ya da spiral (vida dişli) olabilir; halkalı ve spiral gövde ahşapta daha iyi tutunur, çivinin çıkmasını ve yerinden oynamasını zorlaştırır.',
				'Palet çivisi üç biçimde satılır: tellerle rulo hâline getirilmiş rulo çivi, şerit hâlinde dizilmiş tele dizili çivi ve dizisiz dökme çivi. Rulo ve tele dizili çiviler çivi tabancasıyla, dökme çiviler çekiçle ya da dökme çivi kullanan makinelerle çakılır.',
				'Palet çivilerinin birkaç avantajı vardır. İlk olarak kolay kullanılırlar; tabancayla ya da çekiçle ahşaba kolayca çakılırlar. İkinci olarak sağlam bir bağlantı sağlarlar. Üçüncü olarak ekonomiktirler; uygun fiyatlıdırlar ve pek çok projede yaygın olarak kullanılırlar.',
				'Sonuç olarak palet çivisi, paletlerin birleştirilmesinden ahşap yapıların inşasına kadar pek çok işte kullanılan önemli bir malzemedir. Dayanıklı, kolay kullanılan ve ekonomik olması onu tercih edilen bir seçenek hâline getirir.'
			),
		),
		array(
			'slug'    => 'rulo-civiler-hakkinda-bilmeniz-gerekenler',
			'title'   => 'Rulo Çiviler Hakkında Bilmeniz Gerekenler',
			'date'    => '2023-07-13 11:00:00',
			'image'   => 'rulo.jpg',
			'excerpt' => 'Rulo çivilerin özellikleri, kullanım alanları ve avantajları.',
			'content' => $p(
				'Bu yazıda inşaat ve marangozluk işlerinde sıkça kullanılan rulo çivileri anlatıyoruz. Rulo çiviler, çeşitli yapısal amaçlar için tercih edilen pratik ve kullanışlı bir çivi türüdür. Özelliklerinden, kullanım alanlarından ve avantajlarından bahsedeceğiz.',
				'Rulo çivilerde çiviler yan yana dizilir, ince tellerle birbirine bağlanır ve bobin (rulo) şeklinde sarılır. Bu şekilde sunulmaları çivi çakma işini hızlandırır ve verimliliği artırır.',
				'Rulo çiviler rulo çivi tabancalarıyla kullanılmak üzere tasarlanmıştır. Bu nedenle palet ve sandık üretimi gibi seri işlerde, büyük inşaat projelerinde ve hızlı montaj gerektiren işlerde tercih edilirler.',
				'Bu tür çiviler çelik telden üretilir ve çeşitli boy, kalınlık ve gövde tiplerinde bulunur. Böylece farklı projelerde ve malzemelerde kullanılmak üzere çeşitli seçenekler sunarlar.',
				'Rulo çivilerin kullanım alanları oldukça geniştir. Palet ve sandık üretimi, ahşap işleri, kaplama montajı, döşeme, çit yapımı, çatı kaplama ve inşaat projeleri gibi birçok alanda kullanılırlar.',
				'En büyük avantajları hızlı ve kolay uygulanmalarıdır. Tabancayla çivileri tek tek yerleştirmeden, birkaç saniyede birkaç çivi çakabilirsiniz. Bu zaman kazandırır ve işçilik maliyetini düşürür.',
				'Sonuç olarak rulo çiviler, hızlı ve etkili çivi çakmayı sağlayan kullanışlı bir malzemedir. İnşaat, marangozluk ve ambalaj başta olmak üzere pek çok sektörde yaygın olarak kullanılır.'
			),
		),
		array(
			'slug'    => 'tele-dizili-civiler-insaat-projelerindeki-onemleri-ve-kullanimlar',
			'title'   => 'Tele Dizili Çiviler: İnşaat Projelerindeki Önemleri ve Kullanımlar',
			'date'    => '2023-07-13 12:00:00',
			'image'   => 'tele.jpg',
			'excerpt' => 'Tele dizili çivilerin ne olduğu, nasıl kullanıldığı ve hangi işlerde tercih edildiği.',
			'content' => $p(
				'Bu yazıda inşaat ve ahşap işlerinde sıkça kullanılan tele dizili çivileri anlatıyoruz: ne olduklarını, nasıl kullanıldıklarını ve hangi işlerde tercih edildiklerini.',
				'## Tele dizili çivi nedir?',
				'Tele dizili çivilerde çiviler düz bir şerit boyunca yan yana dizilir ve ince tellerle birbirine bağlanır. Şerit, şerit çivi tabancasının şarjörüne yerleştirilir; tabanca her tetikte bir çiviyi teli koparak çakar. Şeritler genellikle açılıdır ve açının tabancaya uygun olması gerekir.',
				'## Nerede kullanılır?',
				'Uzun gövdeli çivilerde yaygındır. Ahşap karkas ve çatı iskeleti, kalıp işleri, ahşap zemin döşeme, kasa yapımı ve palet üretimi gibi kalın ahşabın birleştirildiği işlerde kullanılır.',
				'## Avantajları',
				'Çivilerin düzenli dizilmesi, her çakışta aynı derinlikte ve sağlam bir bağlantı sağlar. Uzun ve kalın çivilerde tabancayı dengeli tutar. Elle çakıma göre çok daha hızlıdır; büyük projelerde işçilik süresini kısaltır.',
				'Rulo çiviye göre şarjörde daha az çivi taşır; bu yüzden seri palet üretiminde çoğunlukla rulo çivi, karkas ve kalıp gibi işlerde tele dizili çivi tercih edilir. Hangisini kullanacağınız elinizdeki tabancaya bağlıdır.',
				'## Güvenlik',
				'Çivi tabancasıyla çalışırken koruyucu gözlük ve eldiven kullanın, tabancayı kullanılmadığında kilitli tutun. Doğru ölçüde çivi, hem bağlantının sağlamlığı hem de tabancanın ömrü için önemlidir.'
			),
		),
		array(
			'slug'    => 'dokme-civiler-hakkinda',
			'title'   => 'Dökme Çiviler Hakkında',
			'date'    => '2023-07-13 13:00:00',
			'image'   => 'dokme.jpg',
			'excerpt' => 'Dökme çivinin ne olduğu, nerede kullanıldığı ve nasıl seçileceği.',
			'content' => $p(
				'Bu yazıda dökme çivileri anlatıyoruz. Dökme çivi, herhangi bir tel ya da bantla dizilmemiş, toplu hâlde satılan çividir. Adını döküm yönteminden değil, "dökme" (toplu, ambalajsız) satılmasından alır.',
				'## Dökme çivi nedir?',
				'Dökme çiviler çelik telden, başı ve ucu şekillendirilerek üretilir. Rulo ya da tele dizili çivilerden farkı, dizme işleminden geçmemesidir. Bu yüzden kilo ya da koli ile satılır.',
				'## Nerede kullanılır?',
				'Dökme çiviler çekiçle elle çakılır ya da dökme çivi besleyen çakım makinelerinde kullanılır. Tabanca kullanılmayan işlerde, palet onarımında, sandık kapağı ve saha işlerinde, küçük adetli üretimde pratiktir. Ahşap ve metal parçaları bir arada tutmak için mobilya ve marangozluk işlerinde de kullanılır.',
				'## Nasıl seçilir?',
				'Çivinin boyu, birleştirilecek tahtaların toplam kalınlığına göre seçilir. Gövde düz ya da halkalı olabilir; halkalı gövde ahşapta daha iyi tutunur. Nemli ortamda ve dış mekânda galvaniz kaplamalı çivi tercih edilir.',
				'## Güvenlik',
				'Çivilerin sivri uçları nedeniyle yaralanma riski vardır. Çakarken eldiven ve koruyucu gözlük kullanın; büyük işlerde işi deneyimli bir ustaya bırakmak daha iyidir.'
			),
		),
	);
}
