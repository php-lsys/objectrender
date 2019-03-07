<?php
namespace LSYS\ObjectRender;
/**
 * @method \LSYS\ObjectRender objectRender()
 */
class DI extends \LSYS\DI{
    /**
     * @return static
     */
    public static function get(){
        $di=parent::get();
        !isset($di->objectRender)&&$di->objectRender(new \LSYS\DI\SingletonCallback(function(){
            return new \LSYS\ObjectRender();
        }));
        return $di;
    }
}