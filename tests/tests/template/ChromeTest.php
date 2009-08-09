<?php
require_once(dirname(dirname(dirname(__FILE__))).'/setUp.php');

class ChromeTest extends PHPUnit_Framework_TestCase {
  public function setUp() {
    Template::setPath(DATA_DIR.'templates');
  }

  public function testCustomRelLink() {
    Chrome::clearLinks();

    Chrome::addLink('next', 'http://www.jerity.com/next');
    $l = Chrome::getLinks();

    $this->assertTrue(is_array($l));
    $this->assertEquals(count($l), 1);
    $this->assertEquals(count($l[0]), 2);
    $this->assertEquals($l[0]['href'], 'http://www.jerity.com/next');
    $this->assertEquals($l[0]['rel'],  'next');
  }

  public function testCustomRevLink() {
    Chrome::clearLinks();

    Chrome::addLink('author', 'mailto:info@jerity.com', true);
    $l = Chrome::getLinks();

    $this->assertTrue(is_array($l));
    $this->assertEquals(count($l), 1);
    $this->assertEquals(count($l[0]), 2);
    $this->assertEquals($l[0]['rev'],  'author');
    $this->assertEquals($l[0]['href'], 'mailto:info@jerity.com');
  }

  public function testEmptyTitle() {
    Chrome::setTitle(null);
    $this->assertSame('', Chrome::getTitle());
  }

  /**
   * @dataProvider  titleSeparatorProvider
   */
  public function testTitleSeparator($sep) {
    if ($sep === null) $sep = Chrome::getTitleSeparator();
    $title = array('Jerity', 'test', 'title');
    Chrome::setTitle($title);
    $this->assertEquals(implode($sep, $title), Chrome::getTitle($sep));
  }

  public static function titleSeparatorProvider() {
    return array(
      array(null),
      array(''),
      array(' '),
      array(' & '),
      array('&'),
      array('&amp;'),
      array(' &amp; '),
      array('&raquo;'),
      array(' &raquo; '),
    );
  }

  public function testGetTitleArray() {
    $title = array('Jerity', 'test', 'title');
    Chrome::setTitle($title);
    $this->assertEquals($title, Chrome::getTitle(false));
  }

  public function testMetaName1() {
    Chrome::clearMetadata();
    $this->assertEquals(0, count(Chrome::getMetadata()));
    Chrome::addMetadata('generator', 'Jerity');
    $this->assertEquals(1, count(Chrome::getMetadata()));
    Chrome::removeMetadata('generator');
    $this->assertEquals(0, count(Chrome::getMetadata()));
  }

  public function testMetaName2() {
    Chrome::clearMetadata();
    $this->assertEquals(0, count(Chrome::getMetadata()));
    Chrome::addMetadata('generator', 'Jerity');
    Chrome::addMetadata('description', 'Jerity Test Page');
    $this->assertEquals(2, count(Chrome::getMetadata()));
    Chrome::removeMetadata('generator');
    $this->assertEquals(1, count(Chrome::getMetadata()));
    Chrome::removeMetadata('description');
    $this->assertEquals(0, count(Chrome::getMetadata()));
  }

  public function testMetaName3() {
    Chrome::clearMetadata();
    $this->assertEquals(0, count(Chrome::getMetadata()));
    Chrome::addMetadata('generator', 'Jerity');
    Chrome::addMetadata('description', 'Jerity Test Page');
    $this->assertEquals(2, count(Chrome::getMetadata()));
    Chrome::clearMetadata();
    $this->assertEquals(0, count(Chrome::getMetadata()));
  }

  public function testContent1() {
    $c = new Chrome('simple');
    $c->clearContent();
    $this->assertEquals(0, count($c->getContent()));
    $c->setContent('PASS');
    $this->assertEquals(1, count($c->getContent()));
    $c->setContent('PASS');
    $this->assertEquals(1, count($c->getContent()));
    $c->setContent('PASS', 'PASS');
    $this->assertEquals(2, count($c->getContent()));
    $c->setContent(array('PASS', 'PASS'));
    $this->assertEquals(2, count($c->getContent()));
    $c->clearContent();
    $this->assertEquals(0, count($c->getContent()));
  }

  public function testModularHead() {
    Chrome::setLanguage('en-gb');
    Chrome::setTitle('Test title');
    Chrome::clearMetadata();
    Chrome::addMetadata('Content-Type', 'text/html; charset=utf-8', Chrome::META_HTTP);
    Chrome::addMetadata('generator', 'Jerity');
    Chrome::addMetadata('description', 'Jerity test case page');
    Chrome::clearStylesheets();
    Chrome::addStylesheet('/css/common.css');
    Chrome::addStylesheet('/css/blah.css', 75);
    Chrome::clearScripts();
    Chrome::addScript('/js/scriptaculous.js', 25);
    Chrome::addScript('/js/prototype.js', 15);
    Chrome::clearIcons();
    Chrome::addIcon('/favicon.ico');
    Chrome::addIcon('/img/icons/favicon.png', Chrome::ICON_PNG);

    ob_start();
    Chrome::outputHead();
    $a = ob_get_clean();

    ob_start();
    echo RenderContext::getGlobalContext()->renderPreContent();
    Chrome::outputOpeningTags();
    Chrome::outputMetaTags();
    Chrome::outputTitleTag();
    Chrome::outputLinkTags();
    Chrome::outputStylesheetTags();
    Chrome::outputExternalScriptTags();
    Chrome::outputFaviconTags();
    Chrome::outputEndHead();
    $b = ob_get_clean();

    $this->assertSame($a, $b);
  }

}
