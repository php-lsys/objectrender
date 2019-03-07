<?php
/**
 * 异常渲染实现
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\ObjectRender\Render;
use LSYS\ObjectRender;
use LSYS\Core;
use LSYS\ObjectRender\Render;
use LSYS\ObjectRender\RenderSupport;
use LSYS\HTTPException;
use LSYS\HTTPException\RenderHTML;
use LSYS\HTTPException\RenderArray;
use LSYS\ObjectRender\RenderHttpCode;
use LSYS\ObjectRender\RenderHeader;
class Exception implements Render,RenderHeader,RenderHttpCode,RenderSupport{
	/**
	 * @return string
	 */
	public function supportClass(){
		if (version_compare(PHP_VERSION,"7",">"))return \Throwable::class;
		else return \Exception::class;
	}
	/**
	 * 指定内如输出时补充的HEADER
	 * @param string $format
	 * @param mixed $body
	 * @return array
	 */
	public function getHeader($format,$body){
		switch ($format){
			//JSON输出时 http状态码强制为200
			case ObjectRender::FORMAT_JSON:
			case ObjectRender::FORMAT_JSONP:
				return [];
			break;
			default:
				if ($body instanceof HTTPException){
					return $body->headers();
				}else return [];
		}
	}
	public function getHttpCode($format,$body){
	    switch ($format){
	        //JSON输出时 http状态码强制为200
	        case ObjectRender::FORMAT_JSON:
	        case ObjectRender::FORMAT_JSONP:
	            return 200;
	        default:
	            if ($body instanceof HTTPException){
	                return $body->getHttpCode();
	            }else return 500;
	    }
	}
	/**
	 * {@inheritDoc}
	 * @see \LSYS\ObjectRender\Render::format()
	 */
	public function format($format,$body){
		switch ($format){
			case ObjectRender::FORMAT_HTML:
				return static::enhtmlpage($body);
			break;
			case ObjectRender::FORMAT_JSON:
			    $arr=static::enarray($body);
			    return ObjectRender::enjson($arr);
			case ObjectRender::FORMAT_JSONP:
			    $arr=static::enarray($body);
				return ObjectRender::enjsonp($arr);
			break;
			case ObjectRender::FORMAT_XML:
				$arr=static::enarray($body);
				return ObjectRender::enxml($arr);
			break;
			case ObjectRender::FORMAT_TEXT:
			    return self::entext($body)."\n".$body->getTraceAsString();
			break;
		}
	}
	/**
	 * 把异常转为HTML页面
	 * @param \Exception $e
	 * @return string
	 */
	public static function enhtmlpage($e,$message=null){
		if (version_compare(PHP_VERSION,"7",">"))assert($e instanceof \Throwable);
		else assert($e instanceof \Exception);
		if ($e instanceof HTTPException){
			$render=$e->getRender();
			if($render instanceof RenderHTML) return $render->toHtml();
		}
		if ($message==null)$title=$e->getMessage();
		else $title=$message;
		$title=htmlspecialchars($title);
		if (\LSYS\Core::$environment===\LSYS\Core::PRODUCT){
		    $assets=null;
		    $body=static::enhtml($e,$message,self::ENHTML_RENDER_SIMPLE);
		}else{
		    $assets=static::enhtmlassets();
		    $body=static::enhtml($e,$message,self::ENHTML_RENDER_FULL);
		    $body.=static::enhtmlenv();
		}
		$charset=\LSYS\Core::$charset;
		ob_start();
		include __DIR__."/ExceptionTPLPage.php";
		return ob_get_clean();
	}
	public static function enhtmlassets(){
	    ob_start();
        include __DIR__."/ExceptionTPLAssets.php";
	    return ob_get_clean();
	}
	public static function enhtmlenv(){
	    ob_start();
        include __DIR__."/ExceptionTPLEnv.php";
	    return ob_get_clean();
	}
    const ENHTML_RENDER_FULL=1;
    const ENHTML_RENDER_SIMPLE=2;
	/**
	 * 把异常转为HTML字符串
	 * @param \Exception $e
	 * @return string
	 */
    public static function enhtml($e,$message=null,$render=self::ENHTML_RENDER_FULL){
		if (version_compare(PHP_VERSION,"7",">"))assert($e instanceof \Throwable);
		else assert($e instanceof \Exception);
		if ($message==null) $message = $e->getMessage();
		$code    = $e->getCode();
		if ($render&self::ENHTML_RENDER_SIMPLE){
			$e=get_class($e);
			return "<h1>{$e}:{$message} [{$code}]</h1>";
		}
		while ($pe=$e->getPrevious()){if ($pe===$e)break;$e=$pe;}
		// Get the exception information
		$class   = get_class($e);
		$file    = $e->getFile();
		$line    = $e->getLine();
		$trace   = $e->getTrace();
		if ($e instanceof \ErrorException)
		{
			/**
			 * If XDebug is installed, and this is a fatal error,
			 * use XDebug to generate the stack trace
			 */
			if (function_exists('xdebug_get_function_stack') AND $code == E_ERROR)
			{
				$trace = array_slice(array_reverse(xdebug_get_function_stack()), 4);
				foreach ($trace as & $frame)
				{
					/**
					 * XDebug pre 2.1.1 doesn't currently set the call type key
					 * http://bugs.xdebug.org/view.php?id=695
					 */
					if ( ! isset($frame['type']))
					{
						$frame['type'] = '??';
					}
					// Xdebug returns the words 'dynamic' and 'static' instead of using '->' and '::' symbols
					if ('dynamic' === $frame['type'])
					{
						$frame['type'] = '->';
					}
					elseif ('static' === $frame['type'])
					{
						$frame['type'] = '::';
					}
					// XDebug also has a different name for the parameters array
					if (isset($frame['params']) AND ! isset($frame['args']))
					{
						$frame['args'] = $frame['params'];
					}
				}
			}
		}
		$message=htmlspecialchars( (string) $message, ENT_QUOTES | ENT_IGNORE, Core::$charset, TRUE);
		$message=trim($message,"\n");
		$message=str_replace(array("\n"),"<br>",$message);
		// Capture the view output
		ob_start();
		include __DIR__."/ExceptionTPLBody.php";
		return ob_get_clean();
	}
	/**
	 * 把异常转为数组
	 * @param \Exception $e
	 * @return array
	 */
	public static function enarray($e){
		if (version_compare(PHP_VERSION,"7",">"))assert($e instanceof \Throwable);
		else assert($e instanceof \Exception);
		$type=strtolower(str_replace("\\", "-", get_class($e)));
		if ($e instanceof HTTPException){
			$render=$e->getRender();
			if($render instanceof RenderArray) return $render->toArray();
			$json=array(
				'status'=>false,
			    'type'=>$type,
				'message'=>$e->getMessage(),
				'code'=>$e->getCode(),
				'http-code'=>$e->getHttpCode(),
			);
		}else{
			$json=array(
				'status'=>false,
			    'type'=>$type,
				'message'=>$e->getMessage(),
				'code'=>$e->getCode(),
			);
		}	
		if (Core::$environment!==Core::PRODUCT){
			$json['file'] = $e->getFile();
			$json['line'] = $e->getLine();
			$json['stack-trace'] = explode("\n", $e->getTraceAsString());
			$json['previous']=array();
			while ($e=$e->getPrevious()){
				$json['previous'][]=array(
					'file'=>$e->getFile(),
					'line'=>$e->getLine(),
					'stack-trace'=>explode("\n", $e->getTraceAsString()),
				);
			}
			if (count($json['previous'])==0)unset($json['previous']);
		}
		return $json;
	}
	/**
	 * 把异常转为字符串
	 * @param \Exception $e
	 * @return string
	 */
	public static function entext($e)
	{
		if (version_compare(PHP_VERSION,"7",">"))assert($e instanceof \Throwable);
		else assert($e instanceof \Exception);
		return sprintf('%s [ %s ]: %s ~ %s [ %d ]',
				get_class($e), $e->getCode(), strip_tags($e->getMessage()),$e->getFile(), $e->getLine());
	}
	/**
	 * Returns an HTML string of information about a single variable.
	 *
	 * Borrows heavily on concepts from the Debug class of [Nette](http://nettephp.com/).
	 *
	 * @param   mixed   $value              variable to dump
	 * @param   integer $length             maximum length of strings
	 * @param   integer $level_recursion    recursion limit
	 * @return  string
	 */
	protected static function dump($value, $length = 128, $level_recursion = 10)
	{
		return self::_dump($value, $length, $level_recursion);
	}
	/**
	 * Helper for self::dump(), handles recursion in arrays and objects.
	 *
	 * @param   mixed   $var    variable to dump
	 * @param   integer $length maximum length of strings
	 * @param   integer $limit  recursion limit
	 * @param   integer $level  current recursion level (internal usage only!)
	 * @return  string
	 */
	private static function _dump( & $var, $length = 128, $limit = 10, $level = 0)
	{
		if ($var === NULL)
		{
			return '<small>NULL</small>';
		}
		elseif (is_bool($var))
		{
			return '<small>bool</small> '.($var ? 'TRUE' : 'FALSE');
		}
		elseif (is_float($var))
		{
			return '<small>float</small> '.$var;
		}
		elseif (is_resource($var))
		{
			if (($type = get_resource_type($var)) === 'stream' AND $meta = stream_get_meta_data($var))
			{
				$meta = stream_get_meta_data($var);
				if (isset($meta['uri']))
				{
					$file = $meta['uri'];
					return '<small>resource</small><span>('.$type.')</span> '.htmlspecialchars($file, ENT_NOQUOTES, Core::$charset);
				}
			}
			else
			{
				return '<small>resource</small><span>('.$type.')</span>';
			}
		}
		elseif (is_string($var))
		{
			// Clean invalid multibyte characters. iconv is only invoked
			// if there are non ASCII characters in the string, so this
			// isn't too much of a hit.
			if (mb_strlen($var) > $length)
			{
				// Encode the truncated string
				$str = htmlspecialchars(mb_substr($var, 0, $length), ENT_NOQUOTES, Core::$charset).'&nbsp;&hellip;';
			}
			else
			{
				// Encode the string
				$str = htmlspecialchars($var, ENT_NOQUOTES, Core::$charset);
			}
			return '<small>string</small><span>('.strlen($var).')</span> "'.$str.'"';
		}
		elseif (is_array($var))
		{
			$output = array();
			// Indentation for this variable
			$space = str_repeat($s = '    ', $level);
			static $marker;
			if ($marker === NULL)
			{
				// Make a unique marker - force it to be alphanumeric so that it is always treated as a string array key
				$marker = uniqid("\x00")."x";
			}
			if (empty($var))
			{
				// Do nothing
			}
			elseif (isset($var[$marker]))
			{
				$output[] = "(\n$space$s*RECURSION*\n$space)";
			}
			elseif ($level < $limit)
			{
				$output[] = "<span>(";
				$var[$marker] = TRUE;
				foreach ($var as $key => & $val)
				{
					if ($key === $marker) continue;
					if ( ! is_int($key))
					{
						$key = '"'.htmlspecialchars($key, ENT_NOQUOTES, Core::$charset).'"';
					}
					$output[] = "$space$s$key => ".self::_dump($val, $length, $limit, $level + 1);
				}
				unset($var[$marker]);
				$output[] = "$space)</span>";
			}
			else
			{
				// Depth too great
				$output[] = "(\n$space$s...\n$space)";
			}
			return '<small>array</small><span>('.count($var).')</span> '.implode("\n", $output);
		}
		elseif (is_object($var))
		{
			// Copy the object as an array
			$array = (array) $var;
			$output = array();
			// Indentation for this variable
			$space = str_repeat($s = '    ', $level);
			$hash = spl_object_hash($var);
			// Objects that are being dumped
			static $objects = array();
			if (empty($var))
			{
				// Do nothing
			}
			elseif (isset($objects[$hash]))
			{
				$output[] = "{\n$space$s*RECURSION*\n$space}";
			}
			elseif ($level < $limit)
			{
				$output[] = "<code>{";
				$objects[$hash] = TRUE;
				foreach ($array as $key => & $val)
				{
					if ($key[0] === "\x00")
					{
						// Determine if the access is protected or protected
						$access = '<small>'.(($key[1] === '*') ? 'protected' : 'private').'</small>';
						// Remove the access level from the variable name
						$key = substr($key, strrpos($key, "\x00") + 1);
					}
					else
					{
						$access = '<small>public</small>';
					}
					$output[] = "$space$s$access $key => ".self::_dump($val, $length, $limit, $level + 1);
				}
				unset($objects[$hash]);
				$output[] = "$space}</code>";
			}
			else
			{
				// Depth too great
				$output[] = "{\n$space$s...\n$space}";
			}
			return '<small>object</small> <span>'.get_class($var).'('.count($array).')</span> '.implode("\n", $output);
		}
		else
		{
			return '<small>'.gettype($var).'</small> '.htmlspecialchars(print_r($var, TRUE), ENT_NOQUOTES, Core::$charset);
		}
	}
	/**
	 * Returns an HTML string, highlighting a specific line of a file, with some
	 * number of lines padded above and below.
	 *
	 *     // Highlights the current line of the current file
	 *     echo self::source(__FILE__, __LINE__);
	 *
	 * @param   string  $file           file to open
	 * @param   integer $line_number    line number to highlight
	 * @param   integer $padding        number of padding lines
	 * @return  string   source of file
	 * @return  FALSE    file is unreadable
	 */
	protected static function source($file, $line_number, $padding = 5)
	{
		if ( ! $file OR ! is_readable($file))
		{
			// Continuing will cause errors
			return FALSE;
		}
		// Open the file and set the line position
		$file = fopen($file, 'r');
		$line = 0;
		// Set the reading range
		$range = array('start' => $line_number - $padding, 'end' => $line_number + $padding);
		// Set the zero-padding amount for line numbers
		$format = '% '.strlen($range['end']).'d';
		$source = '';
		while (($row = fgets($file)) !== FALSE)
		{
			// Increment the line number
			if (++$line > $range['end'])
				break;
				if ($line >= $range['start'])
				{
					// Make the row safe for output
					$row = htmlspecialchars($row, ENT_NOQUOTES, Core::$charset);
					// Trim whitespace and sanitize the row
					$row = '<span class="number">'.sprintf($format, $line).'</span> '.$row;
					if ($line === $line_number)
					{
						// Apply highlighting to this row
						$row = '<span class="line highlight">'.$row.'</span>';
					}
					else
					{
						$row = '<span class="line">'.$row.'</span>';
					}
					// Add to the captured source
					$source .= $row;
				}
		}
		// Close the file
		fclose($file);
		return '<pre class="source"><code>'.$source.'</code></pre>';
	}
	/**
	 * Returns an array of HTML strings that represent each step in the backtrace.
	 *
	 *     // Displays the entire current backtrace
	 *     echo implode('<br/>', self::trace());
	 *
	 * @param   array   $trace
	 * @return  string
	 */
	protected static function trace(array $trace = NULL)
	{
		if ($trace === NULL)
		{
			// Start a new trace
			$trace = debug_backtrace();
		}
		// Non-standard function calls
		$statements = array('include', 'include_once', 'require', 'require_once');
		$output = array();
		foreach ($trace as $step)
		{
			if ( ! isset($step['function']))
			{
				// Invalid trace step
				continue;
			}
			if (isset($step['file']) AND isset($step['line']))
			{
				// Include the source of this step
				$source = self::source($step['file'], $step['line']);
			}
			if (isset($step['file']))
			{
				$file = $step['file'];
				if (isset($step['line']))
				{
					$line = $step['line'];
				}
			}
			// function()
			$function = $step['function'];
			if (in_array($step['function'], $statements))
			{
				if (empty($step['args']))
				{
					// No arguments
					$args = array();
				}
				else
				{
					// Sanitize the file path
					$args = array($step['args'][0]);
				}
			}
			elseif (isset($step['args']))
			{
				if ( ! function_exists($step['function']) OR strpos($step['function'], '{closure}') !== FALSE)
				{
					// Introspection on closures or language constructs in a stack trace is impossible
					$params = NULL;
				}
				else
				{
					if (isset($step['class']))
					{
						if (method_exists($step['class'], $step['function']))
						{
							$reflection = new \ReflectionMethod($step['class'], $step['function']);
						}
						else
						{
							$reflection = new \ReflectionMethod($step['class'], '__call');
						}
					}
					else
					{
						$reflection = new \ReflectionFunction($step['function']);
					}
					// Get the function parameters
					$params = $reflection->getParameters();
				}
				$args = array();
				foreach ($step['args'] as $i => $arg)
				{
					if (isset($params[$i]))
					{
						// Assign the argument by the parameter name
						$args[$params[$i]->name] = $arg;
					}
					else
					{
						// Assign the argument by number
						$args[$i] = $arg;
					}
				}
			}
			if (isset($step['class']))
			{
				// Class->method() or Class::method()
				$function = $step['class'].$step['type'].$step['function'];
			}
			$output[] = array(
					'function' => $function,
					'args'     => isset($args)   ? $args : NULL,
					'file'     => isset($file)   ? $file : NULL,
					'line'     => isset($line)   ? $line : NULL,
					'source'   => isset($source) ? $source : NULL,
			);
			unset($function, $args, $file, $line, $source);
		}
		return $output;
	}
	
}
