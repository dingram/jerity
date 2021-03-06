<?php
/**
 * @author     Dave Ingram <dave@dmi.me.uk>
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright (c) 2010, Dave Ingram, Nick Pope
 * @license    http://creativecommons.org/licenses/BSD/ CC-BSD
 * @package    jerity.ui
 */

namespace Jerity\UI;

use \Jerity\Core\Redirector;
use \Jerity\Core\RedirectorException;
use \Jerity\Core\RenderContext;
use \Jerity\Core\Renderable;

/**
 * Notification class for standard formatting of messages. Also provides the
 * ability to send notifications over redirects.
 *
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright (c) 2010, Dave Ingram, Nick Pope
 * @license    http://creativecommons.org/licenses/BSD/ CC-BSD
 * @package    jerity.ui
 */
class Notification implements Renderable {

  /**
   *
   */
  const PLAIN       = 'msg_plain';

  /**
   *
   */
  const INFORMATION = 'msg_info';

  /**
   *
   */
  const WARNING     = 'msg_warn';

  /**
   *
   */
  const ERROR       = 'msg_error';

  /**
   * The key to use for storing notification data in the redirector state.
   */
  const DATA_KEY = '__notification';

  /**
   * The message to be output.
   *
   * @var  string
   */
  protected $message = null;

  /**
   * The type of notification to render.
   * This is used as the CSS class.
   *
   * @var  string
   */
  protected $type = self::PLAIN;

  /**
   * Creates a new notification and returns it for fluent method chaining.
   *
   * @param  string  $message
   * @param  string  $type
   *
   * @return  Notification  For fluent method chaining.
   *
   * @throws  \InvalidArgumentException
   */
  public static function create($message = null, $type = self::PLAIN) {
    return new static($message, $type);
  }

  /**
   * Creates a new notification.
   *
   * @param  string  $message
   * @param  string  $type
   *
   * @throws  \InvalidArgumentException
   */
  public function __construct($message = null, $type = self::PLAIN) {
    $this->setMessage($message);
    $this->setType($type);
  }

  /**
   * Overrides the default object to string conversion to force the Renderable
   * item to be rendered in string context.
   *
   * @return  string
   */
  public function __toString() {
    return $this->render();
  }

  /**
   * Render the item using the current global rendering context, and return it
   * as a string.
   *
   * @return string
   */
  public function render() {
    if (empty($this->message)) return '';

    # Get the current render context.
    $ctx = RenderContext::get();

    # Render the message appropriately
    switch ($ctx->getLanguage()) {
      case RenderContext::LANG_HTML:
      case RenderContext::LANG_XHTML:
        $output  = '<div class="'.$this->type.'">';
        $output .= $this->message;
        $output .= '</div>';
        break;
      case RenderContext::LANG_XML:
        $output  = '<notification type="'.$this->type.'">';
        $output .= $this->message;
        $output .= '</notification>';
        break;
      default:
        # TODO: Throw exception as we don't know how to render?
        $output = '';
    }
    return $output;
  }

  /**
   * Gets the message to be displayed.
   *
   * @return  string
   */
  public function getMessage() {
    return $this->message;
  }

  /**
   * Sets the message to be displayed.
   *
   * Note: No checking is performed on this message so that markup may be
   *       used.  Please ensure that you escape user input appropriately.
   *
   * @param  string  $message
   *
   * @return  Notification  For fluent method chaining.
   */
  public function setMessage($message) {
    $this->message = $message;
    return $this;
  }

  /**
   * Appends additional content to the message to be displayed.
   *
   * Note: No checking is performed on this message so that markup may be
   *       used.  Please ensure that you escape user input appropriately.
   *
   * @param  string  $message
   *
   * @return  Notification  For fluent method chaining.
   */
  public function appendMessage($message) {
    $this->message .= $message;
    return $this;
  }

  /**
   * Gets the type of the notification.  This type string is used as the CSS
   * class name.
   *
   * @return  string
   */
  public function getType() {
    return $this->type;
  }

  /**
   * Sets the type of the notification.  This type string is used as the CSS
   * class name.
   *
   * @param  string  $type
   *
   * @return  Notification  For fluent method chaining.
   *
   * @throws  \InvalidArgumentException
   */
  public function setType($type) {
    switch ($type) {
      case self::PLAIN:
      case self::INFORMATION:
      case self::WARNING:
      case self::ERROR:
        break;
      default:
        # TODO: Allow custom types?  Restrict to valid characters for CSS class.
        throw new \InvalidArgumentException('Unrecognised notification type: '.$type);
    }

    $this->type = $type;
    return $this;
  }

  /**
   * Redirects with saved state containing a message.  The notification is
   * automatically added to the extra state data.
   *
   * @see  \Jerity\Core\Redirector::redirect()
   *
   * @param  string  $url         Where to redirect to.
   * @param  mixed   $extra_data  Extra data to preserve across redirect.
   *
   * @throws  \Jerity\Core\RedirectorException
   */
  public function doRedirect($url = null, $extra_data = null) {
    $notification = array(self::DATA_KEY => serialize($this));
    if (is_null($extra_data)) {
      $extra_data = $notification;
    } else {
      $extra_data = array_merge($extra_data, $notification);
    }
    Redirector::redirectWithState($url, $extra_data);
  }

  /**
   * Gets the current notification object which was set via redirection. This
   * is used to get the notification that should be displayed after we have
   * been redirected.
   *
   * @see Notification::doRedirect()
   *
   * @return  Notification | null
   */
  public static function getCurrent() {
    $extra_data = Redirector::getExtraData();
    if (!is_null($extra_data)) {
      if (isset($extra_data[self::DATA_KEY])) {
        return unserialize($extra_data[self::DATA_KEY]);
      }
    }
    return null;
  }

}

# vim:et:ts=2:sts=2:sw=2:nowrap:ft=php:fdm=marker
