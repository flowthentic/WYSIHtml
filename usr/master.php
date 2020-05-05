<?php
$_SERVER['DOCUMENT_ROOT'] = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
if ($_SERVER['HTTP_ACCEPT'] == 'application/subpage') exit();
require_once "$_SERVER[DOCUMENT_ROOT]/lib/ls.php";
$info = new DirList('info');
$projekty = new DirList('projekt');
$projekty->sort = function($in) {
  usort($in, function($a, $b){
    return (int)$b['dir'] - (int)$a['dir'];
  });
  return $in;
};
if (!$contents = ob_get_clean())
  $contents = $projekty->getChildren()->current();
?>
<!DOCTYPE html>
<html lang="<?php include 'lib/head.php' ?>">
<head>
  <?php include 'lib/head.php' ?>
	<link rel="icon" type="image/png" href="favicon-128.png" sizes="128x128">
	<link rel="stylesheet" type="text/css" href=".css">
	<script src=".js"></script>
</head>
<body>
  <article><?php
    echo $contents;
  ?></article>
  
  <footer>
    <?php if ($_SESSION['cookies'] != true) { ?>
    <form method="POST" action="/lib/head.php" onsubmit="this.style.display='none'; return false;">
      <label>This website is using cookies</label>
      <input type="submit" name="cookies" value="I undestand" onclick="ajax.submit(this)">
    </form>
    <?php } ?>
  </footer>
</body>
</html>
