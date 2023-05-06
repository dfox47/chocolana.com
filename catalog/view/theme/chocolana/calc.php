<?php // Define constants
define( "RECIPIENT_NAME",       "chocolana.com" );
define( "RECIPIENT_EMAIL",      "dfox@foxartbox.com" ); // where to send email
define( "EMAIL_SUBJECT",        "[chocolana.com]" );



$success = false;



//$sender_name                = isset( $_POST['sender_name'] ) ? preg_replace( "/[^\.\-\' a-zA-Z0-9]/", "", $_POST['sender_name'] ) : "";
$calc__layout_1__box_1      = $_POST['calc__layout_1__box_1'];
$calc__layout_1__box_2      = $_POST['calc__layout_1__box_2'];
$calc__layout_1__box_3      = $_POST['calc__layout_1__box_3'];
$calc__layout_1__box_4      = $_POST['calc__layout_1__box_4'];
$calc__layout_1__box_5      = $_POST['calc__layout_1__box_5'];
$calc__layout_1__box_6      = $_POST['calc__layout_1__box_6'];
$calc__layout_1__box_7      = $_POST['calc__layout_1__box_7'];
$calc__layout_1__box_8      = $_POST['calc__layout_1__box_8'];
$calc__layout_1__box_9      = $_POST['calc__layout_1__box_9'];
$calc__layout_1__box_10     = $_POST['calc__layout_1__box_10'];
$calc__layout_1__box_11     = $_POST['calc__layout_1__box_11'];
$calc__layout_1__box_12     = $_POST['calc__layout_1__box_12'];
$calc__layout_1__box_13     = $_POST['calc__layout_1__box_13'];
$calc__layout_1__box_14     = $_POST['calc__layout_1__box_14'];
$calc__layout_1__box_15     = $_POST['calc__layout_1__box_15'];
$calc__layout_1__box_16     = $_POST['calc__layout_1__box_16'];
$calc__layout_1__box_17     = $_POST['calc__layout_1__box_17'];
$calc__layout_1__box_18     = $_POST['calc__layout_1__box_18'];
$calc__layout_1__box_19     = $_POST['calc__layout_1__box_19'];
$calc__layout_1__box_20     = $_POST['calc__layout_1__box_20'];
$calc__layout_1__box_21     = $_POST['calc__layout_1__box_21'];
$calc__layout_1__box_22     = $_POST['calc__layout_1__box_22'];
$calc__layout_1__box_23     = $_POST['calc__layout_1__box_23'];
$calc__layout_1__box_24     = $_POST['calc__layout_1__box_24'];
$calc__layout_1__box_25     = $_POST['calc__layout_1__box_25'];

$calc__layout_2__box_1      = $_POST['calc__layout_2__box_1'];
$calc__layout_2__box_2      = $_POST['calc__layout_2__box_2'];
$calc__layout_2__box_3      = $_POST['calc__layout_2__box_3'];
$calc__layout_2__box_4      = $_POST['calc__layout_2__box_4'];
$calc__layout_2__box_5      = $_POST['calc__layout_2__box_5'];
$calc__layout_2__box_6      = $_POST['calc__layout_2__box_6'];
$calc__layout_2__box_7      = $_POST['calc__layout_2__box_7'];
$calc__layout_2__box_8      = $_POST['calc__layout_2__box_8'];
$calc__layout_2__box_9      = $_POST['calc__layout_2__box_9'];
$calc__layout_2__box_10     = $_POST['calc__layout_2__box_10'];
$calc__layout_2__box_11     = $_POST['calc__layout_2__box_11'];
$calc__layout_2__box_12     = $_POST['calc__layout_2__box_12'];
$calc__layout_2__box_13     = $_POST['calc__layout_2__box_13'];
$calc__layout_2__box_14     = $_POST['calc__layout_2__box_14'];

$calc__box_type             = $_POST['calc__box_type'];



