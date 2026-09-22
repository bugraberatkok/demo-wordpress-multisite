<?php
/**
 * Test icin Excel'in urettigine benzer bir .xlsx olusturur.
 * Kapsadigi durumlar: paylasilan metin, bicimli metin (<r><t>), sayi hucresi,
 * atlanan hucre (bos sutun), satir sonunda eksik hucre.
 *
 * Calistirma: php /scripts/dev/make-test-xlsx.php /tmp/test.xlsx
 */

$target = $argv[1] ?? '/tmp/test.xlsx';

$shared = array( 'Ürün Adı', 'Stok Kodu', 'Fiyat', 'Kategori', 'Açıklama', 'Ahşap Palet 120x80', 'PAL-120', 'Palet|İhracat', 'Standart dışı ölçülerde', 'Ahşap Sandık', 'SND-01', 'Sandık' );

$si = '';
foreach ( $shared as $i => $text ) {
	// Birini bicimli metin olarak yaz; Excel bunu boyle uretir.
	$si .= 4 === $i
		? '<si><r><t>Açık</t></r><r><t>lama</t></r></si>'
		: '<si><t>' . htmlspecialchars( $text, ENT_XML1 ) . '</t></si>';
}

$sharedXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
	. '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count( $shared ) . '" uniqueCount="' . count( $shared ) . '">'
	. $si . '</sst>';

// A1..E1 baslik, sonra iki veri satiri. Ikinci satirda D sutunu atlanmis.
$sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
	. '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>'
	. '<row r="1"><c r="A1" t="s"><v>0</v></c><c r="B1" t="s"><v>1</v></c><c r="C1" t="s"><v>2</v></c><c r="D1" t="s"><v>3</v></c><c r="E1" t="s"><v>4</v></c></row>'
	. '<row r="2"><c r="A2" t="s"><v>5</v></c><c r="B2" t="s"><v>6</v></c><c r="C2"><v>1250.5</v></c><c r="D2" t="s"><v>7</v></c><c r="E2" t="s"><v>8</v></c></row>'
	. '<row r="3"><c r="A3" t="s"><v>9</v></c><c r="B3" t="s"><v>10</v></c><c r="C3"><v>890</v></c><c r="E3" t="s"><v>11</v></c></row>'
	. '<row r="4"/>'
	. '</sheetData></worksheet>';

$workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
	. '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
	. '<sheets><sheet name="Ürünler" sheetId="1" r:id="rId1"/></sheets></workbook>';

$rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
	. '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
	. '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
	. '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>'
	. '</Relationships>';

$contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
	. '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
	. '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
	. '<Default Extension="xml" ContentType="application/xml"/>'
	. '</Types>';

$rootRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
	. '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
	. '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
	. '</Relationships>';

@unlink( $target );
$zip = new ZipArchive();
$zip->open( $target, ZipArchive::CREATE );
$zip->addFromString( '[Content_Types].xml', $contentTypes );
$zip->addFromString( '_rels/.rels', $rootRels );
$zip->addFromString( 'xl/workbook.xml', $workbook );
$zip->addFromString( 'xl/_rels/workbook.xml.rels', $rels );
$zip->addFromString( 'xl/sharedStrings.xml', $sharedXml );
$zip->addFromString( 'xl/worksheets/sheet1.xml', $sheetXml );
$zip->close();

echo "olusturuldu: $target\n";
