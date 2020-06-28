<?php
/**
 * 输出渲染接口
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\ObjectRender;
interface Render{
	/**
	 * 把指定内容格式化
	 * @param string $format
	 * @param object $body
	 * @return string|resource
	 */
	public function format(string $format,$body);
}