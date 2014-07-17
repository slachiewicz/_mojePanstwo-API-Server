<?	
	
	require_once(APP . '/Vendor/autoload.php');
	use Everyman\Neo4j\Relationship,
	Everyman\Neo4j\Traversal;
	
	if(
		( $client = new Everyman\Neo4j\Client('neo.epf.p3.tiktalik.io', 7474) ) &&
		( $neo_id = $this->DB->selectValue("SELECT neo_id FROM krs_pozycje WHERE id='$id'") ) &&
		( $node = $client->getNode($neo_id) ) && 
		( $traversal = new Everyman\Neo4j\Traversal($client) )
	)
	{
		
		$depth = isset( $_REQUEST['depth'] ) ? (int) $_REQUEST['depth'] : 2;
		$depth = min($depth, 5);
		$depth = max($depth, 1);

//        $queryString = "START n=node({nodeId}) MATCH (n)-[r*1..3]-(nodes) RETURN r, nodes";
//        $query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array('nodeId' => intval($neo_id)));
//        $result = $query->getResultSet();
		
		/*
		echo "<br/>";
		echo $neo_id;
		echo "<br/>";
		var_export( $node );
		*/
		
		$traversal->setPruneEvaluator(Traversal::PruneNone)
		    ->setReturnFilter(Traversal::ReturnAll)
            ->setOrder(Traversal::OrderBreadthFirst)
		    ->setMaxDepth( $depth );
		
		
		$output = array(
			'nodes' => array(),
			'relationships' => array(),
		);

        $traversal->setUniqueness(Traversal::UniquenessNodeGlobal);
		$nodes = $traversal->getResults($node, Traversal::ReturnTypeNode);

        $traversal->setUniqueness(Traversal::UniquenessRelationshipGlobal);
		$relationships = $traversal->getResults($node, Traversal::ReturnTypeRelationship);
		
		foreach( $nodes as $node )
		{
			$labels = $node->getLabels();
			$label = array_shift($labels);
			
			$data = $node->getProperties();
			if( !empty($data) )
				foreach( $data as $key => &$value )
					$value = stripslashes(htmlspecialchars_decode($value));
			
			$output['nodes'][] = array(
				'id' => $node->getId(),
				'data' => $data,
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