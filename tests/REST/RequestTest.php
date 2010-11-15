<?php
/**
 * @author     Dave Ingram <dave@dmi.me.uk>
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright (c) 2010, Dave Ingram, Nick Pope
 * @license    http://creativecommons.org/licenses/BSD/ CC-BSD
 * @package    jerity.test
 */

use \Jerity\REST;

/**
 * @author     Dave Ingram <dave@dmi.me.uk>
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright (c) 2010, Dave Ingram, Nick Pope
 * @license    http://creativecommons.org/licenses/BSD/ CC-BSD
 * @package    jerity.test
 * @group      REST
 */
class RestRequestTest extends PHPUnit_Framework_TestCase {

  public function testSimpleGET() {
    $r = new REST\Request('/api/rest/endpoint.json', 'GET', array(), '', array());
    $this->assertEquals('/api/rest/endpoint', $r->getUrl());
    $this->assertEquals('/api/rest/endpoint.json', $r->getUrl(false));
    $this->assertEquals('GET', $r->getVerb());
    $this->assertEquals(array(), $r->getArgs());
    $this->assertFalse($r->hasArg('foo'));
    $this->assertNull($r->getArg('foo'));
    $this->assertEquals('', $r->getRawBody());
    $this->assertEquals(array(), $r->getHeaders());
    $this->assertNull($r->getHeader('Content-Type'));
    $this->assertFalse($r->hasHeader('Content-Type'));
    $this->assertEquals('json', $r->getResponseFormat());
  }

  public function testSimpleForcedFormatGET() {
    $r = new REST\Request('/api/rest/endpoint.json', 'GET', array(), '', array(), 'xml');
    $this->assertEquals('/api/rest/endpoint', $r->getUrl());
    $this->assertEquals('/api/rest/endpoint.json', $r->getUrl(false));
    $this->assertEquals('GET', $r->getVerb());
    $this->assertEquals(array(), $r->getArgs());
    $this->assertFalse($r->hasArg('foo'));
    $this->assertNull($r->getArg('foo'));
    $this->assertEquals('', $r->getRawBody());
    $this->assertEquals(array(), $r->getHeaders());
    $this->assertNull($r->getHeader('Content-Type'));
    $this->assertFalse($r->hasHeader('Content-Type'));
    $this->assertEquals('xml', $r->getResponseFormat());
  }

  public function testSimpleForcedFormatGET2() {
    $r = new REST\Request('/api/rest/endpoint.json', 'GET', array(), '', array(), 'json');
    $r->setResponseFormat('xml');
    $this->assertEquals('/api/rest/endpoint', $r->getUrl());
    $this->assertEquals('/api/rest/endpoint.json', $r->getUrl(false));
    $this->assertEquals('GET', $r->getVerb());
    $this->assertEquals(array(), $r->getArgs());
    $this->assertFalse($r->hasArg('foo'));
    $this->assertNull($r->getArg('foo'));
    $this->assertEquals('', $r->getRawBody());
    $this->assertEquals(array(), $r->getHeaders());
    $this->assertNull($r->getHeader('Content-Type'));
    $this->assertFalse($r->hasHeader('Content-Type'));
    $this->assertEquals('xml', $r->getResponseFormat());
  }

  public function testSimpleDetectFormatUrlGET() {
    $r = new REST\Request('/api/rest/endpoint.json', 'GET', array(), '', array());
    $this->assertEquals('json', $r->getResponseFormat());
  }

  public function testSimpleDetectFormatHeadersGET() {
    $r = new REST\Request('/api/rest/endpoint', 'GET', array(), '', array('Accept'=>'application/xml'));
    $this->assertEquals('xml', $r->getResponseFormat());
  }

  public function testSimpleDetectFormatHeadersMultipleGET() {
    $r = new REST\Request('/api/rest/endpoint', 'GET', array(), '', array('Accept'=>'application/xml, application/json'));
    $this->assertEquals('xml', $r->getResponseFormat());
  }

  public function testSimpleDetectFormatHeadersPrecedenceGET() {
    $r = new REST\Request('/api/rest/endpoint', 'GET', array(), '', array('Accept'=>'application/xml;q=1.0, application/json;q=0.5'));
    $this->assertEquals('xml', $r->getResponseFormat());
  }

  public function testSimpleDetectFormatHeadersPrecedenceGET2() {
    $r = new REST\Request('/api/rest/endpoint', 'GET', array(), '', array('Accept'=>'application/xml;q=0.5, application/json;q=0.9'));
    $this->assertEquals('xml', $r->getResponseFormat());
  }

  /*
  public function testSimpleDetectFormatUrlGET() {
    $r = new REST\Request('/api/rest/endpoint.json', 'GET', array(), '', array());
    $this->assertEquals('json', $r->getResponseFormat());
  }

  public function testSimpleDetectFormatUrlGET() {
    $r = new REST\Request('/api/rest/endpoint.json', 'GET', array(), '', array());
    $this->assertEquals('json', $r->getResponseFormat());
  }
   */

}

# vim:et:ts=2:sts=2:sw=2:nowrap:ft=php:fdm=marker
