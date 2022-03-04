<?php // Define constants
define( "RECIPIENT_NAME", "chocolana.com" );
define( "RECIPIENT_EMAIL", "info@foxartbox.com" ); // where to send email
define( "EMAIL_SUBJECT", "[chocolana.com]" );

$success = false;

$sender_name            = isset( $_POST['sender_name'] ) ? preg_replace( "/[^\.\-\' a-zA-Z0-9]/", "", $_POST['sender_name'] ) : "";
$sender_email           = isset( $_POST['sender_email'] ) ? preg_replace( "/[^\.\-\_\@a-zA-Z0-9]/", "", $_POST['sender_email'] ) : "";
$sender_message         = $_POST['sender_message'];

//$recaptcha=$_POST['g-recaptcha-response'];

$message = "
	<html>
		<body>
			<strong>$sender_name</strong> ($sender_email)<br />
			<span style=\"font-size:20px\">$sender_message</span>
		</body>
	</html>
";

// If all values exist, send the email
// if ( $sender_name && $sender_email && $sender_message && !empty($recaptcha) ) {
if ( $sender_name && $sender_email && $sender_message ) {
	$recipient = RECIPIENT_NAME . " <" . RECIPIENT_EMAIL . ">";
	$headers = "Content-type: text/html; charset = utf-8 \r\n";
	$headers .= "From: " . $sender_name . " <" . $sender_email . ">";
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


