<?php
/**
 * 输出渲染接口
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\ObjectRender;
interface RenderHeader{
	/**
	 * 渲染指定内容时,设置需要输出HEADER
	 * @param string $format
	 * @param object $body
	 * @return array
	 */
    public function getHeader(string $format,$body):array;
}