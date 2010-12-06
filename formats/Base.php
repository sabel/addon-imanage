<?php

/**
 * Imanage_Formats_Base
 *
 * @abstract
 * @category   Addon
 * @package    addon.imanage
 * @author     Hamanaka Kazuhiro <hamanaka.kazuhiro@sabel.jp>
 * @copyright  2004-2008 Mori Reo <mori.reo@sabel.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
abstract class Imanage_Formats_Base extends Sabel_Object
{
  protected $imagick = null;
  
  public function __construct($resource)
  {
    $this->imagick = new Imagick();
    $this->imagick->readImageBlob($resource);
  }
  
  public function resize($height, $width, $fixed = true)
  {
    $imagick = $this->imagick;
    
    if ((int)$height === 0 || (int)$width === 0) {
      list ($height, $width) = $this->calcSize($height, $width);
    }
    
    $imagick->thumbnailImage($width, $height, true);
    
    if ($fixed) {
      list($x, $y) = $this->getOffset($height, $width);
      
      $buf = new Imagick();
      $buf->newImage($width, $height, new ImagickPixel("#fff"), $this->type);
      $buf->compositeImage($imagick, Imagick::COMPOSITE_OVER, $x, $y);
      
      $this->imagick = $buf;
    }
    
    return $this;
  }
  
  public function save($path)
  {
    $this->imagick->writeImages($path, true);
    
    return $this;
  }
  
  public function setFormat($toFormat)
  {
    $this->imagick->setImageFormat($toFormat);
    
    return $this;
  }
  
  protected function calcSize($_height, $_width)
  {
    $_height = (int)$_height;
    $_width  = (int)$_width;
    $height  = $this->imagick->getImageHeight();
    $width   = $this->imagick->getImageWidth();
    
    if ($_height === 0) {
      if ($width <= $_width) {
        $_height = $height;
      } else {
        $_height = (int)round(($_width * $height) / $width);
      }
    } elseif ($_width === 0) {
      if ($height <= $_height) {
        $_width = $width;
      } else {
        $_width = (int)round(($_height * $width) / $height);
      }
    }
    
    return array($_height, $_width);
  }
  
  protected function getOffset($size)
  {
    return array(
      ($size - $this->imagick->getImageWidth())  / 2,
      ($size - $this->imagick->getImageHeight()) / 2,
    );
  }
}
