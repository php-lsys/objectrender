<!--
/**
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @copyright  (c) 2007-2012 Kohana Team
 */
-->
<?php
use LSYS\Core;
use function LSYS\ObjectRender\__;
use LSYS\ObjectRender\Render\Exception;
$error_id = uniqid ( 'error' );
?>
<style type="text/css">
#lfw_error {
	background: #ddd;
	font-size: 1em;
	font-family: sans-serif;
	text-align: left;
	color: #111;
}

#lfw_error #copy {
	position: absolute;
	right: 20px;
	font-size: 12px;
	color: #333;
	top: 17px;
}

#lfw_error h1, #lfw_error h2 {
	position: relative;
	　word-break: break-all;
	word-wrap: break-word;
	margin: 0;
	padding: 1em;
	font-size: 1em;
	font-weight: normal;
	background: #911;
	color: #fff;
}

#lfw_error h1 a, #lfw_error h2 a {
	color: #fff;
}

#lfw_error h2 {
	background: #222;
}

#lfw_error h3 {
	margin: 0;
	padding: 0.4em 0 0;
	font-size: 1em;
	font-weight: normal;
}

#lfw_error p {
	margin: 0;
	padding: 0.2em 0;
}

#lfw_error a {
	color: #1b323b;
}

#lfw_error pre {
	overflow: auto;
	white-space: pre-wrap;
}

#lfw_error table {
	width: 100%;
	display: block;
	margin: 0 0 0.4em;
	padding: 0;
	border-collapse: collapse;
	background: #fff;
}

#lfw_error table td {
	border: solid 1px #ddd;
	text-align: left;
	vertical-align: top;
	padding: 0.4em;
}

#lfw_error table .bcent {
	vertical-align: middle;
}

#lfw_error div.errcontent {
	padding: 0.4em 1em 1em;
	overflow: hidden;
}

#lfw_error pre.source {
	margin: 0 0 1em;
	padding: 0.4em;
	background: #fff;
	border: dotted 1px #b7c680;
	line-height: 1.2em;
}

#lfw_error pre.source span.line {
	display: block;
}

#lfw_error pre.source span.highlight {
	background: #f0eb96;
}

#lfw_error pre.source span.line span.number {
	color: #666;
}

#lfw_error ol.trace {
	display: block;
	margin: 0 0 0 2em;
	padding: 0;
	list-style: decimal;
}

#lfw_error ol.trace li {
	margin: 0;
	padding: 0;
}

.js .collapsed {
	display: none;
}

#lfw_error table {
	border-collapse: collapse;
}

#lfw_error  .ext_boxs {
	overflow: hidden;
	margin-top: 3px;
	background: #fff;
	flex-wrap: wrap;
	display: -webkit-flex;
    display: -moz-box;
    display: -ms-flexbox;
    display: flex;   
}

#lfw_error  .ext_boxs code {
	color: #333;
    padding: 0 30px;
    -webkit-flex: 1;  
    -ms-flex: 1;  
    -webkit-box-flex: 1;  
    -moz-box-flex: 1;
    flex: 1;    
    width:130px\0;
    width:130px\9\0;
    *width:130px;
    _width:130px;
    padding: 0px\0;
    padding: 0px\9\0;
    *padding: 0px;
    _padding: 0px;
    float: left\0;
    float: left\9\0;
    *float: left;
    _float: left;
	text-align: center;
	line-height: 35px;
	border-right: 1px solid #ddd;
	border-bottom: 1px solid #ddd;
}
</style>
<script type="text/javascript">
document.documentElement.className = document.documentElement.className + ' js';
function koggle(elem)
{
	elem = document.getElementById(elem);
	if (elem.style && elem.style['display'])
		// Only works with the "style" attr
		var disp = elem.style['display'];
	else if (elem.currentStyle)
		// For MSIE, naturally
		var disp = elem.currentStyle['display'];
	else if (window.getComputedStyle)
		// For most other browsers
		var disp = document.defaultView.getComputedStyle(elem, null).getPropertyValue('display');
	// Toggle the state of the "display" style
	elem.style.display = disp == 'block' ? 'none' : 'block';
	return false;
}
</script>
<div id="lfw_error">
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