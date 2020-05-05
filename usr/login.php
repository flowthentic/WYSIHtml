<?php
session_start();
function GetPermissions()
{
	if (isset($_SESSION['id']))
	  return 100;
	else return false;
}


if (($_POST["act"] == "Login" || $_POST["act"] == "Logout") && $_SERVER["REQUEST_METHOD"] == "POST")
{
  session_destroy();
  unset($_SESSION);
  if ($_POST['username'] == 'janporuban' && $_POST["password"] == 'mlJeaqtyCX')
  {
	  session_start();
    $_SESSION['id'] = $_POST['username'];
  }
  elseif ($_POST["act"] == "Login")
    $_SESSION['invalid'] = 'notify';
  header("Location: http://$_SERVER[SERVER_NAME]$_POST[redir]");
}
else
{
  ?>
  <form id="auth" action="/auth.php" method="POST" onsubmit="this.querySelector('[name=redir]').value=window.location.pathname+window.location.search;">
	  <ul>
		  <li class="logged_in"><input type="submit" name="act" value="Logout"></li>

		  <li class="logged_out"><input type="text" name="username" placeholder="Username"></li>
		  <li class="logged_out"><input type="password" name="password" placeholder="Password"></li>
		  <li class="logged_out login_invalid"><small>Invalid username or password</small></li>
		  <li class="logged_out"><input type="submit" name="act" value="Login"></li>
	  </ul>
	  <input type="hidden" name="redir" value="" />
  </form>
  <script>
  <?php
  if (isset($_SESSION['id']))
	  echo 'w3.hide(".logged_out");';
  elseif (isset($_SESSION['invalid']))
    echo 'w3.hide(".logged_in");';
  else echo 'w3.hide(".login_invalid, .logged_in");';
  echo '</script>';
}
?>
