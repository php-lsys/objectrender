<?php 
/**
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 */
?>
<?php
use LSYS\Core;
use function LSYS\ObjectRender\__;
use LSYS\ObjectRender\Render\Exception;
$error_id = uniqid ( 'error_env' );
?>
<div class="lfw_error">
	<h2><a href="#<?php echo $env_id = $error_id.'environment' ?>" onclick="return koggle('<?php echo $env_id ?>')"><?php echo __('Environment') ?></a>
	<span id="copy"><?php echo __('LSYS Copy')?></span></h2>
	<div id="<?php echo $env_id ?>" class="errcontent collapsed">
		<?php $included = get_included_files() ?>
		<h3><a href="#<?php echo $env_id = $error_id.'environment_included' ?>" onclick="return koggle('<?php echo $env_id ?>')"><?php echo __('Included files') ?></a> (<?php echo count($included) ?>)</h3>
		<div id="<?php echo $env_id ?>" class="collapsed">
			<table>
				<?php foreach ($included as $file): ?>
				<tr>
					<td><code><?php echo ($file) ?></code></td>
				</tr>
				<?php endforeach ?>
			</table>
		</div>
		<?php $included = get_loaded_extensions() ?>
		<h3><a href="#<?php echo $env_id = $error_id.'environment_loaded' ?>" onclick="return koggle('<?php echo $env_id ?>')"><?php echo __('Loaded extensions') ?></a> (<?php echo count($included) ?>)</h3>
		<div id="<?php echo $env_id ?>" class="collapsed">
			<div class="ext_boxs">
				<?php foreach ($included as $file): ?>
					<code><?php echo ($file) ?></code>
				<?php endforeach ?>
			</div>
		</div>
		<?php foreach (array('_SESSION', '_GET', '_POST', '_FILES', '_COOKIE', '_SERVER') as $var): ?>
		<?php if (empty($GLOBALS[$var]) OR ! is_array($GLOBALS[$var])) continue ?>
		<h3><a href="#<?php echo $env_id = $error_id.'environment'.strtolower($var) ?>" onclick="return koggle('<?php echo $env_id ?>')">$<?php echo $var ?></a></h3>
		<div id="<?php echo $env_id ?>" class="collapsed">
			<table>
				<?php foreach ($GLOBALS[$var] as $key => $value): ?>
				<tr>
					<td class="bcent"><code><?php echo htmlspecialchars( (string) $key, ENT_QUOTES, Core::$charset, TRUE); ?></code></td>
					<td><pre><?php echo Exception::dump($value) ?></pre></td>
				</tr>
				<?php endforeach ?>
			</table>
		</div>
		<?php endforeach ?>
	</div>
</div>