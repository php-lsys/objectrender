<?php 
/**
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 */
?>
<?php
use function LSYS\ObjectRender\__;
use LSYS\ObjectRender\Render\Exception;
$error_id = uniqid ( 'error_body' );
?>
<div class="lfw_error">
	<h1><span class="type"><?php echo $class ?> [ <?php echo $code ?> ]:</span> <span class="message"><?php echo $message; ?></span></h1>
	<div id="<?php echo $error_id ?>" class="errcontent">
		<p><span class="file"><?php echo ($file) ?> [ <?php echo $line ?> ]</span></p>
		<?php echo Exception::source($file, $line) ?>
		<ol class="trace">
		<?php foreach (Exception::trace($trace) as $i => $step): ?>
			<li>
				<p>
					<span class="file">
						<?php if ($step['file']): $source_id = $error_id.'source'.$i; ?>
							<a href="#<?php echo $source_id ?>" onclick="return koggle('<?php echo $source_id ?>')"><?php echo ($step['file']) ?> [ <?php echo $step['line'] ?> ]</a>
						<?php else: ?>
							{<?php echo __('PHP internal call') ?>}
						<?php endif ?>
					</span>
					&raquo;
					<?php echo $step['function'] ?>(<?php if ($step['args']): $args_id = $error_id.'args'.$i; ?><a href="#<?php echo $args_id ?>" onclick="return koggle('<?php echo $args_id ?>')"><?php echo __('arguments') ?></a><?php endif ?>)
				</p>
				<?php if (isset($args_id)): ?>
				<div id="<?php echo $args_id ?>" class="collapsed">
					<table>
					<?php foreach ($step['args'] as $name => $arg): ?>
						<tr>
							<td><code><?php echo $name ?></code></td>
							<td><pre><?php echo Exception::dump($arg) ?></pre></td>
						</tr>
					<?php endforeach ?>
					</table>
				</div>
				<?php endif ?>
				<?php if (isset($source_id)): ?>
					<pre id="<?php echo $source_id ?>" class="source collapsed"><code><?php echo $step['source'] ?></code></pre>
				<?php endif ?>
			</li>
			<?php unset($args_id, $source_id); ?>
		<?php endforeach ?>
		</ol>
	</div>
</div>