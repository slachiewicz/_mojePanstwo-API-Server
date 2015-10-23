<?

$html='Dzień dobry,
<br><br>
Za 3 dni mija czas uzyskania odpowiedzi na Twój wniosek o udostępnienie informacji
<br><br>
publicznej. Sprawdź swoją skrzynkę mailową i monitoruj na bieżąco sprawę. Urząd, do
<br><br>
którego wysłano wniosek ma 14 dni na udzielenie Ci odpowiedzi.
<br><br>
Przypominamy również, że zgodnie z ustawą  o dostępie do informacji publicznej
<br><br>
realizacja prawa do informacji publicznej w przewidzianych ustawą formach jest
<br><br>
uzależniona od spełnienia 3 warunków:
<br><br>
1. przedmiotem żądania informacji musi być informacja publiczna w rozumieniu art. 1
<br><br>
oraz art. 3 ust. 2 z uwzględnieniem interpretacji rozszerzającej wynikającej z art. 61
<br><br>
ust. 1 Konstytucji RP;
<br><br>
2. adresatem żądania udostępnienia informacji publicznej, na zasadach tej ustawy,
<br><br>
mogą być wyłącznie podmioty zobowiązane zgodnie z art. 4,
<br><br>
3. według art. 4 ust. 3 obowiązane do udostępnienia informacji publicznej są podmioty,
<br><br>
o których mowa w ust. 1 i 2, będące w posiadaniu takich informacji.
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