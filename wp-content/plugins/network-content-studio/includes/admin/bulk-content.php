<?php
/**
 * Toplu Guncelleme icerik duzeltmeleri: kardes siteler arasindaki kopya metin.
 *
 * Olculdu (24-25 Eylul 2026): ahsapambalajsanayi.com ile ahsapkasa.com'un
 * Hakkimizda, Hizmetlerimiz ve Iletisim sayfalari %100 ayniydi; ithalkeresteci
 * ile kavakkeresteci'nin Sik Sorulan Sorular sayfasi %51. Ahsap Kasa ozgun
 * metni korur; Ahsap Ambalaj kendi odagina (sanayi ve ihracat yukleri), kereste
 * siteleri kendi urunlerine gore yazildi. Yeni firma bilgisi yok.
 *
 * Esleme tam metindir: alanin (ya da tekrarli satir hucresinin) degeri eski
 * metnin birebir aynisiysa yenisi yazilir. Panelden elle degistirilmis alan
 * eslesmez, dokunulmaz. Tema varsayilanlari da ayni metne cekildi.
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array<string, array<string, string>> tema => [ eski => yeni ]
 */
function nwcs_bulk_revisions(): array {
	return array(
		'ahsapambalaj-theme' => array(
			'Elli yıllık tecrübeyle, istediğiniz ölçü ve ebatta ahşap sandık, kafes ve palet üretiyoruz. Kaliteli, dürüst ve hızlı.' =>
				'Sanayi ve ihracat yükleri için ölçüye göre ahşap sandık, kafes ve palet. Arkasında Koçist Orman Ürünleri’nin 50 yıllık tecrübesi var.',
			'Nasıl çalışıyoruz' =>
				'Bir ambalaj işi nasıl ilerler',
			'Ölçüden sevkiyata kadar izlenen yol.' =>
				'Yükün bilgilerinden sevkiyata.',
			'Yeni bir ürün için ya da kullandığınız ambalajın yerine; her ölçüde kereste, palet, sandık ve kafesi ihtiyacınıza göre tasarlar, size sunarız.' =>
				'Önce yükü tanırız: ürünün ölçüsü, ağırlığı ve gideceği yer. Makine, kalıp ya da yedek parça için sandığı, kafesi veya paleti bu bilgilere göre tasarlarız.',
			'Ölçülerinizi, spesifikasyonları veya proje çizimini alır, en hızlı şekilde üretime geçeriz. Tüm parçaları içeren demonte paketler hâlinde sevk ederiz.' =>
				'Taşıyıcı kızakları, iç sabitlemeyi ve kapak yapısını planladıktan sonra üretime geçeriz. Büyük ambalajları bütün parçalarıyla demonte paketler hâlinde gönderebiliriz.',
			'Amacımız' =>
				'Neyi önemsiyoruz',
			'En kaliteli malzemeyi en kısa zamanda müşterimizin hizmetine sunmaktır. Bu bağlamda firmamızın çalışmaları daha kaliteli ve daha hesaplı malzemeyi tüketicinin hizmetine sunma ilkesiyle devam etmektedir.' =>
				'Yükün yolda zarar görmeden yerine ulaşması. Bunun için doğru malzemeyi zamanında ve makul fiyatla sunmayı esas alıyoruz.',
			'Kalite Politikamız' =>
				'Kalite politikamız',
			'Ürünlerimizi zamanında ve uygun fiyatla teslim ederek müşteri ihtiyaç ve beklentilerini karşılamak; işi ilk seferinde ve her seferinde doğru yapmak; çalışanlarımızın sağlığını ve çevreyi gözetmek; tedarikçilerimizle birlikte sürekli gelişmek.' =>
				'Teslim tarihine uymak, işi ilk seferinde doğru yapmak ve müşterinin isteğini esas almak. Bunu yaparken çalışanlarımızın sağlığını ve çevreyi gözetiyor, tedarikçilerimizle birlikte gelişmeye çalışıyoruz.',
			'Dört ürün grubunun hiçbirinde standart ölçü yok. Her iş, sizin verdiğiniz ölçüye göre üretilir.' =>
				'Her ambalaj taşıyacağı yüke göre tasarlanır. Ürünün ölçüsünü ve ağırlığını verin, gerisini birlikte planlayalım.',
			'Standart dışı, istediğiniz ölçü ve ebatlarda ahşap palet üretimi yapmaktayız. Kardonlu palet ihtiyaçlarınızda da bizimle irtibat kurabilirsiniz.' =>
				'Ağır ve standart dışı yükler için ölçüye göre palet. Kardonlu palet seçeneği de var.',
			'Standart dışı, istediğiniz ölçü ve ebatlarda ahşap sandık üretimi yapmaktayız. Fiyatlarımız ve cazip tekliflerimiz için lütfen bizimle irtibat kurunuz.' =>
				'Makineyi, kalıbı ya da yedek parçayı dört yandan koruyan kapalı sandık. İhracat sevkiyatına göre hazırlanır, demonte gönderilebilir.',
			'Standart dışı, istediğiniz ölçü ve ebatlarda ahşap kafes üretimi yapmaktayız. Size özel teklif ve cazip fiyat seçeneklerimiz için lütfen bizimle irtibat kurunuz.' =>
				'Açık iskeletli kafes, yükün ölçüsüne göre kurulur.',
			'İhracat şartnamenize uygun, ölçüye göre ahşap ambalaj çözümleri üretiyoruz. Parçaları içeren demonte paketler hâlinde sevk ediyoruz.' =>
				'İhracat şartnamenize göre, yükün ölçüsüne ve ağırlığına uygun paketleme.',
			'Ürün grubumuz bunlarla sınırlı değil.' =>
				'Yükünüz bu dört gruba uymuyor mu?',
			'Özel boy ve ebatta kereste ile aradığınız başka bir ahşap ambalaj çözümü varsa yazın; üretilebilirliğini birlikte değerlendirelim.' =>
				'Proje sevkiyatı ya da alışılmadık bir yük için ölçüleri ve bir fotoğraf gönderin; nasıl ambalajlanacağını birlikte değerlendirelim.',
			'Ürünün ölçülerini ve tahmini adedi yazın, teklifinizi hazırlayalım.' =>
				'Yükün ölçüsünü, ağırlığını ve gideceği yeri yazın; ambalaj önerimizi ve teklifimizi hazırlayalım.',
			'Müşteri hizmetleri' =>
				'Telefon',
			'Merkez ofis ve atölye' =>
				'Adres',
			'Teklif isteyin' =>
				'Ambalaj teklifi isteyin',
			'Ölçü alanına en, boy ve yükseklik yazmanız yeterli. Çiziminiz varsa e-posta ile de gönderebilirsiniz.' =>
				'Yükün ölçüsü (en × boy × yükseklik), ağırlığı ve gideceği yer yeterli. Çiziminiz ya da fotoğrafınız varsa e-postayla gönderin.',
			'Ölçüler ve adet' =>
				'Yükün ölçüsü, ağırlığı ve adedi',
			'Örn. 120 × 80 × 100 cm, yaklaşık 50 adet' =>
				'Örn. 220 × 140 × 160 cm, 1.800 kg, 3 sandık',
			'Eklemek istedikleriniz' =>
				'Varış yeri ve eklemek istedikleriniz',
			'En kısa sürede dönüş yapacağız. Acele bir işse 0 212 648 10 90 numaralı telefondan da ulaşabilirsiniz.' =>
				'Yükün bilgilerini aldık; ambalaj önerimizle size döneceğiz. Acele bir iş için 0 212 648 10 90’ı arayabilirsiniz.',
			'Ürün grubumuz' =>
				'Ne üretiyoruz',
			'Bunlarla sınırlı değil elbette.' =>
				'Her biri yüke göre ölçülendirilir.',
			'Standart dışı ölçülerde, kardonlu seçenekle' =>
				'Ağır ve standart dışı yükler için',
			'İhracata uygun, demonte sevk edilebilir' =>
				'Makine ve kalıp için kapalı koruma',
			'Ürününüzün ölçüsüne göre kafes iskeleti' =>
				'Açık iskelet, yükün ölçüsünde',
			'Şartnamenize uygun paketleme çözümleri' =>
				'İhracat şartnamesine göre paketleme',
			'Ölçülerinizi gönderin, teklifinizi hazırlayalım.' =>
				'Yükün ölçüsünü ve ağırlığını gönderin.',
			'Ürünün eni, boyu, yüksekliği ve tahmini adedi yeterli. Çiziminiz varsa daha da hızlı ilerleriz.' =>
				'Eni, boyu, yüksekliği, ağırlığı ve gideceği yer yeterli; ambalaj önerimizle birlikte teklifimizi hazırlayalım.',
		),
		'ithalkeresteci-theme' => array(
			'Sipariş, sevkiyat süresi, ödeme seçenekleri, ölçüye göre kesim ve kereste metreküp hesabıyla ilgili en çok sorulanlar.' =>
				'İthal kereste, kalas ve plywood siparişinde en çok sorulanlar: sevkiyat süresi, ödeme, ölçüye kesim ve metreküp hesabı.',
			'Sevkiyat süresi nedir?' =>
				'Sipariş verdikten sonra kereste ne zaman gelir?',
			'Normal koşullarda 3–5 iş günü içinde sevkiyat yapılır.' =>
				'İthal kereste, kalas ve plywood için sevkiyat normal koşullarda 3–5 iş günü içinde yapılır.',
			'Kereste metreküpü nasıl hesaplanır?' =>
				'Kalasın metreküpü nasıl hesaplanır?',
			'Kalınlık ve genişlik santimetreden metreye çevrilir, boy ve adetle çarpılır. Örneğin 5 × 10 cm kesitli, 4 m boyunda 100 adet kereste 0,05 × 0,10 × 4 × 100 = 2 m³ eder.' =>
				'Kalınlığı ve genişliği santimetreden metreye çevirin, boy ve adetle çarpın. Örneğin 5 × 20 cm kesitli, 4 m boyunda 50 adet kalas: 0,05 × 0,20 × 4 × 50 = 2 m³.',
			'İstediğim ölçüde kesim yapılıyor mu?' =>
				'Kalas ve kereste istediğim ölçüde kesiliyor mu?',
			'Evet. Projenize uygun ebatlarda kesim ve hazırlık yapılabilir; istenen ölçü birebir kesilir.' =>
				'Evet. İnşaat ya da çatı projenize göre boy ve kesit birebir kesilir; ölçü listenizi göndermeniz yeterli.',
			'Ölçü ve adedi yazın ya da arayın; size en kısa sürede dönelim.' =>
				'İthal kereste, kalas ya da plywood için ölçüyü ve adedi yazın ya da arayın; fiyatla dönelim.',
			'Ürün, ölçü ve adet yeterli; fiyatla dönelim.' =>
				'Ürün, kesit, boy ve adet yeterli.',
			'Örn. 5 × 10 cm, 4 m, 100 adet' =>
				'Örn. kalas 5 × 20 cm, 4 m, 50 adet',
		),
		'kavakkeresteci-theme' => array(
			'Sipariş, sevkiyat süresi, ödeme seçenekleri, ölçüye göre kesim ve kereste metreküp hesabıyla ilgili en çok sorulanlar.' =>
				'Kavak kereste, çıta ve takozla ilgili sorular: nerede kullanılır, ölçüye kesim, sevkiyat ve metreküp hesabı.',
			'Sevkiyat süresi nedir?' =>
				'Kavak kereste, çıta ve takoz ne kadar sürede gönderilir?',
			'Normal koşullarda 3–5 iş günü içinde sevkiyat yapılır.' =>
				'Normal koşullarda sipariş 3–5 iş günü içinde sevk edilir.',
			'İstediğim ölçüde kesim yapılıyor mu?' =>
				'Çıta ve takoz özel ölçüde hazırlanıyor mu?',
			'Evet. Kereste, çıta ve takoz projenize uygun ölçüde kesilip hazırlanabilir.' =>
				'Evet. Kavak kereste, çıta ve takoz verdiğiniz ölçüye göre kesilip hazırlanır.',
			'Kereste metreküpü nasıl hesaplanır?' =>
				'Çıtanın metreküpü nasıl hesaplanır?',
			'Kalınlık ve genişlik santimetreden metreye çevrilir, boy ve adetle çarpılır. Örneğin 5 × 10 cm kesitli, 4 m boyunda 100 adet kereste 0,05 × 0,10 × 4 × 100 = 2 m³ eder.' =>
				'Kesit ölçülerini metreye çevirip boy ve adetle çarpın. Örneğin 3 × 5 cm kesitli, 3 m boyunda 200 adet çıta: 0,03 × 0,05 × 3 × 200 = 0,9 m³.',
			'Ölçü ve adedi yazın ya da arayın; size en kısa sürede dönelim.' =>
				'Kavak kereste, çıta ya da takoz için ölçüyü ve adedi yazın ya da arayın; size en kısa sürede dönelim.',
			'Ürün, ölçü ve adet yeterli; fiyatla dönelim.' =>
				'Hangi ürün, hangi ölçü ve kaç adet: bu kadarı yeterli.',
			'Örn. 5 × 10 cm, 4 m, 100 adet' =>
				'Örn. çıta 3 × 5 cm, 3 m, 200 adet',
		),
	);
}
