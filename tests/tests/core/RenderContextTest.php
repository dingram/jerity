<?php
require_once(dirname(dirname(dirname(__FILE__))).'/setUp.php');

class RenderContextTest extends PHPUnit_Framework_TestCase {

  /**
   * @covers RenderContext::getGlobalContext()
   */
  public function testInitialGlobalContext() {
    $ctx = RenderContext::getGlobalContext();
    $this->assertType('RenderContext', $ctx);
  }

  /**
   * @covers RenderContext::pushGlobalContext()
   * @covers RenderContext::popGlobalContext()
   */
  public function testPushPopContext() {
    $ctx1 = RenderContext::getGlobalContext();
    $this->assertType('RenderContext', $ctx1);

    $newctx = RenderContext::makeContext(RenderContext::TYPE_HTML5);
    RenderContext::pushGlobalContext($newctx);
    $ctx2 = RenderContext::getGlobalContext();
    $this->assertNotSame($ctx1, $ctx2);
    $this->assertNotEquals($ctx1, $ctx2);
    $this->assertEquals($newctx, $ctx2);
    $this->assertSame($newctx, $ctx2);

    $ctx2a = RenderContext::popGlobalContext();
    $this->assertEquals($newctx, $ctx2a);
    $this->assertSame($newctx, $ctx2a);
    $ctx1a = RenderContext::getGlobalContext();
    $this->assertEquals($ctx1, $ctx1a);
    $this->assertSame($ctx1, $ctx1a);
  }

  /**
   * @covers RenderContext::getGlobalContext()
   * @covers RenderContext::pushGlobalContext()
   * @covers RenderContext::popGlobalContext()
   */
  public function testEmptyGlobalContext() {
    $ctxs = array();
    while ($ctx = RenderContext::popGlobalContext()) {
      $ctxs[] = $ctx;
    }

    $ctx = RenderContext::getGlobalContext();
    $this->assertSame(null, $ctx);

    $ctxs = array_reverse($ctxs);
    foreach ($ctxs as $ctx) {
      RenderContext::pushGlobalContext($ctx);
    }
  }

  /**
   * @covers RenderContext::makeContext()
   * @covers RenderContext::getLanguage()
   * @covers RenderContext::getVersion()
   * @covers RenderContext::getDialect()
   * @dataProvider makeContextProvider
   */
  public function testMakeContext($type) {
    $bits = explode('-', $type);
    while (count($bits) < 3) {
      $bits[] = null;
    }
    list($language, $version, $dialect) = $bits;
    $version = doubleval($version);
    if (is_null($dialect))  $dialect='';

    $ctx = RenderContext::makeContext($type);
    $this->assertSame(  $language, $ctx->getLanguage());
    $this->assertEquals($version,  $ctx->getVersion());
    $this->assertSame(  $dialect,  $ctx->getDialect());
  }

  public static function makeContextProvider() {
    return array(
      array(RenderContext::TYPE_HTML4_FRAMESET),
      array(RenderContext::TYPE_HTML4_STRICT),
      array(RenderContext::TYPE_HTML4_TRANSITIONAL),
      array(RenderContext::TYPE_HTML5),
      array(RenderContext::TYPE_XHTML1_FRAMESET),
      array(RenderContext::TYPE_XHTML1_MOBILE),
      array(RenderContext::TYPE_XHTML1_STRICT),
      array(RenderContext::TYPE_XHTML1_TRANSITIONAL),
      array(RenderContext::TYPE_XHTML1_1),
      array(RenderContext::TYPE_XHTML1_1_MOBILE),
      array(RenderContext::TYPE_XHTML1_2_MOBILE),
    );
  }

  /**
   * @covers RenderContext::makeContext()
   * @expectedException InvalidArgumentException
   */
  public function testMakeContextFail() {
    $ctx = RenderContext::makeContext('js-1.1');
  }

