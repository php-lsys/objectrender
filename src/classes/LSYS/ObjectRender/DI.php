<?php
namespace LSYS\ObjectRender;
/**
 * @method \LSYS\ObjectRender object_render()
 */
class DI extends \LSYS\DI{
    /**
     * @return static
     */
    public static function get(){
        $di=parent::get();
        !isset($di->object_render)&&$di->object_render(new \LSYS\DI\SingletonCallback(function(){
            return new \LSYS\ObjectRender();
        }));
        return $di;
    }
}