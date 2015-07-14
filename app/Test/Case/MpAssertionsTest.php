<?php

App::uses('MpAssertions', 'Test');

class MpAssertionsTest extends CakeTestCase {

    public function testAssertArrayMatchingKeysSame() {
        MpAssertions::assertArrayMatchingKeysSame(
            array('k1' => 'v1', 'k2' => 'v2'),
            array('k2' => 'v2', 'k3' => 'v1')
        );

        // no matching keys is an empty-empty match
        MpAssertions::assertArrayMatchingKeysSame(
            array('k1' => 'v1', 'k2' => 'v2'),
            array('k4' => 'v2', 'k3' => 'v1')
        );
    }

    public function testAssertArrayMatchingKeysNotEquals() {
        MpAssertions::assertArrayMatchingKeysNotSame(
            array('k1' => 'v1', 'k2' => 'v2'),
            array('k2' => 'v3', 'k3' => 'v1')
        );
    }

    public function testAssertUrlEqualsQueryNoOrder() {
        MpAssertions::assertUrlSame('?val1=1&val2=2', '?val2=2&val1=1');
    }

    public function testAssertArraySameValues() {
        MpAssertions::assertArraySameValues(array(0,1,2), array(2,1,0));

        MpAssertions::assertArraySameValues(array('a' => 'A', 'b' => 'B'), array('c' => 'B', 'd' => 'A'));
    }
}