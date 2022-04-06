<?php
//check cookie VNRISIntranet
if( !isset( $_COOKIE["VNRISIntranet"]) ) 
{
    header('Content-type: text/html; charset=utf-8');
} else {
 
    header('Location: ../../index.php?welcome='.$_COOKIE["VNRISIntranet"]);
}

?>

<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Login Form</title>
  
  
  <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Open+Sans:600'>
      <link rel="stylesheet" href="../../assets/css/login_user.css">
  <script>
    //   function login()
		// {
		// 	$.ajax({
		// 		url: './index.php?ctrl=user&action=login',
		// 		type: 'POST',
		// 		dataType: 'html',
		// 		cache: false,
		// 		data: {
		// 			'USERNAME': $("#username").val(),
		// 			'PASSWORD': $("#password").val()
		// 			},
		// 		success: function(string){		
		// 			if(string =='OK')
		// 			{
		// 				alert('OK');
		// 				window.location = "/Redirect.php?PAGE=" + URL;
		// 			} else
		// 			{
		// 				console.log(string);
		// 				alert('Mật khẩu không đúng');
		// 			}							
		// 		},
		// 		error: function (){
		// 			alert('ERROR');
		// 		}
		// 	});
		// }
  </script>
</head>
<body>

  <div class="login-wrap">
    <div class="login-html">
        <input id="tab-1" type="radio" name="tab" class="sign-in" checked><label for="tab-1" class="tab">Sign In</label>
        <input id="tab-2" type="radio" name="tab" class="sign-up"><label for="tab-2" class="tab"></label>
        <div class="login-form">
          <form class="sign-in-htm" action="checkLogin.php" method="POST">
            <div class="group">
              <label for="user" class="label">Username</label>
              <input id="username" name="username" type="text" class="input" placeholder="username" required>
            </div>
            <div class="group">
              <label for="pass" class="label">Password</label>
              <input id="password" name="password" type="password" class="input" data-type="password" placeholder="password" required>
            </div>
            <div class="group">
              <input id="check" type="checkbox" class="check" checked>
              <label for="check"><span class="icon"></span> Keep me Signed in</label>
            </div>
            <div class="group">
              <input type="submit" class="button" value="Sign In">
            </div>
            <div class="hr"></div>
            <div class="foot-lnk">
              <a href="#forgot">Forgot Password?</a>
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