<?php
/**
 * @author     Dave Ingram <dave@dmi.me.uk>
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright (c) 2010, Dave Ingram, Nick Pope
 * @license    http://creativecommons.org/licenses/BSD/ CC-BSD
 * @package    jerity.test
 */

use \Jerity\REST;
use \Jerity\Core\RenderContext;

/**
 * @author     Dave Ingram <dave@dmi.me.uk>
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright (c) 2010, Dave Ingram, Nick Pope
 * @license    http://creativecommons.org/licenses/BSD/ CC-BSD
 * @package    jerity.test
 * @group      REST
 */
class RestManagerTest extends PHPUnit_Framework_TestCase {

  public function testGetRequestHeaders() {
    $this->assertEquals(array(), REST\Manager::getRequestHeaders());
    $tmp = $_SERVER;
    $_SERVER['HTTP_HOST'] = 'phpunit.tests.jerity';
    $this->assertEquals(array('Host'=>'phpunit.tests.jerity'), REST\Manager::getRequestHeaders());
    $_SERVER['HTTP_CONTENT_TYPE'] = 'application/octet-stream';
    // uses cached headers
    $this->assertEquals(array('Host'=>'phpunit.tests.jerity'), REST\Manager::getRequestHeaders());
    // force rebuild
    $this->assertEquals(array('Host'=>'phpunit.tests.jerity','Content-Type'=>'application/octet-stream'), REST\Manager::getRequestHeaders(true));
    $_SERVER = $tmp;
  }

  public function testSetDefaultFormat() {
    // nothing to actually test...?
    REST\Manager::setDefaultFormat('xml');
    REST\Manager::setDefaultFormat('json');
  }

  public function testSetBasePath() {
    // nothing to actually test...?
    REST\Manager::setBasePath('abc/def/ghi');
    REST\Manager::setBasePath('/abc/def/ghi');
    REST\Manager::setBasePath('abc/def/ghi/');
    REST\Manager::setBasePath('/abc/def/ghi/');
    REST\Manager::setBasePath('/');
  }

  public function testSetConstantHandlers() {
    // nothing to actually test...?
    REST\Manager::setConstantHandlers(true);
    REST\Manager::setConstantHandlers(false);
  }

}

# vim:et:ts=2:sts=2:sw=2:nowrap:ft=php:fdm=marker
