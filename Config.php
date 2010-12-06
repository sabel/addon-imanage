<?php

/**
 * Imanage_Config
 *
 * @category   Addon
 * @package    addon.imanage
 * @author     Ebine Yutaka <ebine.yutaka@sabel.jp>
 * @copyright  2004-2008 Mori Reo <mori.reo@sabel.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
class Imanage_Config implements Sabel_Config
{
  public function configure()
  {
    $images_dirname = "images";
    
    $pub_dir = RUN_BASE . DS . "public";
    
    $image_dir     = DS . $images_dirname  . DS . "data";
    $thumbnail_dir = DS . $images_dirname  . DS . "thumbnail";
    $resize_dir    = DS . $images_dirname  . DS . "resize";
    
    return array(
      "base_dir"        => $pub_dir . DS . $images_dirname,
      "image_dir"       => $pub_dir . $image_dir,
      "thumbnail_dir"   => $pub_dir . $thumbnail_dir,
      "resize_dir"      => $pub_dir . $resize_dir,
      "image_uri"       => str_replace(DS, "/", $image_dir),
      "thumbnail_uri"   => str_replace(DS, "/", $thumbnail_dir),
      "resize_uri"      => str_replace(DS, "/", $resize_dir),
      "hash_func"       => "md5hash",
      "thumbnail_sizes" => array(/*"60x60", "90x90", "120x120", ... */),
    );
  }
}
