<?
	
	$html = $this->element('Pisma.pismo', array(
		'pismo' => $pismo,
	));
	
	$css = '';
	
	$path = App::path('Vendor');
	require( $path[0] . 'autoload.php' );
		
	
	
	$emogrifier = new \Pelago\Emogrifier();

	$css = 'p {margin: 0; padding: 0;} #docContent p {padding: 5px; margin: 5px;} #docTitle {margin-top: 20px;}';
	
	$emogrifier->setHtml($html);
	$emogrifier->setCss($css);
	
	$mergedHtml = $emogrifier->emogrify();	
	echo $mergedHtml;