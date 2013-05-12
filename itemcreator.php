<?PHP


header('Content-type: text/xml');
$xml = simplexml_load_file('C:\Users\Asus\Desktop\New folder\db\data/addition.xml');
echo $xml->asXML();


?>