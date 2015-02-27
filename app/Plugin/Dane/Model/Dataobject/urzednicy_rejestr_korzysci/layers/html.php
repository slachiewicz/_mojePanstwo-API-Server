<?
$html_sha1 = $this->data['data']['urzednicy_rejestr_korzysci.html_sha1'];

if (!$html_sha1) {
	return false;
}

$osoba_id = $this->data['data']['urzednicy_rejestr_korzysci.osoba_id'];
$url = 'resources/PKW/urzednicy/deklaracje/' . $osoba_id . '/' . $html_sha1 . '.html';
$html = $this->S3Files->getBody($url);

if (!$html) {
	throw new Exception("administracja_publiczna_rejestr_korzysci id=$id html_sha1=$html_sha1 is not stored in S3. Tried $url");
}

return $html;