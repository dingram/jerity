<?php
##############################################################################
# Copyright © 2010 David Ingram, Nicholas Pope
#
# This work is licenced under the Creative Commons BSD License License. To
# view a copy of this licence, visit http://creativecommons.org/licenses/BSD/
# or send a letter to Creative Commons, 171 Second Street, Suite 300,
# San Francisco, California 94105, USA.
##############################################################################


class ContentTest extends PHPUnit_Framework_TestCase {
  public function setUp() {
    Template::setPath(DATA_DIR.'templates');
  }

  /**
   */
  public function testRender() {
    $c = new Content('simple');
    $c->set('content', '');
    $this->assertSame('', $c->render());
    $c->set('content', 'PASS');
    $this->assertSame('PASS', $c->render());
  }

  /**
   */
  public function testRender2() {
    $c = Content::create('simple');
    $c->set('content', '');
    $this->assertSame('', $c->render());
    $c->set('content', 'PASS');
    $this->assertSame('PASS', $c->render());
  }

}
