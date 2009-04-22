<?php

/**
 * @package JerityTemplate
 * @author Dave Ingram <dave@dmi.me.uk>
 * @copyright Copyright (c) 2009 Dave Ingram
 */
/**
 * Template variable storage class.
 *
 * Example usage:
 * <code>
 * <?php
 * $a = new TemplateVars(array('foo'=>'bar', 'baz'=>'qux', 'spam'=>'eggs'));
 * $a['spam'] = 'beans';
 * $a->setFoo('xuq');
 * $a->handleCall('setBaz', array('rab'));
 * unset($a['spam']); // $a['spam'] is now 'eggs'
 *
 * // these should fail because 'fooBar' was not specified in the constructor
 * try {
 *   $a['fooBar'] = 'rab';
 * } catch (Exception $e) {
 *   print $e."\n\n";
 * }
 * try {
 *   $a->handleCall('setFooBar', array('rab'));
 * } catch (Exception $e) {
 *   print $e."\n\n";
 * }
 * try {
 *   $a->setFooBar('rab');
 * } catch (Exception $e) {
 *   print $e."\n\n";
 * }
 * ?>
 * </code>
 *
 * @package JerityTemplate
 * @author Dave Ingram <dave@dmi.me.uk>
 * @copyright Copyright (c) 2009 Dave Ingram
 */
class TemplateVars implements ArrayAccess {
  /**
   * List of default values for variables.
   *
   * @var array
   */
  protected $defaults;

  /**
   * List of current values for variables.
   *
   * @var array
   */
  protected $vals;


  /**
   * Create an instance of a template variable storage class.
   *
   * Note that if property names are not defined here, then they cannot be
   * specified later.
   *
   * @param array $defaults Default property values.
   */
  function __construct(array $defaults) {
    $this->defaults = $defaults;
    $this->vals = $defaults;
  }

  /**
   * Check whether a certain property exists.
   *
   * @param string $k Template variable name to check
   * @return bool     Whether the variable exists
   *
   * @see ArrayAccess
   */
  function offsetExists($k) {
    return isset($this->defaults[$k]);
  }

  /**
   * Retrieve a property, or throw an exception if it does not exist.
   *
   * @param string $k Template variable name to retrieve
   * @return mixed    Value of the variable
   *
   * @see ArrayAccess
   * @throws OutOfBoundsException
   */
  function offsetGet($k) {
    if (!isset($this->vals[$k])) {
      throw new OutOfBoundsException('"'.$k.'" is not a valid template variable');
    }
    return $this->vals[$k];
  }

  /**
   * Set a property, or throw an exception if it does not exist.
   *
   * @param string $k Template variable name to set
   * @param mixed  $v Value to set
   * @return void
   *
   * @see ArrayAccess
   * @throws OutOfBoundsException
   */
  function offsetSet($k, $v) {
    if (!isset($this->vals[$k])) {
      throw new OutOfBoundsException('"'.$k.'" is not a valid template variable');
    }
    $this->vals[$k] = $v;
  }

  /**
   * Return a property to its default value, or throw an exception if it does
   * not exist.
   *
   * @param string $k Template variable name to reset
   * @return void
   *
   * @see ArrayAccess
   * @throws OutOfBoundsException
   */
  function offsetUnset($k) {
    if (!isset($this->vals[$k])) {
      throw new OutOfBoundsException('"'.$k.'" is not a valid template variable');
    }
    $this->vals[$k] = $this->defaults[$k];
  }

  /**
   * Handle automatic accessor/mutator calls.
   *
   * Throws an exception if the number of arguments are wrong, or if the method name is
   * not recognised, or if the desired property does not exist.
   *
   * Note: It is suggested that other ways of accessing this data are used, as
   * this does introduce some overhead.
   *
   * @param string $f Function call name
   * @param array  $a List of arguments
   * @return void|mixed
   *
   * @throws InvalidArgumentException
   * @throws BadMethodCallException
   * @throws OutOfBoundsException
   */
  function __call($f, array $a) {
    list($t, $v) = array(substr($f, 0, 3), strtolower($f[3]).substr($f, 4));
    switch ($t) {
      case 'get':
        if (count($a)!=1) throw new InvalidArgumentException('Method requires one argument: '.$f.'()');
        return $this->offsetGet($a[0]);
      case 'set':
        if (count($a)!=1) throw new InvalidArgumentException('Method requires one argument: '.$f.'()');
        return $this->offsetSet($v, $a[0]);
      default:
        throw new BadMethodCallException('Unrecognised method: '.$f.'()');
    }
  }
}
