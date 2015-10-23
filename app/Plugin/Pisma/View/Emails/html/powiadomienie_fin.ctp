<?

$html='Dzień dobry,
<br><br>
Właśnie minął czas uzyskania odpowiedzi na Twój wniosek o udostępnienie informacji
<br><br>
publicznej. Urząd, do którego wysłano wniosek ma 14 dni na odpowiedzenie. Czy
<br><br>
pojawiła się odpowiedź na wysłany wniosek? Jeśli nie, przysługuje Ci prawo złożenia
<br><br>
skargi na bezczynność organu.
<br><br>
Zajrzyj, do zakładki Pisma na stronie www.mojepanstwo.pl i złóż skargę na bezczynność
<br><br>
organu.
<br><br><br><br>
Pozdrawiamy,
<br><br>
Zespół mojepanstwo.pl';

$emogrifier = new \Pelago\Emogrifier();

$css = 'p {margin: 0; padding: 0;} #docContent p {padding: 5px; margin: 5px;} #docTitle {margin-top: 20px;}';

$emogrifier->setHtml($html);
$emogrifier->setCss($css);

$mergedHtml = $emogrifier->emogrify();
echo $mergedHtml;