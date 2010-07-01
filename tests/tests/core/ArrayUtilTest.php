<?php
##############################################################################
# Copyright © 2010 David Ingram, Nicholas Pope
#
# This work is licenced under the Creative Commons BSD License License. To
# view a copy of this licence, visit http://creativecommons.org/licenses/BSD/
# or send a letter to Creative Commons, 171 Second Street, Suite 300,
# San Francisco, California 94105, USA.
##############################################################################

require_once(dirname(dirname(dirname(__FILE__))).'/Configure.php');

class ArrayUtilTest extends PHPUnit_Framework_TestCase {

  /**
   * @dataProvider  flattenProvider
   * @covers        ArrayUtil::flatten()
   */
  public function testFlatten($input, $expected) {
    $this->assertSame($expected, ArrayUtil::flatten($input));
  }

  public static function flattenProvider() {
    $multi  = array();
    $result = array();
    $final = array();

    $count = -1;

    $multi[++$count] = array(0, 1, 2, 3, 4, 5);
    $result[ $count] = array(0, 1, 2, 3, 4, 5);

    $multi[++$count] = array(5, 4, 3, 2, 1, 0);
    $result[ $count] = array(5, 4, 3, 2, 1, 0);

    $multi[++$count] = array('a'=>0, 'b'=>1, 'c'=>2, 'd'=>3, 'e'=>4, 'f'=>5);
    $result[ $count] = array(0, 1, 2, 3, 4, 5);

    $multi[++$count] = array(array(0), 1, 2, 3, 4, 5);
    $result[ $count] = array(0, 1, 2, 3, 4, 5);

    $multi[++$count] = array(array(0), array(1), array(2), array(3), array(4), array(5));
    $result[ $count] = array(0, 1, 2, 3, 4, 5);

    $multi[++$count] = array(array(array(0)), array(1, array(2, 3, array(4))), 5);
    $result[ $count] = array(0, 1, 2, 3, 4, 5);

    $multi[++$count] = array(array(array(0)), array('z'=>1, 'a'=>array(2, 3, array(4))), 5);
    $result[ $count] = array(0, 1, 2, 3, 4, 5);

    $multi[++$count] = array(array(array(0)), array('z'=>1, 'a'=>array(2, 'f'=>3, array(4))), 5);
    $result[ $count] = array(0, 1, 2, 3, 4, 5);

    for ($i=0; $i<=$count; ++$i) {
      $final[] = array($multi[$i], $result[$i]);
    }

    return $final;
  }

  /**
   * @dataProvider  collapseKeysProvider
   * @covers        ArrayUtil::collapseKeys()
   */
  public function testCollapseKeys($input, $expected) {
    $this->assertSame($expected, ArrayUtil::collapseKeys($input));
  }

  public static function collapseKeysProvider() {
    $input  = array();
    $output = array();
    $final  = array();

    $count = -1;

    $input[++$count] = array(0, 1, 2, 3, 4, 5);
    $output[ $count] = array(0, 1, 2, 3, 4, 5);

    $input[++$count] = array(5, 4, 3, 2, 1, 0);
    $output[ $count] = array(5, 4, 3, 2, 1, 0);

    $input[++$count] = array('a'=>0, 'b'=>1, 'c'=>2, 'd'=>3, 'e'=>4, 'f'=>5);
    $output[ $count] = array('a'=>0, 'b'=>1, 'c'=>2, 'd'=>3, 'e'=>4, 'f'=>5);

    $input[++$count] = array('a'=>0, 'b'=>1, 'c'=>array('d'=>3, 'e'=>4, 'f'=>5));
    $output[ $count] = array('a'=>0, 'b'=>1, 'c[d]'=>3, 'c[e]'=>4, 'c[f]'=>5);

    $input[++$count] = array('a'=>0, 'b'=>1, 'c'=>array('d'=>array('e'=>4, 'f'=>5)));
    $output[ $count] = array('a'=>0, 'b'=>1, 'c[d][e]'=>4, 'c[d][f]'=>5);

    for ($i=0; $i<=$count; ++$i) {
      $final[] = array($input[$i], $output[$i]);
    }

    return $final;
  }

}
