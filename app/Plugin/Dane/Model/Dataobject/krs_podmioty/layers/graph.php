<?	
	
	require(APP . '/Vendor/autoload.php');
	use Everyman\Neo4j\Relationship,
	Everyman\Neo4j\Traversal;
	
	if(
		( $client = new Everyman\Neo4j\Client('neo.epf.p2.tiktalik.com', 7474) ) &&
		( $neo_id = $this->DB->selectValue("SELECT neo_id FROM krs_pozycje WHERE id='$id'") ) &&
		( $node = $client->getNode($neo_id) ) && 
		( $traversal = new Everyman\Neo4j\Traversal($client) )
	)
	{
		
		/*
		echo "<br/>";
		echo $neo_id;
		echo "<br/>";
		var_export( $node );
		*/
		
		$traversal->addRelationship('*', Relationship::DirectionOut)
		    ->setPruneEvaluator(Traversal::PruneNone)
		    ->setReturnFilter(Traversal::ReturnAll)
		    ->setMaxDepth(4);
		
		return $traversal->getResults($node, Traversal::ReturnTypeNode);
		
	
	} else return false;