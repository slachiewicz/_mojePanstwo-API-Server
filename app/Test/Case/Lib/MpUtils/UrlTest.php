<?php

App::uses('MpUtils\\Url', 'Lib');

class UrlTest extends CakeTestCase {

    public function testPassString() {
        $url = 'https://krzysiek@mojepanstwo.pl/path?q1=1&q2=2&flag=#fragment';
        $u = new MpUtils\Url($url);

        $this->assertSame($url, $u->buildUrl());
    }

    public function testEmpty() {
        $u = new MpUtils\Url();

        $this->assertSame('', $u->buildUrl());
    }

    public function testSet() {
        $u = new MpUtils\Url();

        $u->set('host', 'api.mojepanstwo.pl');

        $this->assertSame('http://api.mojepanstwo.pl', $u->buildUrl());
    }

    public function testPathAbsolute() {
        $u = new MpUtils\Url();

        $u->set('host', 'api.mojepanstwo.pl');
        $u->set('path', '/path');

        $this->assertSame('http://api.mojepanstwo.pl/path', $u->buildUrl());
    }


    public function testPathRelative() {
        $u = new MpUtils\Url();

        $u->set('host', 'api.mojepanstwo.pl');
        $u->set('path', 'path');

        $this->assertSame('http://api.mojepanstwo.pl/path', $u->buildUrl());
    }


    public function testSetParamNew() {
        $u = new MpUtils\Url('http://mojepanstwo.pl');

        $u->setParam('q1', '1');
        $this->assertSame('http://mojepanstwo.pl?q1=1', $u->buildUrl());

        $u->setParam('q2', '2');
        // TODO assert with no order on params? @see DataobjectsControlerTest assertUrlSame
        $this->assertSame('http://mojepanstwo.pl?q1=1&q2=2', $u->buildUrl());
    }

    public function testSetParamChange() {
        $u = new MpUtils\Url('http://mojepanstwo.pl?q1=1');

        $u->setParam('q1', '2');
        $this->assertSame('http://mojepanstwo.pl?q1=2', $u->buildUrl());
    }

    public function testParamRemove() {
        $u = new MpUtils\Url('http://mojepanstwo.pl?q1=1&q2=2');

        $u->removeParam('q1');
        $this->assertSame('http://mojepanstwo.pl?q2=2', $u->buildUrl());
    }

    public function testParamRemoveLast() {
        $u = new MpUtils\Url('http://mojepanstwo.pl?q1=1');

        $u->removeParam('q1');
        $this->assertSame('http://mojepanstwo.pl', $u->buildUrl());
    }
}