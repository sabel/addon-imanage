<?php

/**
 * Imanage_Addon
 *
 * @category   Addon
 * @package    addon.imanage
 * @author     Ebine Yutaka <ebine.yutaka@sabel.jp>
 * @copyright  2004-2008 Mori Reo <mori.reo@sabel.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
class Imanage_Addon extends Sabel_Object implements Sabel_Addon
{
  const VERSION = 1.0;
  
  public function execute(Sabel_Bus $bus)
  {
    $bus->insertProcessor("router", new Imanage_Processor("imanage"), "after");
  }
}
