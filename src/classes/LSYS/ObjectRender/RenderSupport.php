<?php
/**
 * 带支持的渲染类的渲染接口
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\ObjectRender;
interface RenderSupport extends Render{
	/**
	 * 返回支持类名,多个类返回数组
	 * @return string|array
	 */
	public function supportClass();
}