  /**
   * @covers RenderContext::getDoctype()
   * @covers RenderContext::renderPreContent()
   * @covers RenderContext::setLanguage()
   * @covers RenderContext::setVersion()
   * @covers RenderContext::setDialect()
   * @covers RenderContext::getLanguage()
   * @covers RenderContext::getVersion()
   * @covers RenderContext::getDialect()
   * @dataProvider getDoctypeProvider
   */
  public function testGetDoctype($lang, $ver, $dialect, $expected) {
    $ctx = new RenderContext();
    $ctx->setLanguage($lang);
    $ctx->setVersion($ver);
    $ctx->setDialect($dialect);
    $this->assertSame($lang,     $ctx->getLanguage());
    $this->assertEquals($ver,    $ctx->getVersion());
    $this->assertSame($dialect,  $ctx->getDialect());
    $this->assertSame($expected, $ctx->getDoctype());
    $preContent = $ctx->renderPreContent();
    if ($lang == RenderContext::LANG_XML || $lang == RenderContext::LANG_XHTML) {
      $this->assertContains('<'.'?xml version="1.0" encoding="utf-8" ?'.">\n", $preContent);
      if ($expected !== '') {
        $this->assertContains($expected, $preContent);
      }
    } elseif ($preContent !== '') {
      $this->assertSame($expected."\n", $preContent);
    }
  }

