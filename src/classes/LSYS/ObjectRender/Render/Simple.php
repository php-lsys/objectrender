<?php
/**
 * 默认渲染实现
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\ObjectRender\Render;
use LSYS\ObjectRender;
use LSYS\ObjectRender\Render;
class Simple implements Render{
	/**
	 * {@inheritDoc}
	 * @see \LSYS\ObjectRender\Render::format()
	 */
	public function format($format,$body){
		switch ($format){
			case ObjectRender::FORMAT_JSON:
			    $body=self::_no_scalar_str($body);
			    return ObjectRender::enjson($body);
			case ObjectRender::FORMAT_JSONP:
			    $body=self::_no_scalar_str($body);
			    return ObjectRender::enjsonp($body);
			case ObjectRender::FORMAT_HTML:
				return self::_no_scalar_str($body);
			break;
			case ObjectRender::FORMAT_XML:
			    $body=self::_no_scalar_str($body);
			    return ObjectRender::enxml($body);
			break;
			case ObjectRender::FORMAT_TEXT:
			    return strip_tags(self::_no_scalar_str($body));
			break;
			default: return NULL;
		}
	}
	/**
	 * 非字面变量转为字符串
	 * @param mixed $no_scalar_var
	 * @return string
	 */
	private static function _no_scalar_str($no_scalar_var){
	    ob_start();
	    print_r($no_scalar_var);
	    return ob_get_clean();
	}
}