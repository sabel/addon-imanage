<?php

/**
 * Imanage_Object
 *
 * @category   Addon
 * @package    addon.imanage
 * @author     Hamanaka Kazuhiro <hamanaka.kazuhiro@sabel.jp>
 * @author     Ebine Yutaka <ebine.yutaka@sabel.jp>
 * @copyright  2004-2008 Mori Reo <mori.reo@sabel.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
class Imanage_Object extends Sabel_Object
{
  /**
   * @var array
   */
  protected $config = array();
  
  public function __construct(array $config)
  {
    $this->setConfig($config);
  }
  
  public function setConfig(array $config)
  {
    $this->config = $config;
  }
  
  public function getUrl($fileName)
  {
    $d1 = $fileName{0};
    $d2 = $fileName{1};
    
    return get_uri_prefix() . $this->config["image_uri"] . "/{$d1}/{$d2}/{$fileName}";
  }
  
  public function thumbImage($fileName, $height, $width, $alt = "", $class = "", $id = "", $title = "")
  {
    return $this->imgTag($fileName, $height, $width, $alt, $class, $id, $title, true);
  }
  
  public function resizeImage($fileName, $height, $width, $alt = "", $class = "", $id = "", $title = "")
  {
    return $this->imgTag($fileName, $height, $width, $alt, $class, $id, $title, false);
  }
  
  /**
   * @param string $data
   *
   * @return string
   */
  public function save($data)
  {
    if (is_object($data) && method_exists($data, "__toString")) {
      $data = $data->__toString();
    }
    
    if (!is_string($data)) {
      $message = __METHOD__ . "() argument must be an image data.";
      throw new Sabel_Exception_InvalidArgument($message);
    }
    
    $func = $this->config["hash_func"];
    
    $fileName = $func() . "." . Sabel_Util_Image::getType($data);
    file_put_contents($this->getImageDir($fileName) . DS . $fileName, $data);
    
    return $fileName;
  }
  
  /**
   * @param string $fileName file name
   *
   * @return void
   */
  public function delete($fileName)
  {
    $d1 = $fileName{0};
    $d2 = $fileName{1};
    $tdir = $this->config["thumbnail_dir"];
    
    foreach (scandir($tdir) as $item) {
      if ($item{0} === ".") continue;
      
      $path = $tdir . DS . $item . DS . $d1 . DS . $d2 . DS . $fileName;
      if (is_file($path)) unlink($path);
    }
    
    $path = $this->config["image_dir"] . DS . $d1 . DS . $d2 . DS . $fileName;
    if (is_file($path)) unlink($path);
  }
  
  /**
   * @param string $fileName
   * @param int    $size
   *
   * @return void
   */
  public function thumbnail($fileName, $height, $width, $output = true, $fixed = true)
  {
    $sizes = $this->config["thumbnail_sizes"];
    
    if (!empty($sizes)) {
      if (!in_array("{$height}x{$width}", $sizes, true)) {
        return false;
      }
    }
    
    if ($fixed) {
      $filePath = $this->getThumbnailDir($fileName, $height, $width) . DS . $fileName;
    } else {
      $filePath = $this->getResizeDir($fileName, $height, $width) . DS . $fileName;
    }
    
    if (!is_file($filePath)) {
      $data = $this->getImageData($fileName);
      
      if ($data === null) {
        return false;
      } else {
        $image = $this->createImageObject($data);
        $image->resize($height, $width, $fixed)->save($filePath);
      }
    }
    
    if ($output) {
      $data = file_get_contents($filePath);
      
      header("Content-Type: image/" . Sabel_Util_Image::getType($data));
      echo $data;
      
      return true;
    } else {
      return $filePath;
    }
  }
  
  public function getImageDir($fileName)
  {
    $dir = $this->config["image_dir"] . DS . $fileName{0} . DS . $fileName{1};
    
    $fs = new Sabel_Util_FileSystem();
    if (!$fs->isDir($dir)) $fs->mkdir($dir, 0777);
    
    return $dir;
  }
  
  public function getImageData($fileName)
  {
    $filePath = $this->getImageDir($fileName) . DS . $fileName;
    return (is_file($filePath)) ? file_get_contents($filePath) : null;
  }
  
  public function getThumbnailDir($fileName, $height, $width)
  {
    $dir = $this->config["thumbnail_dir"] . DS . $height . "x" . $width;
    $dir = $dir . DS . $fileName{0} . DS . $fileName{1};
    
    $fs = new Sabel_Util_FileSystem();
    if (!$fs->isDir($dir)) $fs->mkdir($dir, 0777);
    
    return $dir;
  }
  
  public function getResizeDir($fileName, $height, $width)
  {
    $dir = $this->config["resize_dir"] . DS . $height . "x" . $width;
    $dir = $dir . DS . $fileName{0} . DS . $fileName{1};
    
    $fs = new Sabel_Util_FileSystem();
    if (!$fs->isDir($dir)) $fs->mkdir($dir, 0777);
    
    return $dir;
  }
  
  protected function imgTag($fileName, $height, $width, $alt = "", $class = "", $id = "", $title = "", $fixed = true)
  {
    if ($height === 0 && $width === 0) {
      $src = $this->getUrl($fileName);
    } else {
      if ($alt !== "" && $title === "") {
        $title = $alt;
      }
      
      if ($fixed) {
        $uri = get_uri_prefix() . $this->config["thumbnail_uri"];
      } else {
        $uri = get_uri_prefix() . $this->config["resize_uri"];
      }
      
      $d1  = $fileName{0};
      $d2  = $fileName{1};
      $src = "{$uri}/{$height}x{$width}/{$d1}/{$d2}/{$fileName}";
    }
    
    $attr = "";
    $vars = array("id", "class", "src", "alt", "title");
    
    foreach ($vars as $var) {
      if ($$var !== "") {
        $attr .= $var . '="' . $$var . '" ';
      }
    }
    
    return "<img {$attr}/>";
  }
  
  protected function createImageObject($data)
  {
    switch (Sabel_Util_Image::getType($data)) {
      case "gif":
        return new Imanage_Formats_Gif($data);
      case "jpeg":
        return new Imanage_Formats_Jpeg($data);
      case "png":
        return new Imanage_Formats_Png($data);
      default:
        $message = __METHOD__ . "() invalid image type.";
        throw new Sabel_Exception_Runtime($message);
    }
  }
}
