<?php 
/**
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @var string $title
 * @var string $assets
 * @var string $charset
 * @var string $body
 */
?>
<!DOCTYPE HTML>
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset?>">
		<title><?php echo $title?></title>
		<?php echo $assets?>
	</head>
	<body><?php echo $body?></body>
</html>