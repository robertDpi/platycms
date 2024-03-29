<?php
require_once Config::$app_folder.'/parsers/Twig/ExtensionInterface.php';
require_once Config::$app_folder.'/parsers/Twig/Extension.php';

class Stacey_Twig_Extension extends Twig_Extension {

  var $sortby_value;

  public function getName() {
    return 'Stacey';
  }

  public function getFilters() {
    # custom twig filters
    return array(
      'mime' => new Twig_Filter_Method($this, 'mime'),
      'absolute' => new Twig_Filter_Method($this, 'absolute'),
      'context' => new Twig_Filter_Method($this, 'context')
    );
  }

  public function getFunctions() {
    # custom Twig functions
    return array(
      'sortbydate' => new Twig_Function_Method($this, 'sortbydate'),
      'sortby' => new Twig_Function_Method($this, 'sortby'),
      'debug' => new Twig_Function_Method($this, 'var_dumper'),
      'pebug' => new Twig_Function_Method($this, 'var_dumper_pre'),
      'get' => new Twig_Filter_Method($this, 'get'),
      'slice' => new Twig_Filter_Method($this, 'slice'),
      'resize_path' => new Twig_Filter_Method($this, 'resize_path'),
    );
  }

  #
  #   dump out our var for easy debugging
  #
  public function var_dumper($input) {
    var_dump( $input );
  }
  
  #
  #   dump out our var for easy debugging ++ Now with Extra Pre's
  #
  public function var_dumper_pre($input) {
    echo "<pre>";
    print_r( $input );
    echo "</pre>";
  }

  #
  #   manually change page context
  #
  function get($url, $current_url = '') {
    # strip leading & trailing slashes from $url
    $url = preg_replace(array('/^\//', '/\/$/'), '', $url);
    # if the current url is passed, then we use it to build up a relative context
    $url = $current_url.$url;
    # strip leading '../'s from the url if any exists
    $url = preg_replace('/^((\.+)*\/)*/', '', $url);
    # turn route into file path
    $file_path = Helpers::url_to_file_path($url);
    # check for children of the index page
    if (!$file_path) return $file_path = Helpers::url_to_file_path('index/'.$url);
    # create & return the new page object
    return AssetFactory::get($file_path);
  }

  #
  # shortcut to generate the image resize path from a full image path
  #
  function resize_path($img_path, $max_width = '100', $max_height = '100', $ratio = 0, $quality = 100) {

    $root_path = preg_replace('/content\/.*/', '', $img_path);
    $clean_path = preg_replace('/^(\.+\/)*content/', '', $img_path);

    if(!file_exists(Config::$root_folder.'.htaccess')) {
      return $root_path.'app/parsers/slir/index.php?w='.$max_width.'&h='.$max_height.'&c='.$ratio.'&q='.$quality.'&i='.$clean_path;
    } else {
      return $root_path.'render/w'.$max_width.'-h'.$max_height.'-c'.$ratio.'-q'.$quality.$clean_path;
    }
  }

  #
  # allow offsetting and limiting arrays
  #
  function slice($array, $start, $end) {
    return array_slice($array, $start, $end);
  }

  #
  #   sort by date-based subvalue
  #
  public function custom_date_sort($a, $b) {
    return strtotime($a[$this->sortby_value]) > strtotime($b[$this->sortby_value]);
  }

  function sortbydate($object, $value) {
    $this->sortby_value = $value;
    $sorted = array();
    # expand sub variables if required
    if (is_array($object)) {
      foreach ($object as $key) {
        if (is_string($key)) $sorted[] =& AssetFactory::get($key);
      }
    }
    # sort the array
    uasort($sorted, array($this, 'custom_date_sort'));
    return $sorted;
  }

  #
  #   sort by subvalue using natural string comparison
  #
  public function custom_str_sort($a, $b) {
    return strnatcmp($a[$this->sortby_value], $b[$this->sortby_value]);
  }

  function sortby($object, $value) {
    $this->sortby_value = $value;
    $sorted = array();
    # expand sub variables if required
    if (is_array($object)) {
      foreach ($object as $key) {
        if (is_string($key)) $sorted[] =& AssetFactory::get($key);
      }
    }
    # sort the array
    uasort($sorted, array($this, 'custom_str_sort'));
    return $sorted;
  }
  
  #
  #   determine mime type by filename
  #
  function mime($filename) {
    # media mime types
    $mime_types = array(
      #images
      'jpg' => 'image/jpeg',
      'jpeg' => 'image/jpeg',
      'png' => 'image/png',
      'gif' => 'image/gif',
      # audio
      'ogg' => 'audio/ogg',
      'oga' => 'audio/ogg',
      'm4a' => 'audio/mp4',
      # video
      'ogv' => 'video/ogg',
      'mp4' => 'video/mp4',
      'm4v' => 'video/mp4',
      'webm' => 'video/webm'
    );
    # get file extension
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if(array_key_exists($ext, $mime_types)){
      return $mime_types[$ext];
    }
  }

  #
  #   transforms relative path to absolute
  #
  function absolute($relative_path) {
    $server_name = (($_SERVER['HTTPS'] ? 'https://' : 'http://')).$_SERVER['HTTP_HOST'];
    return $server_name.str_replace('/index.php', strstr($relative_path, '/content'), $_SERVER['SCRIPT_NAME']);
  }

}

?>
