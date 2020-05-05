<?php
define('LS_DEFAULT_DIR', 'products');
define('LS_DEFAULT_FILE', 'detail.html');
class DirList
{
  public static $globalData = array();
  private $catt, $handler, $pwd, $rootLen, $path, $data = array();
  public $sort;
  public function __construct($path = null, $handler = null, $cat = null)
  {
    $this->path = trim($path ?? $_GET['path'], '#/.'); //,<>!\\?\'"@$%^&*()
    $this->handler = $handler ?? $_GET['ls'] ?: 'ls.html'; //if empty string has been provided, don't look in $_GET, it will mean that caller wants to cat
    $this->catt = $cat ?? $_GET['cat'] ?: LS_DEFAULT_FILE;
    $this->pwd = "$_SERVER[DOCUMENT_ROOT]/".($this->path ?: LS_DEFAULT_DIR);
    $this->rootLen = strlen($_SERVER['DOCUMENT_ROOT'])+1;
  }
  public function listChildren($cat = null)
  {
    $output = array();
    $cat = $cat ?? $this->catt;
    foreach (scandir($this->pwd) as $subfolder)
      if (substr($subfolder, 0, 1) != '.' && file_exists("$this->pwd/$subfolder/$cat"))
        $output[] = array('path' => "$this->path/$subfolder", 'dir' => $subfolder);
    if (is_callable($this->sort)) $output = call_user_func($this->sort, $output);
    return $output;
  }
  public function getChildren($cat = null)
  {
    foreach ($this->listChildren($cat) as $child)
    {
      yield new DirList($child['path'], $this->handler, $this->catt);
    }
  }
  public function getParent()
  {
    return new DirList(substr(dirname($this->pwd), $this->rootLen), $this->handler, $this->cat);
  }
  private $stringing = false;
  public function __toString()
  {
    $rawFile = $this->cat;
    if ($rawFile && !$this->stringing)
    {
      $this->stringing = true;
      try
      {
        $handlerDir = realpath("$this->pwd/..");
        ob_start();
        do
        {
          if (include "$handlerDir/$this->handler")
            return ob_get_clean();
          $handlerDir = realpath("$handlerDir/..");
        } while (strlen($handlerDir) >= $this->rootLen);
        echo $rawFile;
        return ob_get_clean();
      }
      finally {$this->stringing = false;}
    }
    else return json_encode($this->__get('data'));
  }
  public function __get($key)
  {
    switch ($key)
    {
      case 'pwd': return $this->pwd;
      case 'path': return $this->path;
      case 'cat': return file_get_contents("$this->pwd/$this->catt");
      case 'data': 
        $images = array_map(function($v) {
              return array('path' => $v);
            }, preg_grep('/(.jpe?g|.png|.gif)$/i', scandir($this->pwd)));
        return array_merge(self::$globalData, $this->data, array(
            'parent' => $this->getParent(),
            'list' => $this->listChildren(),
            'images' => array_values($images),
            'path' => $this->path,
            'dir' => substr($this->pwd, strrpos($this->pwd, '/') + 1)));
      default: return $this->data[$key];
    }
  }
  private static $reserved = array('pwd', 'cat', 'data', 'dir', 'list', 'path');
  public function __set($key, $val)
  {
    if (in_array($key, self::$reserved)) throw new OutOfBoundsException("Member is reserved");
    $this->data[$key] = $val;
  }
}

if (__FILE__ == $_SERVER['SCRIPT_FILENAME'])
{
  $ls = new DirList();
  if ($_SERVER['HTTP_ACCEPT'] != 'application/json')
  {
    if ($ls->cat)
    {
      ob_start();
      echo $ls;
    }
    include "$_SERVER[DOCUMENT_ROOT]/master.php";
    ob_end_flush();
  }
  else echo json_encode($ls->data);
}
?>
