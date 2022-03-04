<?php if(!$inWidget instanceof \inWidget\Core) {
	throw new \Exception('inWidget object was not initialised.');
} ?>



<!DOCTYPE html>



<html lang="en">

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="content-language" content="<?= $inWidget->langName ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />

	<link rel="stylesheet" href="https://chocolana.com/catalog/view/theme/chocolana/styles.min.css?v<?php echo(date("Ymd")); ?>" />
</head>



<body>
<div class="inst">
	<?php if ($inWidget->toolbar == true): ?>
		<table class="profile">
			<tr>
				<td rowspan="2" class="avatar">
					<a href="//instagram.com/<?= $inWidget->data->username ?>" target="_blank"><img src="<?= $inWidget->data->avatar ?>"></a>
				</td>

				<td class="value">
					<?= $inWidget->data->posts; ?>
					<span><?=$inWidget->lang['statPosts'] ?></span>
				</td>

				<td class="value">
					<?= $inWidget->data->followers ?>
					<span><?= $inWidget->lang['statFollowers'] ?></span>
				</td>

				<td class="value" style="border-right:none !important;">
					<?= $inWidget->data->following ?>
					<span><?= $inWidget->lang['statFollowing'] ?></span>
				</td>
			</tr>

			<tr>
				<td colspan="3" style="border-right:none !important;">
					<a href=//instagram.com/<?= $inWidget->data->username ?>" class="follow" target="_blank"><?= $inWidget->lang['buttonFollow'] ?> &#9658;</a>
				</td>
			</tr>
		</table>
	<?php endif;

	$i = 0;

	$count = $inWidget->countAvailableImages($inWidget->data->images);

	if ( $count > 0 ) {
		if($inWidget->config['imgRandom'] === true)
			shuffle($inWidget->data->images);

		echo '<div class="inst__wrap">';

		foreach ($inWidget->data->images as $key=>$item){
			if($inWidget->isBannedUserId($item->authorId) === true) continue;

			switch ($inWidget->preview){
				case 'large':
					$thumbnail = $item->large;
					break;
				case 'fullsize':
					$thumbnail = $item->fullsize;
					break;
				default:
					$thumbnail = $item->small;
			}

			echo '<a href="'.$item->link.'" class="inst__img" target="_blank"><span style="background-image:url('.$thumbnail.');">&nbsp;</span></a>';

			$i++;

			if ($i >= $inWidget->view)
				break;
		}

		echo '</div>';
	}
	else {
		if(!empty($inWidget->config['HASHTAG'])) {
			$inWidget->lang['imgEmptyByHash'] = str_replace(
				'{$hashtag}',
				$inWidget->config['HASHTAG'],
				$inWidget->lang['imgEmptyByHash']
			);

			echo '<div class="empty">'.$inWidget->lang['imgEmptyByHash'].'</div>';
		}

		else echo '<div class="empty">'.$inWidget->lang['imgEmpty'].'</div>';
	} ?>
</div>



</body>

</html>


