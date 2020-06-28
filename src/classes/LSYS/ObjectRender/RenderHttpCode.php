<?php
/**
 * 输出渲染接口
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\ObjectRender;
interface RenderHttpCode{
    /**
     * 指定内容输出时的HTTP状态码
     * @param string $format
     * @param object $body
     * @return int
     */
    public function getHttpCode(string $format,$body):int;
}