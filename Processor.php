<?php

/**
 * Imanage_Processor
 *
 * @category   Addon
 * @package    addon.imanage
 * @author     Ebine Yutaka <ebine.yutaka@sabel.jp>
 * @copyright  2004-2008 Mori Reo <mori.reo@sabel.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
class Imanage_Processor extends Sabel_Bus_Processor
{
  protected $afterEvents = array("controller" => "setImanage");
  
  /**
   * @var Imanage_Object
   */
  protected $imanage = null;
  
  public function execute(Sabel_Bus $bus)
  {
    $iConfig = new Imanage_Config();
    $config = $iConfig->configure();
    
    $this->imanage = new Imanage_Object($config);
    
    if (($request = $bus->get("request")) === null) {
      return;
    }
    
    if (!$request->isGet()) {
      return;
    }
    
    $parts = array();
    $fixed = true;
    $uri   = "/" . $request->getUri();
    
    if (strpos($uri, $config["thumbnail_uri"] . "/") === 0) {
      $parts = explode("/", substr($uri, strlen($config["thumbnail_uri"]) + 1));
    } elseif (strpos($uri, $config["resize_uri"] . "/") === 0) {
      $parts = explode("/", substr($uri, strlen($config["resize_uri"]) + 1));
      $fixed = false;
    }
    
    if (!empty($parts)) {
      @list ($size, , , $fileName) = $parts;
      @list ($height, $width) = explode("x", $size);
      
      if (!is_natural_number($height) || !is_natural_number($width)) {
        $bus->get("response")->getStatus()->setCode(Sabel_Response::BAD_REQUEST);
      } elseif ($this->imanage->thumbnail($fileName, $height, $width, true, $fixed)) {
        exit;
      } else {
        $bus->get("response")->getStatus()->setCode(Sabel_Response::NOT_FOUND);
      }
    }
  }
  
  public function setImanage($bus)
  {
    if ($controller = $bus->get("controller")) {
      $controller->setAttribute("imanage", $this->imanage);
    }
  }
}
