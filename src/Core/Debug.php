<?php
/**
 * @author     Dave Ingram <dave@dmi.me.uk>
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright (c) 2010, Dave Ingram, Nick Pope
 * @license    http://creativecommons.org/licenses/BSD/ CC-BSD
 * @package    jerity.core
 */

namespace Jerity\Core;

/**
 * Debugging class providing useful methods for diagnosing problems and
 * performance.
 *
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright (c) 2010, Dave Ingram, Nick Pope
 * @license    http://creativecommons.org/licenses/BSD/ CC-BSD
 * @package    jerity.core
 */
class Debug {

  /**
   * If true, debugging output is enabled.
   *
   * @var  boolean
   */
  protected static $enabled = false;

  /**
   * If true, the redirects will be paused if debugging is enabled.
   *
   * @var  boolean
   */
  protected static $pause_redirect = false;

  /**
   * Non-instantiable class.
   */
  // @codeCoverageIgnoreStart
  private function __construct() {
  }
  // @codeCoverageIgnoreEnd

  ##############################################################################
  # debugging control {{{

  /**
   * Checks if debugging is enabled.
   *
   * @return  boolean
   */
  public static function isEnabled() {
    return self::$enabled;
  }

  /**
   * Enables or disables debugging.  This includes:
   *
   *  - Toggling xdebug
   *  - Toggling error display
   *
   * @param  boolean  $enabled  true to enable debugging, false to disable.
   */
  public static function setEnabled($enabled) {
    self::$enabled = (boolean) $enabled;
    if ($enabled) {
      if (extension_loaded('xdebug')) xdebug_enable();
      ini_set('display_errors', 'on');
    } else {
      if (extension_loaded('xdebug')) xdebug_disable();
      ini_set('display_errors', 'off');
    }
  }

  /**
   * Checks or sets whether to pause on redirect.
   *
   * @param  bool  $pause  Whether to pause on redirect.
   *
   * @return  bool
   */
  public static function pauseOnRedirect($pause = null) {
    if (!is_null($pause) && is_bool($pause)) {
      self::$pause_redirect = $pause;
    }
    return self::$pause_redirect;
  }

  # }}} debugging control
  ##############################################################################

  ##############################################################################
  # logging/message tools {{{

  /**
   * Outputs a comment based on the current render context into the document.
   *
   * @param  string  $text  The debugging text to output.
   *
   * @todo  Allow single line comments with // and #
   */
  public static function comment($text) {
    if (!self::$enabled) return;
    $ctx = RenderContext::get();
    switch ($ctx->getLanguage()) {
      case RenderContext::LANG_FBML:
      case RenderContext::LANG_HTML:
      case RenderContext::LANG_MHTML:
      case RenderContext::LANG_WML:
      case RenderContext::LANG_XHTML:
      case RenderContext::LANG_XML:
        $comment_open  = '<!--';
        $comment_close = '-->';
        break;
      case RenderContext::LANG_FBJS:
      case RenderContext::LANG_JSON:
        $comment_open  = '/*';
        $comment_close = '*/';
        break;
      case RenderContext::LANG_TEXT:
      default:
        # Don't know how to handle this...
        return;
    }
    $multiline = (strpos($text, PHP_EOL) !== false);
    echo $comment_open, ($multiline ? PHP_EOL : ' ');
    echo $text;
    echo ($multiline ? PHP_EOL : ' '), $comment_close, PHP_EOL;
  }

  /**
   * Outputs a block containing the data into the document.
   *
   * @param  mixed    $data       The debugging data to output.
   * @param  boolean  $highlight  Whether the data should be highlighted.
   * @param  boolean  $collapsed  Should the debug block be collapsed initially
   *
   * @todo  Tidy this up.
   * @todo  Formatting and highlighting without xdebug.
   */
  public static function out($data, $highlight = true, $collapsed = false) {
    if (!self::$enabled) return;
    static $count = 0;
    $id = '__debug'.$count;
    echo PHP_EOL;
    echo '<div id="'.$id.'" style="background: #fed; border: solid 2px #edc; font-size: 12px; margin: 1em; padding: 0.3em; width: auto;">';
    echo '<div style="background: #edc; overflow: hidden; padding: 0.3em;">';
    echo '<span style="font-weight: bold;">Debug</span>';
    echo '<div style="float: right; font-size: 10px;">( ';
    $style = 'cursor: pointer; text-decoration: underline;';
    if (!$highlight) {
      echo '<span style="'.$style.'" onclick="document.getElementById(\''.$id.'_data\').select();">Select All</span> | ';
    }
    echo '<span style="'.$style.'" onclick="var e = document.getElementById(\''.$id.'_data\'); if (e.style.display == \'none\') { e.style.display = \'block\'; this.innerHTML = \'Collapse\'; } else { e.style.display = \'none\'; this.innerHTML = \'Expand\'; }">'.($collapsed ? 'Expand' : 'Collapse').'</span> | ';
    echo '<span style="'.$style.'" onclick="var e = document.getElementById(\''.$id.'\'); e.parentNode.removeChild(e);">Remove</span>';
    echo ' )</div>';
    if (extension_loaded('xdebug')) {
      printf('<span style="display: block; margin-top: 0.3em; white-space: nowrap;">%s:%s in %s::%s()</span>',
        str_replace($_SERVER['DOCUMENT_ROOT'].'/', '', xdebug_call_file()),
        xdebug_call_line(),
        xdebug_call_class(),
        xdebug_call_function()
      );
    } else {
      # TODO: Formatting and highlighting without xdebug.
    }
    echo '</div>';
	echo '<pre style="background: none; border: none; margin: none; padding: none;">';
    $style = 'background: none; border: none; margin-top: 0.3em; max-height: 150px; width: 100%;';
    if ($collapsed) $style .= ' display: none;';
    if ($highlight) {
      echo '<div id="'.$id.'_data" style="'.$style.' font-family: monospace; max-height: 150px; overflow: auto; white-space: pre;">';
      # TODO: Need to escape data without clobbering highlight/xdebug modifications.
      var_dump($data);
      echo '</div>';
    } else {
      echo '<textarea cols="80" rows="8" id="'.$id.'_data" style="'.$style.'">';
      ob_start();
      var_dump($data);
      $data = ob_get_clean();
      echo strip_tags($data);
      echo '</textarea>';
    }
	echo '</pre>';
    echo '</div>';
    echo PHP_EOL, PHP_EOL;
    $count++;
  }

  /**
   * Outputs a message to a log file.
   *
   * @param  mixed  $message  The debugging message to log.
   *
   * @todo  Implement this method...
   */
  public static function log($message) {
    if (!self::$enabled) return;
    # TODO: Implement...
  }

  # }}} logging/message tools
  ##############################################################################

}

# vim:et:ts=2:sts=2:sw=2:nowrap:ft=php:fdm=marker
