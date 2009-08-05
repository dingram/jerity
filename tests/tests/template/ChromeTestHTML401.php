<?php
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/jerity.php');
require_once(dirname(__FILE__).'/ChromeTest.php'); // needed for separator provider

class ChromeTestHTML401 extends PHPUnit_Framework_TestCase {
  public function setUp() {
    Template::setPath(dirname(dirname(__FILE__)).'/data/templates');
    RenderContext::setGlobalContext(
      RenderContext::makeContext(RenderContext::TYPE_HTML4_STRICT)
    );
  }

  public function testCustomLinkRender() {
    Chrome::clearLinks();

    Chrome::addLink('next', 'http://www.jerity.com/next');
    Chrome::addLink('author', 'mailto:info@jerity.com', true);

    ob_start();
    Chrome::outputLinkTags();
    $d = ob_get_clean();

    $this->assertContains('<link rel="next" href="http://www.jerity.com/next">', $d);
    $this->assertContains('<link rev="author" href="mailto:info@jerity.com">', $d);
  }

  /**
   * @dataProvider  ChromeTest::titleSeparatorProvider
   */
  public function testTitleRender($sep) {
    if (is_null($sep)) $sep = Chrome::getTitleSeparator();
    $title = array('Jerity', 'test', 'title');
    Chrome::setTitle($title);
    Chrome::setTitleSeparator($sep);

    ob_start();
    Chrome::outputTitleTag();
    $d = ob_get_clean();

    $this->assertContains('<title>'.implode($sep, $title).'</title>', $d);
  }

}