$img__src                   = "https://chocolana.com/image/calc__email";
$img__styles                = "style='height: 50px; width: 50px;'";
$td__styles                 = "style='border: 1px solid #999; font-size: 50px; height: 50px; text-align: center; text-transform: uppercase; width: 50px;'";



// 5x5
if ( $calc__box_type == 1 ) {
	$message = "
		<html>
			<body>
				<p>Тип коробки: <strong>5x5</strong></p>

<tabel style='border-collapse: collapse;' width='100%'>
	<tr>
		<td $td__styles><img $img__styles src='$img__src/$calc__layout_1__box_1.jpg' alt='$calc__layout_1__box_1'></td>
		<td $td__styles><img $img__styles src='$img__src/$calc__layout_1__box_2.jpg' alt='$calc__layout_1__box_2'></td>
		<td $td__styles><img $img__styles src='$img__src/$calc__layout_1__box_3.jpg' alt='$calc__layout_1__box_3'></td>
		<td $td__styles><img $img__styles src='$img__src/$calc__layout_1__box_4.jpg' alt='$calc__layout_1__box_4'></td>
		<td $td__styles><img $img__styles src='$img__src/$calc__layout_1__box_5.jpg' alt='$calc__layout_1__box_5'></td>
	</tr>

	<tr>
		<td $td__styles><img $img__styles src='$img__src/$calc__layout_1__box_6.jpg' alt='$calc__layout_1__box_6'></td>
		<td $td__styles><img $img__styles src='$img__src/$calc__layout_1__box_7.jpg' alt='$calc__layout_1__box_7'></td>
		<td $td__styles><img $img__styles src='$img__src/$calc__layout_1__box_8.jpg' alt='$calc__layout_1__box_8'></td>
		<td $td__styles><img $img__styles src='$img__src/$calc__layout_1__box_9.jpg' alt='$calc__layout_1__box_9'></td>
		<td $td__styles><img $img__styles src='$img__src/$calc__layout_1__box_10.jpg' alt='$calc__layout_1__box_10'></td>
	</tr>

	<tr>
		<td $td__styles><img $img__styles src='$img__src/$calc__layout_1__box_11.jpg' alt='$calc__layout_1__box_11'></td>
		<td $td__styles><img $img__styles src='$img__src/$calc__layout_1__box_12.jpg' alt='$calc__layout_1__box_12'></td>
		<td $td__styles><img $img__styles src='$img__src/$calc__layout_1__box_13.jpg' alt='$calc__layout_1__box_13'></td>
		<td $td__styles><img $img__styles src='$img__src/$calc__layout_1__box_14.jpg' alt='$calc__layout_1__box_14'></td>
		<td $td__styles><img $img__styles src='$img__src/$calc__layout_1__box_15.jpg' alt='$calc__layout_1__box_15'></td>
	</tr>

	<tr>
		<td $td__styles><img $img__styles src='$img__src/$calc__layout_1__box_16.jpg' alt='$calc__layout_1__box_16'></td>
		<td $td__styles><img $img__styles src='$img__src/$calc__layout_1__box_17.jpg' alt='$calc__layout_1__box_17'></td>
		<td $td__styles><img $img__styles src='$img__src/$calc__layout_1__box_18.jpg' alt='$calc__layout_1__box_18'></td>
		<td $td__styles><img $img__styles src='$img__src/$calc__layout_1__box_19.jpg' alt='$calc__layout_1__box_19'></td>
		<td $td__styles><img $img__styles src='$img__src/$calc__layout_1__box_20.jpg' alt='$calc__layout_1__box_20'></td>
	</tr>

	<tr>
		<td $td__styles><img $img__styles src='$img__src/$calc__layout_1__box_21.jpg' alt='$calc__layout_1__box_21'></td>
		<td $td__styles><img $img__styles src='$img__src/$calc__layout_1__box_22.jpg' alt='$calc__layout_1__box_22'></td>
		<td $td__styles><img $img__styles src='$img__src/$calc__layout_1__box_23.jpg' alt='$calc__layout_1__box_23'></td>
		<td $td__styles><img $img__styles src='$img__src/$calc__layout_1__box_24.jpg' alt='$calc__layout_1__box_24'></td>
		<td $td__styles><img $img__styles src='$img__src/$calc__layout_1__box_25.jpg' alt='$calc__layout_1__box_25'></td>
	</tr>
</tabel>
			</body>
		</html>
	";
}
// 7x2
else if ( $calc__box_type == 2 ) {
	$message = "
<html>
	<body>
		<p>Тип коробки: <strong>7x2</strong></p>

		<tabel style='border-collapse: collapse;' width='100%'>
<tr>
	<td $td__styles><img $img__styles src='$img__src/$calc__layout_2__box_1.jpg' alt='$calc__layout_2__box_1'></td>
	<td $td__styles><img $img__styles src='$img__src/$calc__layout_2__box_2.jpg' alt='$calc__layout_2__box_2'></td>
	<td $td__styles><img $img__styles src='$img__src/$calc__layout_2__box_3.jpg' alt='$calc__layout_2__box_3'></td>
	<td $td__styles><img $img__styles src='$img__src/$calc__layout_2__box_4.jpg' alt='$calc__layout_2__box_4'></td>
	<td $td__styles><img $img__styles src='$img__src/$calc__layout_2__box_5.jpg' alt='$calc__layout_2__box_5'></td>
	<td $td__styles><img $img__styles src='$img__src/$calc__layout_2__box_6.jpg' alt='$calc__layout_2__box_6'></td>
	<td $td__styles><img $img__styles src='$img__src/$calc__layout_2__box_7.jpg' alt='$calc__layout_2__box_7'></td>
</tr>

<tr>
	<td $td__styles><img $img__styles src='$img__src/$calc__layout_2__box_8.jpg' alt='$calc__layout_2__box_8'></td>
	<td $td__styles><img $img__styles src='$img__src/$calc__layout_2__box_9.jpg' alt='$calc__layout_2__box_9'></td>
	<td $td__styles><img $img__styles src='$img__src/$calc__layout_2__box_10.jpg' alt='$calc__layout_2__box_10'></td>
	<td $td__styles><img $img__styles src='$img__src/$calc__layout_2__box_11.jpg' alt='$calc__layout_2__box_11'></td>
	<td $td__styles><img $img__styles src='$img__src/$calc__layout_2__box_12.jpg' alt='$calc__layout_2__box_12'></td>
	<td $td__styles><img $img__styles src='$img__src/$calc__layout_2__box_13.jpg' alt='$calc__layout_2__box_13'></td>
	<td $td__styles><img $img__styles src='$img__src/$calc__layout_2__box_14.jpg' alt='$calc__layout_2__box_14'></td>
</tr>
		</tabel>
	</body>
</html>
	";
}
else {
	$message = "
		<html>
			<body>
				<p>just test</p>
			</body>
		</html>
	";
}

// If all values exist, send the email
// if ( $sender_name && $sender_email && $sender_message && !empty($recaptcha) ) {
if ( $calc__box_type ) {
	$recipient = RECIPIENT_NAME . " <" . RECIPIENT_EMAIL . ">";
	$headers = "Content-type: text/html; charset = utf-8 \r\n";
//	$headers .= "From: " . $sender_name . " <" . $sender_email . ">";
	$success = mail($recipient, EMAIL_SUBJECT, $message, $headers);
}

// Return an appropriate response to the browser
if (isset($_GET["ajax"])) {
	echo $success ? "success" : "error";
}
else { ?>
	<html>
		<head>
			<title>Спасибо!</title>
		</head>

		<body>
			<?php if ($success) {
				echo "<p>Спасибо за ваше сообщение!</p>";
			}
			else {
				echo "<p>При отправке сообщения возникла ошибка. Попробуйте позже</p>";
			} ?>

			<a href="/">На главную страницу</a>
		</body>
	</html>
<?php } ?>