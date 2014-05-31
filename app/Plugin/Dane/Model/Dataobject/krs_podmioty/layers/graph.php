<?	
	
	require(APP . '/Vendor/autoload.php');
	use Everyman\Neo4j\Relationship,
	Everyman\Neo4j\Traversal;
	
	if(
		( $client = new Everyman\Neo4j\Client('neo.epf.p2.tiktalik.io', 7474) ) &&
		( $neo_id = $this->DB->selectValue("SELECT neo_id FROM krs_pozycje WHERE id='$id'") ) &&
		( $node = $client->getNode($neo_id) ) && 
		( $traversal = new Everyman\Neo4j\Traversal($client) )
	)
	{
		
		$depth = isset( $_REQUEST['depth'] ) ? (int) $_REQUEST['depth'] : 3;
		$depth = min($depth, 5);
		$depth = max($depth, 1);
		
		/*
		echo "<br/>";
		echo $neo_id;
		echo "<br/>";
		var_export( $node );
		*/
		
		$traversal->setPruneEvaluator(Traversal::PruneNone)
		    ->setReturnFilter(Traversal::ReturnAll)
		    ->setMaxDepth( $depth );
		
		
		$output = array(
			'nodes' => array(),
			'relationships' => array(),
		);
		
		$nodes = $traversal->getResults($node, Traversal::ReturnTypeNode);
		$relationships = $traversal->getResults($node, Traversal::ReturnTypeRelationship);
		
		foreach( $nodes as $node )
		{
			$labels = $node->getLabels();
			$label = array_shift($labels);
			
			$output['nodes'][] = array(
				'id' => $node->getId(),
				'data' => $node->getProperties(),
				'label' => $label->getName(),
			);
		}
		
		foreach( $relationships as $relationship )
		{
			
			// echo "\nrelation_id= " . $relationship->getId();
			
			$output['relationships'][] = array(
				'id' => $relationship->getId(),
				'type' => $relationship->getType(),
				'data' => $relationship->getProperties(),
				'start' => $relationship->getStartNode()->getId(),
				'end' => $relationship->getEndNode()->getId(),
			);
		}
				
		return $output;
		
	
	} else return false;