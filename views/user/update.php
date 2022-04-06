<?php
  $username = isset($_GET['username']) ? $_GET['username'] : '';

  if (!empty($username ) ) {
    $title = 'Update User';
  } else {
    $title = 'Create User';
  }
  $title = !empty($username) ? 'Update User' : 'Create User';
  $readonly = !empty($username) ? 'readonly' : '';

?>

<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title><?php echo $title; ?></title>
  
  
  <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Open+Sans:600'>
    <link rel="stylesheet" href="../../assets/css/login_user.css">
    <script src="./assets/JS/jquery-1.10.1.min.js"></script>
</head>
<body>

  <div class="login-wrap">
    <div class="login-html">
        <input id="tab-1" type="radio" name="tab" class="sign-in" checked><label for="tab-1" class="tab"><?php echo $title; ?></label>
        <input id="tab-2" type="radio" name="tab" class="sign-up"><label for="tab-2" class="tab"></label>
        <div class="login-form">
          <form class="sign-in-htm" action="../../models/updateUser_conn.php" method="POST">
            <div class="group">
              <label for="user" class="label">Username</label>
              <input id="username" name="username" type="text" class="input" placeholder="username"  value="<?php echo $username; ?>" <?php echo $readonly; ?> required>
            </div>
            <div class="group">
              <label for="pass" class="label">Password</label>
              <input id="password" name="password" type="password" class="input" data-type="password" placeholder="password" required>
            </div>
            <div class="group">
              <input type="submit" class="button" value="Save">
            </div>
            <div class="hr"></div>
            <div class="foot-lnk">
              <a href="#">Vui lòng nhập đầy đủ thông tin</a>
            </div>
          </form>

                  <!-- <form class="sign-up-htm" action="./api/user/signup.php" method="POST">
                    <div class="group">
                      <label for="user" class="label">Username</label>
                      <input id="username" name="username" type="text" class="input">
                    </div>
                    <div class="group">
                      <label for="pass" class="label">Password</label>
                      <input id="password" name="password" type="password" class="input" data-type="password">
                    </div>
                    <div class="group">
                      <label for="pass" class="label">Confirm Password</label>
                      <input id="pass" type="password" class="input" data-type="password">
                    </div>
                    <div class="group">
                      <input type="submit" class="button" value="Sign Up">
                    </div>
                    <div class="hr"></div>
                    <div class="foot-lnk">
                      <label for="tab-1">Already Member?</a>
                    </div>
                  </form> -->
    </div>
  </div>
  
</body>
</html>