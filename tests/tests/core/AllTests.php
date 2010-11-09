<?php
##############################################################################
# Copyright © 2010 David Ingram, Nicholas Pope
#
# This work is licenced under the Creative Commons BSD License License. To
# view a copy of this licence, visit http://creativecommons.org/licenses/BSD/
# or send a letter to Creative Commons, 171 Second Street, Suite 300,
# San Francisco, California 94105, USA.
##############################################################################

require_once(dirname(dirname(dirname(__FILE__))).'/setUp.php');
require_once('ArrayUtilTest.php');
require_once('InflectorTest.php');
require_once('NumberTest.php');
require_once('RenderContextTest.php');
require_once('StringTest.php');
require_once('TagTestHTML401.php');
require_once('TagTestXHTML10.php');

class Jerity_Core_AllTests {
  public static function suite() {
    $suite = new PHPUnit_Framework_TestSuite();
    $suite->setName('jerity-core');

    $suite->addTestSuite('ArrayUtilTest');
    $suite->addTestSuite('NumberTest');
    $suite->addTestSuite('RenderContextTest');
    $suite->addTestSuite('StringTest');
    $suite->addTestSuite('InflectorTest');
    $suite->addTestSuite('TagTestHTML401');
    $suite->addTestSuite('TagTestXHTML10');

    return $suite;
  }
}