  public static function getDoctypeProvider() {
    return array(
      array(RenderContext::LANG_HTML , 2   , RenderContext::DIALECT_NONE        , '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML//EN">'),
      array(RenderContext::LANG_HTML , 3.2 , RenderContext::DIALECT_NONE        , '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">'),
      array(RenderContext::LANG_HTML , 4.01, RenderContext::DIALECT_STRICT      , '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">'),
      array(RenderContext::LANG_HTML , 4.01, RenderContext::DIALECT_TRANSITIONAL, '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'),
      array(RenderContext::LANG_HTML , 4.01, RenderContext::DIALECT_FRAMESET    , '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">'),
      array(RenderContext::LANG_HTML , 5   , RenderContext::DIALECT_NONE        , '<!DOCTYPE html>'),
      array(RenderContext::LANG_XHTML, 1.0 , RenderContext::DIALECT_STRICT      , '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'),
      array(RenderContext::LANG_XHTML, 1.0 , RenderContext::DIALECT_TRANSITIONAL, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'),
      array(RenderContext::LANG_XHTML, 1.0 , RenderContext::DIALECT_FRAMESET    , '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">'),
      array(RenderContext::LANG_XHTML, 1.1 , RenderContext::DIALECT_NONE        , '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">'),
      array(RenderContext::LANG_XHTML, 1.0 , RenderContext::DIALECT_MOBILE      , '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">'),
      array(RenderContext::LANG_XHTML, 1.1 , RenderContext::DIALECT_MOBILE      , '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.1//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile11.dtd">'),
      array(RenderContext::LANG_XHTML, 1.2 , RenderContext::DIALECT_MOBILE      , '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.2//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">'),
      array(RenderContext::LANG_CSS  , 2   , RenderContext::DIALECT_NONE        , ''),
      array(RenderContext::LANG_FBJS , 0   , RenderContext::DIALECT_NONE        , ''),
      array(RenderContext::LANG_FBML , 0   , RenderContext::DIALECT_NONE        , ''),
      array(RenderContext::LANG_JS   , 1.6 , RenderContext::DIALECT_NONE        , ''),
      array(RenderContext::LANG_JSON , 0   , RenderContext::DIALECT_NONE        , ''),
      array(RenderContext::LANG_MHTML, 0   , RenderContext::DIALECT_NONE        , ''),
      array(RenderContext::LANG_TEXT , 0   , RenderContext::DIALECT_NONE        , ''),
      array(RenderContext::LANG_WML  , 1   , RenderContext::DIALECT_NONE        , ''),
      array(RenderContext::LANG_XML  , 1.0 , RenderContext::DIALECT_NONE        , ''),
    );
  }

  /**
   * @covers RenderContext::getDoctype()
   * @dataProvider getDoctypeFailProvider
   * @expectedException InvalidArgumentException
   */
  public function testGetDoctypeFail($lang, $ver, $dialect) {
    $ctx = new RenderContext();
    $ctx->setLanguage($lang);
    $ctx->setVersion($ver);
    $ctx->setDialect($dialect);
    $dt = $ctx->getDoctype();
  }

  public static function getDoctypeFailProvider() {
    return array(
      array(RenderContext::LANG_HTML , 3   , RenderContext::DIALECT_NONE        ),
      array(RenderContext::LANG_HTML , 4.01, RenderContext::DIALECT_NONE        ),
      array(RenderContext::LANG_XHTML, 1.05, RenderContext::DIALECT_STRICT      ),
    );
  }

  /**
   * @covers RenderContext::getContentType()
   * @dataProvider contentTypeProvider
   */
  public function testContentTypeDetection($lang, $dialect, $strict, $expected) {
    $ctx = new RenderContext();
    $ctx->setLanguage($lang);
    $ctx->setDialect($dialect);
    $this->assertSame($expected, $ctx->getContentType($strict));
  }

  public static function contentTypeProvider() {
    return array(
      array(RenderContext::LANG_HTML,  RenderContext::DIALECT_NONE,   true,  RenderContext::CONTENT_HTML),
      array(RenderContext::LANG_XHTML, RenderContext::DIALECT_MOBILE, true,  RenderContext::CONTENT_XHTML_MP),
      array(RenderContext::LANG_XHTML, RenderContext::DIALECT_STRICT, true,  RenderContext::CONTENT_XHTML),
      array(RenderContext::LANG_XHTML, RenderContext::DIALECT_STRICT, false, RenderContext::CONTENT_HTML),
      array(RenderContext::LANG_JS   , RenderContext::DIALECT_NONE  , true , RenderContext::CONTENT_JS),
      array(RenderContext::LANG_FBJS , RenderContext::DIALECT_NONE  , true , RenderContext::CONTENT_JS),
      array(RenderContext::LANG_TEXT , RenderContext::DIALECT_NONE  , true , RenderContext::CONTENT_TEXT),
      array(RenderContext::LANG_XML  , RenderContext::DIALECT_NONE  , true , RenderContext::CONTENT_XML),
      array(RenderContext::LANG_FBML , RenderContext::DIALECT_NONE  , true , RenderContext::CONTENT_XML),
      array(RenderContext::LANG_JSON , RenderContext::DIALECT_NONE  , true , RenderContext::CONTENT_JSON),
      array(RenderContext::LANG_CSS  , RenderContext::DIALECT_NONE  , true , RenderContext::CONTENT_CSS),
      array(RenderContext::LANG_MHTML, RenderContext::DIALECT_NONE  , true , RenderContext::CONTENT_XHTML_MP),
      array(RenderContext::LANG_WML  , RenderContext::DIALECT_NONE  , true , RenderContext::CONTENT_WML),
      array('binary'                 , RenderContext::DIALECT_NONE  , true , 'application/octet-stream'),
      array(''                       , RenderContext::DIALECT_NONE  , true , 'application/octet-stream'),
    );
  }

  /**
   * @covers RenderContext::getContentType()
   */
  public function testContentTypeCache() {
    $ctx = RenderContext::makeContext(RenderContext::TYPE_HTML5);
    $this->assertSame(RenderContext::CONTENT_HTML, $ctx->getContentType());
    $this->assertSame(RenderContext::CONTENT_HTML, $ctx->getContentType());
  }

  /**
   * @covers RenderContext::isXMLSyntax()
   * @dataProvider xmlSyntaxProvider
   */
  public function testIsXMLSyntax($lang, $expected) {
    $ctx = new RenderContext();
    $ctx->setLanguage($lang);
    $this->assertSame($expected, $ctx->isXMLSyntax());
  }

  public static function xmlSyntaxProvider() {
    return array(
      array(RenderContext::LANG_CSS  , false),
      array(RenderContext::LANG_FBJS , false),
      array(RenderContext::LANG_FBML , true ),
      array(RenderContext::LANG_HTML,  false),
      array(RenderContext::LANG_JS   , false),
      array(RenderContext::LANG_JSON , false),
      array(RenderContext::LANG_MHTML, false),
      array(RenderContext::LANG_TEXT , false),
      array(RenderContext::LANG_WML  , true ),
      array(RenderContext::LANG_XHTML, true ),
      array(RenderContext::LANG_XML  , true ),
      array('binary'                 , false),
      array(''                       , false),
    );
  }

}
