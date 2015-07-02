<?

App::uses('MPSearch', 'Model/Datasource');
App::uses('Dataobject', 'Plugin/Dane/Model');

interface ElasticSearchClientMock {
    public function search($params);
}

/**
 * Assuming given ElasticSearch output test converting data to Cake-way
 */
class MPSearchTest extends CakeTestCase {
    public function setUp() {
        // new MPSearch without API
        $this->Datasource = $this->getMockBuilder('MPSearch')->setMethods(null)->disableOriginalConstructor()->getMock();

        // mock API
        $this->Datasource->API = $this->getMock('ElasticSearchClientMock');

        // 10 wojewodztw jako dane testowe
        $this->queryData = array(
            'conditions' =>
                array(
                    'dataset' => 'wojewodztwa',
                ),
            'fields' => NULL,
            'joins' =>
                array(),
            'limit' => '10',
            'offset' => NULL,
            'order' =>
                array(
                    0 => NULL,
                ),
            'page' => 1,
            'group' => NULL,
            'callbacks' => true,
        );

        $this->testData = array('took' => 22, 'timed_out' => false,
            '_shards' => array('total' => 12, 'successful' => 12, 'failed' => 0,),
            'hits' => array('total' => 16, 'max_score' => NULL, 'hits' => array(
                0 => array(
                    '_index' => 'mojepanstwo_v1',
                    '_type' => 'objects',
                    '_id' => '861894',
                    '_score' => NULL,
                    'fields' => array('id' => array(0 => '1',), 'source' => array(0 => array(
                        'data' => array('wojewodztwa.nazwa' => 'Dolnośląskie', 'wojewodztwa.id' => '1',),),),
                        'dataset' => array(0 => 'wojewodztwa',), 'slug' => array(0 => 'dolnoslaskie',),), 'sort' => array(0 => -9223372036854775808, 1 => 'dolnośląski',),),
                1 => array('_index' => 'mojepanstwo_v1', '_type' => 'objects', '_id' => '861895', '_score' => NULL, 'fields' => array('id' => array(0 => '2',), 'source' => array(0 => array('data' => array('wojewodztwa.nazwa' => 'Kujawsko-pomorskie', 'wojewodztwa.id' => '2',),),), 'dataset' => array(0 => 'wojewodztwa',), 'slug' => array(0 => 'kujawsko-pomorskie',),), 'sort' => array(0 => -9223372036854775808, 1 => 'kujawsko',),),
                2 => array('_index' => 'mojepanstwo_v1', '_type' => 'objects', '_id' => '861896', '_score' => NULL, 'fields' => array('id' => array(0 => '3',), 'source' => array(0 => array('data' => array('wojewodztwa.nazwa' => 'Lubelskie', 'wojewodztwa.id' => '3',),),), 'dataset' => array(0 => 'wojewodztwa',), 'slug' => array(0 => 'lubelskie',),), 'sort' => array(0 => -9223372036854775808, 1 => 'lubelska',),)
            ,),),);

        $this->defaultResponse = array(
            0 =>
                array(
                    'global_id' => '861894',
                    'dataset' => 'wojewodztwa',
                    'id' => '1',
                    'url' => 'http://api-server.dev/dane/wojewodztwa/1',
                    'mpurl' => 'http://mojepanstwo.pl/dane/wojewodztwa/1',
                    'slug' => 'dolnoslaskie',
                    'score' => NULL,
                    'wojewodztwa.nazwa' => 'Dolnośląskie',
                    'wojewodztwa.id' => '1',
                ),
            1 =>
                array(
                    'global_id' => '861895',
                    'dataset' => 'wojewodztwa',
                    'id' => '2',
                    'url' => 'http://api-server.dev/dane/wojewodztwa/2',
                    'mpurl' => 'http://mojepanstwo.pl/dane/wojewodztwa/2',
                    'slug' => 'kujawsko-pomorskie',
                    'score' => NULL,
                    'wojewodztwa.nazwa' => 'Kujawsko-pomorskie',
                    'wojewodztwa.id' => '2',
                ),
            2 =>
                array(
                    'global_id' => '861896',
                    'dataset' => 'wojewodztwa',
                    'id' => '3',
                    'url' => 'http://api-server.dev/dane/wojewodztwa/3',
                    'mpurl' => 'http://mojepanstwo.pl/dane/wojewodztwa/3',
                    'slug' => 'lubelskie',
                    'score' => NULL,
                    'wojewodztwa.nazwa' => 'Lubelskie',
                    'wojewodztwa.id' => '3',
                ),
        );

        $this->Datasource->API->expects($this->once())->method('search')->will($this->returnValue($this->testData));
    }

    public function testStoringLastResponseStats() {
        $this->Datasource->read(new Dataobject(), $this->queryData);

        $this->assertEquals(16, $this->Datasource->lastResponseStats['count']);
        $this->assertEquals(22, $this->Datasource->lastResponseStats['took_ms']);
    }

    public function testReadBasic() {
        $response = $this->Datasource->read(new Dataobject(), $this->queryData);

        $this->assertEquals($this->defaultResponse, $response);
    }

    public function testReadFieldsOne() {
        $this->queryData['fields'] = 'wojewodztwa.id';

        $response = $this->Datasource->read(new Dataobject(), $this->queryData);

        $this->assertEquals(array(
            array('wojewodztwa.id' => '1'),
            array('wojewodztwa.id' => '2',),
            array('wojewodztwa.id' => '3')),
            $response);
    }

    public function testReadFieldsMany() {
        $this->queryData['fields'] = array('global_id', 'wojewodztwa.id');

        $response = $this->Datasource->read(new Dataobject(), $this->queryData);

        $this->assertEquals(array(
            array('global_id' => '861894', 'wojewodztwa.id' => '1'),
            array('global_id' => '861895', 'wojewodztwa.id' => '2',),
            array('global_id' => '861896', 'wojewodztwa.id' => '3')),
            $response);
    }

//    public function testLimitOverflow() {
//        // TODO
//    }
}