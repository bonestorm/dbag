<?php

require_once 'ZFExt/Model/Entry.php';

class ZFExt_Model_EntryTest extends PHPUnit_Framework_TestCase {

	public function testSetsAllowedDomainObjectProperty() {
		$entry = new ZFExt_Model_Entry;
		$entry->title = 'My Title';
		$this->assertEquals('My Title', $entry->title);
	}

	public function testConstructorInjectionOfProperties() {
		$data = array(
			'title' => 'My Title',
			'content' => 'My Content',
			'published_date' => '2009-08-17T17:30:00Z',
			'author' => new ZFExt_Model_Author
		);
		$entry = new ZFExt_Model_Entry($data);
		$expected = $data;
		$expected['id'] = null;
		$this->assertsEquals($expected, $entry->toArray());
	}

	public function testReturnsIssetStatusOfProperties() {
		$entry = new ZFExt_Model_Entry;
		$entry->title = 'My Title';
		unset($entry->title);
		$this->assertFalse(isset($entry->title));
	}

	public function testCannotSetNewPropertiesUnlessDefinedForDomainObject()
	{
		$entry = new ZFExt_Model_Entry;
		try {
			$entry->notdefined = 1;
			$this->fail('Setting new property not defined in class should have reaised an Exception');
		} catch (ZFExt_Model_Exception $e) {}
	}

}

