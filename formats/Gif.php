<?php

/**
 * Imanage_Formats_Gif
 *
 * @abstract
 * @category   Addon
 * @package    addon.imanage
 * @author     Hamanaka Kazuhiro <hamanaka.kazuhiro@sabel.jp>
 * @copyright  2004-2008 Mori Reo <mori.reo@sabel.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
class Imanage_Formats_Gif extends Imanage_Formats_Base
{
  protected $type = "gif";
  
  public function resize($height, $width, $fixed = true)
  {
    $imagick = new Imagick();
    
    if ((int)$height === 0 || (int)$width === 0) {
      list ($height, $width) = $this->calcSize($height, $width);
    }
    
    $this->imagick = $this->imagick->coalesceImages();
    $this->imagick->scaleImage($width, $height, true);
    
    $imagick->newImage($width, $height, new ImagickPixel("#fff"), $this->type);
    
    list($x, $y) = $this->getOffset($height, $width);
    
    foreach ($this->imagick as $frame) {
      $delay = $frame->getImageDelay();
      $clone = $frame->getImage();
      $clone->scaleImage($width, $height, true);
      
      if ($fixed) {
        $frame->setImage($imagick);
        $frame->setImageDelay($delay);
        $frame->compositeImage($clone, Imagick::COMPOSITE_DEFAULT, $x, $y);
      } else {
        $frame->setImage($clone);
        $frame->setImageDelay($delay);
      }
    }
    
    return $this;
  }
}
