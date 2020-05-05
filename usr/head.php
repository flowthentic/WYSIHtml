<?php
define('TITLE', 'Marstech');
define('LANGUAGES', 'sk hu en ru'); #first is default
if(isset($_COOKIE['PHPSESSID'])) session_start();

//return LANGUAGE at opening HTML tag
if (!isset($lang))
{
  if (isset($_SESSION['lang']))
    echo $lang = $_SESSION['lang'];
  elseif (in_array($_GET['lang'], $siteLanguages))
    echo $lang = $_GET['lang'];
  else
  {
    $acceptedLanguages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']); #split languages into array
    natsort($acceptedLanguages); #order based on q= priority, highest will be last
    array_unshift($acceptedLanguages, $siteLanguages[0]); #make page default as least priority, in case no matching language is found
    for ($li = count($acceptedLanguages) - 1; $li >= 0; $li--)  #go through all the elements from the beginning
    {
      $acceptedLanguages[$li] = substr(ltrim($acceptedLanguages[$li]), 0, 2); #strip everything unnecessary
      if (in_array($acceptedLanguages[$li], $siteLanguages)) break;
    }
    echo $lang = $acceptedLanguages[$li];
  }
  unset($siteLanguages[array_search($lang, $siteLanguages)]);
  $lang = ".$lang";
}

//handle POSTs
elseif (in_array($_POST['lang'], $siteLanguages = explode(' ', LANGUAGES)))
  $_SESSION['lang'] = $_POST['lang'];
elseif (isset($_POST['cookies']))
  $_SESSION['cookies'] = true;
  
//return HEAD  
else
{
  if (isset($contents))
  {
    require_once "$_SERVER[DOCUMENT_ROOT]/opt/simple_html_dom.php";
    $DOMcontents = str_get_html($contents);
    $contentsTitle = $DOMcontents->find("h1$lang, h2$lang, h3$lang", 0)->plaintext.' - ';
    $meta = $DOMcontents->find("p$lang, span$lang", 0)->plaintext;
  }
  $contentsTitle = "<title>$contentsTitle".TITLE.'</title>';

  echo <<< EoHEAD
<title>$contentsTitle</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="description" content="">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="$meta">
$hreflang
<style>.lang:not($lang){ display: none; }</style>
EoHEAD;
  foreach ($siteLanguages as $otherlang)
    echo "<link rel='alternate' href='$_SERVER[SCRIPT_NAME]?path=".urlencode($_GET['path'])."&lang=$otherlang' hreflang='$otherlang' />";
  if (isset($owa)) $owa->placeHelperPageTags();
}
?>
