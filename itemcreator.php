<?PHP


include_once '/geshi/geshi.php';
$xml = simplexml_load_file('C:\Users\Asus\Desktop\New folder\db\data/addition.xml');

$text = $xml->asXML();
$language = 'XML';
$geshi = new GeSHi($text, $language);
echo $geshi->parse_code();

?>