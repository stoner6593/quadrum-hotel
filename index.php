<?php
session_start();

$hotel = array(
    "hotel_name"  => "quadrum",
    "hotel_color" => "#2F3291",

    "logo_path"   => "hoteles/img/quadrum.png",
    "logo_width"  => "300px",
    "logo_height" => "80px",
    "logo_margin" => "20px auto",

    "db_host" => "localhost:3306",
    "db_user" => "root",
    "db_pass" => "stoner93",
    "db_name" => "quadrum"
  );

$_SESSION["hotel"] = $hotel;

$_SESSION['xyzidusuario'] = "";
$_SESSION['xyzusuario'] = "";
$_SESSION['xyzcodigo'] = "";
$_SESSION['userlog'] = "";
$_SESSION['estadomenu'] = 0;
$_SESSION['idturno'] = 0;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">

<title>Administrador</title>
<style>
	@import url(https://fonts.googleapis.com/css?family=Montserrat:400,700);
	body {
		background: url('') no-repeat fixed center center; /* http://i.imgur.com/Eor57Ae.jpg*/
		background-size: cover;
		font-family: Montserrat;
	}

	.logo {
		width: <?php echo $_SESSION["hotel"]["logo_width"] ?>;
		height: <?php echo $_SESSION["hotel"]["logo_height"] ?>;
		background: url('<?php echo $_SESSION["hotel"]["logo_path"] ?>') no-repeat;
		margin: <?php echo $_SESSION["hotel"]["logo_margin"] ?>;
	}

	.login-block {
		width: 320px;
		padding: 20px;
		background:#ECECEC;
		border-radius: 5px;
		border-top: 5px solid <?php echo $_SESSION["hotel"]["hotel_color"] ?>;
		margin: 0 auto;
	}

	.login-block h1 {
		text-align: center;
		color: <?php echo $_SESSION["hotel"]["hotel_color"] ?>;
		font-size: 16px;
		text-transform: uppercase;
		margin-top: 0;
		margin-bottom: 20px;
	}

	.login-block input {
		width: 100%;
		height: 42px;
		box-sizing: border-box;
		border-radius: 5px;
		border: 1px solid #ccc;
		margin-bottom: 20px;
		font-size: 14px;
		font-family: Montserrat;
		padding: 0 20px 0 50px;
		outline: none;
	}

	.login-block input#username {
		background: #fff url('imagenesv/u0XmBmv.png') 20px top no-repeat;
		background-size: 16px 80px;
	}

	.login-block input#username:focus {
		background: #fff url('imagenesv/u0XmBmv.png') 20px bottom no-repeat;
		background-size: 16px 80px;
	}

	.login-block input#password {
		background: #fff url('imagenesv/Qf83FTt.png') 20px top no-repeat;
		background-size: 16px 80px;
	}

	.login-block input#password:focus {
		background: #fff url('imagenesv/Qf83FTt.png') 20px bottom no-repeat;
		background-size: 16px 80px;
	}

	.login-block input:active, .login-block input:focus {
		border: 1px solid #ff656c;
	}

	.login-block button {
		width: 100%;
		height: 40px;
		background: <?php echo $_SESSION["hotel"]["hotel_color"] ?>;
		box-sizing: border-box;
		border-radius: 5px;
		border: 1px solid <?php echo $_SESSION["hotel"]["hotel_color"] ?>;
		color: #fff;
		font-weight: bold;
		text-transform: uppercase;
		font-size: 14px;
		font-family: Montserrat;
		outline: none;
		cursor: pointer;
	}

	.login-block button:hover {
		background: <?php echo $_SESSION["hotel"]["hotel_color"] ?>;
	}

	.txterror{
		font-family: Arial, Helvetica, sans-serif;
		font-size: 11px;
		color:#E1583E;
		text-align:center;
		padding:10px;
	}

</style>
</head>

<body>

<div class="logo"></div>

<div class="login-block">
    <h1>Acceso Administrador</h1>
    <form action="prg_login.php" method="post" autocomplete="off">
        <input type="text" value="" placeholder="Usuario" id="username" name="username"  />
        <input type="password" value="" placeholder="Password" id="password" name="password" />
        <button>Entrar</button>
    </form>
</div>
<div class="txterror">
	<?php echo $_SESSION['msgerror']; $_SESSION['msgerror']="";?>
</div>

</body>

</html>
