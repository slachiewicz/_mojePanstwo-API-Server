<?

App::uses('DataobjectsController', 'Plugin/Dane/Controller');
App::uses('Dataobject', 'Dane.Model');

class DataobjectsControllerTest extends ControllerTestCase {
    //public $fixtures = array('plugin.dane.wojewodztwa3/Dataobjects');

    public function setUp() {
        $this->generate('Dane.Dataobjects');
        $this->controller->Dataobject = $this->getMock('Dataobject', array('find', 'getDataSource'));

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
    }

    private function dataobjectExpectsFind($params, $response, $count) {
        $this->controller->Dataobject->expects($this->once())
            ->method('find')->with($this->equalTo('all'), $this->equalTo($params))
            ->will($this->returnValue($response));

        $this->controller->Dataobject->expects($this->any())
            ->method('getDataSource')
            ->will($this->returnValue((object)array(
                'lastResponseStats' => array(
                    'count' => $count
                )
            )));
    }

    public function testIndexSimple() {
        $this->dataobjectExpectsFind(array(
            'conditions' => array(
                'dataset' => 'wojewodztwa'
            )
        ), $this->defaultResponse, 3);

        $this->testAction('/dane/wojewodztwa');

        $this->assertSame(array(
            '_serialize' => array('_items', '_meta'),
            '_items' => $this->defaultResponse,
            '_meta' => array('page' => 1, 'max_results' => MPSearch::RESULTS_COUNT_MAX, 'total' => 3)
        ), $this->vars);
    }

    public function assertArraySameValues($expected, $actual) {
        $this->assertSame(array_diff($expected, $actual), array_diff($actual, $expected));
    }

    public function assertUrlSamePathAndQuery($expected, $actual) {
        $this->assertUrlSame($expected, $actual, array('path', 'query'));
    }

    /**
     * @see http://php.net/manual/en/function.parse-url.php
     * @param $expected
     * @param $actual
     * @param $fields
     */
    public function assertUrlSame($expected, $actual, $fields =
    array('host', 'path', 'query', 'fragment', 'scheme', 'port', 'user', 'pass' )) {
        $expected = parse_url($expected);
        $actual = parse_url($actual);

        foreach($fields as $fld) {
            $this->assertSame($expected[$fld], $actual[$fld]);
        }
    }

    public function assertArrayMatchingKeysSame($expected, $actual) {
        $this->assertSame(array_intersect_key($expected, $actual), array_intersect_key($actual, $expected));
    }

    public function assertArrayMatchingKeysNotSame($expected, $actual) {
        $this->assertNotSame(array_intersect_key($expected, $actual), array_intersect_key($actual, $expected));
    }

    public function testAssertArrayMatchingKeysSame() {
        $this->assertArrayMatchingKeysSame(
            array('k1' => 'v1', 'k2' => 'v2'),
            array('k2' => 'v2', 'k3' => 'v1')
        );

        // no matching keys is an empty-empty match
        $this->assertArrayMatchingKeysSame(
            array('k1' => 'v1', 'k2' => 'v2'),
            array('k4' => 'v2', 'k3' => 'v1')
        );
    }

    public function testAssertArrayMatchingKeysNotEquals() {
        $this->assertArrayMatchingKeysNotSame(
            array('k1' => 'v1', 'k2' => 'v2'),
            array('k2' => 'v3', 'k3' => 'v1')
        );
    }

    public function testAssertUrlEqualsQueryNoOrder() {
        $this->assertUrlSame('?val1=1&val2=2', '?val2=2&val1=1');
    }

    public function testIndexHateoasFirst() {
        $this->dataobjectExpectsFind(array(
            'conditions' => array(
                'dataset' => 'wojewodztwa'
            ),
            'limit' => 1
        ), array($this->defaultResponse[0]), 3);

        $this->testAction('/dane/wojewodztwa?limit=1');


        $this->assertArrayMatchingKeysSame(array(
            '_items' => array($this->defaultResponse[0]),
            '_meta' => array('page' => 1, 'max_results' => MPSearch::RESULTS_COUNT_MAX, 'total' => 3),
        ), $this->vars);

        $links = $this->vars['_links'];
        $this->assertUrlSamePathAndQuery('/dane/wojewodztwa?limit=1', $links['self']);
        $this->assertUrlSamePathAndQuery('/dane/wojewodztwa?limit=1&page=3', $links['last']);
        $this->assertUrlSamePathAndQuery('/dane/wojewodztwa?limit=1&page=2', $links['next']);

        $this->assertArrayNotHasKey('first', $links);
        $this->assertArrayNotHasKey('prev', $links);
    }

    public function testIndexHateoasMiddle() {
        $this->dataobjectExpectsFind(array(
            'conditions' => array(
                'dataset' => 'wojewodztwa'
            ),
            'limit' => 1,
            'page' => 2
        ), array($this->defaultResponse[0]), 3);

        $this->testAction('/dane/wojewodztwa?limit=1&page=2');

        $this->assertArrayMatchingKeysSame(array(
            '_items' => array($this->defaultResponse[0]),
            '_meta' => array('page' => 2, 'max_results' => MPSearch::RESULTS_COUNT_MAX, 'total' => 3),
        ), $this->vars);

        $links = $this->vars['_links'];
        $this->assertUrlSamePathAndQuery('/dane/wojewodztwa?limit=1&page=2', $links['self']);
        $this->assertUrlSamePathAndQuery('/dane/wojewodztwa?limit=1&page=3', $links['last']);
        $this->assertUrlSamePathAndQuery('/dane/wojewodztwa?limit=1&page=3', $links['next']);
        $this->assertUrlSamePathAndQuery('/dane/wojewodztwa?limit=1&page=1', $links['prev']);
        $this->assertUrlSamePathAndQuery('/dane/wojewodztwa?limit=1&page=1', $links['first']);
    }

    public function testIndexHateoasLast() {
        $this->dataobjectExpectsFind(array(
            'conditions' => array(
                'dataset' => 'wojewodztwa'
            ),
            'limit' => 1,
            'page' => 3
        ), array($this->defaultResponse[0]), 3);

        $this->testAction('/dane/wojewodztwa?limit=1&page=3');


        $this->assertArrayMatchingKeysSame(array(
            '_items' => array($this->defaultResponse[0]),
            '_meta' => array('page' => 3, 'max_results' => MPSearch::RESULTS_COUNT_MAX, 'total' => 3),
        ), $this->vars);

        $links = $this->vars['_links'];
        $this->assertUrlSamePathAndQuery('/dane/wojewodztwa?limit=1&page=3', $links['self']);
        $this->assertUrlSamePathAndQuery('/dane/wojewodztwa?limit=1&page=1', $links['first']);
        $this->assertUrlSamePathAndQuery('/dane/wojewodztwa?limit=1&page=2', $links['prev']);

        $this->assertArrayNotHasKey('last', $links);
        $this->assertArrayNotHasKey('next', $links);
    }
}