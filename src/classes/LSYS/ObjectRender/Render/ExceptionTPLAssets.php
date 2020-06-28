<?php 
/**
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 */
?>
<style type="text/css">
.lfw_error {
    clear: both;
	background: #ddd;
	font-size: 1em;
	font-family: sans-serif;
	text-align: left;
	color: #111;
}

.lfw_error #copy {
	position: absolute;
	right: 20px;
	font-size: 12px;
	color: #333;
	top: 17px;
}

.lfw_error h1, .lfw_error h2 {
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

.lfw_error h1 a, .lfw_error h2 a {
	color: #fff;
}

.lfw_error h2 {
	background: #222;
}

.lfw_error h3 {
	margin: 0;
	padding: 0.4em 0 0;
	font-size: 1em;
	font-weight: normal;
}

.lfw_error p {
	margin: 0;
	padding: 0.2em 0;
}

.lfw_error a {
	color: #1b323b;
}

.lfw_error pre {
	overflow: auto;
	white-space: pre-wrap;
}

.lfw_error table {
	width: 100%;
	display: block;
	margin: 0 0 0.4em;
	padding: 0;
	border-collapse: collapse;
	background: #fff;
}

.lfw_error table td {
	border: solid 1px #ddd;
	text-align: left;
	vertical-align: top;
	padding: 0.4em;
}

.lfw_error table .bcent {
	vertical-align: middle;
}

.lfw_error div.errcontent {
	padding: 0.4em 1em 1em;
	overflow: hidden;
}

.lfw_error pre.source {
	margin: 0 0 1em;
	padding: 0.4em;
	background: #fff;
	border: dotted 1px #b7c680;
	line-height: 1.2em;
}

.lfw_error pre.source span.line {
	display: block;
}

.lfw_error pre.source span.highlight {
	background: #f0eb96;
}

.lfw_error pre.source span.line span.number {
	color: #666;
}

.lfw_error ol.trace {
	display: block;
	margin: 0 0 0 2em;
	padding: 0;
	list-style: decimal;
}

.lfw_error ol.trace li {
	margin: 0;
	padding: 0;
}

.lfw_error_js .collapsed {
	display: none;
}

.lfw_error table {
	border-collapse: collapse;
}

.lfw_error  .ext_boxs {
	overflow: hidden;
	margin-top: 3px;
	background: #fff;
	flex-wrap: wrap;
	display: -webkit-flex;
    display: -moz-box;
    display: -ms-flexbox;
    display: flex;   
}

.lfw_error  .ext_boxs code {
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
document.documentElement.className = document.documentElement.className + ' lfw_error_js';
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
