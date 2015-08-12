<?php

App::uses('AppController', 'Controller');

/**
 * PagesControllerTest class
 *
 * @package       Cake.Test.Case.Controller
 */
class AppControllerTest extends CakeTestCase {

    public $fixtures = array('plugin.paszport.api_app');

    public function setUp() {
        parent::setUp();

        $this->controller = new AppController(new CakeRequest(null, false), new CakeResponse());
        $this->controller->Auth = $this->getMock('Auth', array('allow'));
    }

    /**
     * @expectedException ForbiddenException
     * @expectedExceptionCode 403
     * @return void
     */
    public function testApiKeyNeeded() {
        $this->controller->beforeFilter();
    }

    /**
     * @expectedException ForbiddenException
     * @expectedExceptionCode 403
     * @return void
     */
    public function testUnkownKeyBlocked() {
        $this->controller->request->query = array('apiKey' => '000');
        $this->controller->beforeFilter();
    }

    public function testAJAXFromPortalPassesEvenWithoutAPIKey() {
        $_SERVER['HTTP_ORIGIN'] = 'http://mojepanstwo.pl';

        $this->controller->beforeFilter();
    }

    public function testAJAXFromSSLPortalPassesEvenWithoutAPIKey() {
        $_SERVER['HTTP_ORIGIN'] = 'https://mojepanstwo.pl';

        $this->controller->beforeFilter();
    }

    public function testPortalApiKeyPassess() {
        $this->controller->request->query = array('apiKey' => ROOT_API_KEY);

        $this->controller->beforeFilter();
    }

    public function testBackendKeyPasses() {
        $this->controller->request->query = array('apiKey' => 234);

        $this->controller->beforeFilter();
    }

    /**
     * @expectedException BadRequestException
     * @expectedExceptionCode 400
     * @return void
     */
    public function testWebKeyOriginHeaderNeeded() {
        $this->controller->request->query = array('apiKey' => '123');

        $this->controller->beforeFilter();
    }

    /**
     * @expectedException ForbiddenException
     * @expectedExceptionCode 403
     * @return void
     */
    public function testWebKeyDomainMismatch() {
        $this->controller->request->query = array('apiKey' => '123');
        $_SERVER['HTTP_ORIGIN'] = 'http://example.other';

        $this->controller->beforeFilter();
    }

    public function testWebKeyPassesExactDomain() {
        $this->controller->request->query = array('apiKey' => '123');
        $_SERVER['HTTP_ORIGIN'] = 'http://example1.com';

        $this->controller->beforeFilter();
    }

    public function testWebKeyPassesWildcardDomain1() {
        $this->controller->request->query = array('apiKey' => '345');
        $_SERVER['HTTP_ORIGIN'] = 'http://subdomain.example2.com';

        $this->controller->beforeFilter();
    }

    public function testWebKeyPassesWildcardDomain2() {
        $this->controller->request->query = array('apiKey' => '345');
        $_SERVER['HTTP_ORIGIN'] = 'http://example2.com';

        $this->controller->beforeFilter();
    }
}