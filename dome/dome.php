<?php
use LSYS\ObjectRender\DI;
use LSYS\ObjectRender;
use LSYS\ObjectRender\RenderSupport;
use LSYS\ObjectRender\RenderHeader;
use LSYS\ObjectRender\RenderHttpCode;
use LSYS\HTTPException\HTTP400Exception;
include_once __DIR__."/../vendor/autoload.php";
class bb{
}
class outbb implements RenderSupport,RenderHeader,RenderHttpCode{
    public static function factory(){
        return new static();
    }
    public function support_class(){
        return bb::class;
    }
    public function get_http_code($format,$body){
        return 404;
    }
    public function get_header($format,$body){
        return [
            'x'=>'sss'
        ];
    }
    public function format($format, $body){
        switch ($format){
            case ObjectRender::FORMAT_XML:
                return '<root></root>';
            case ObjectRender::FORMAT_JSON:
            case ObjectRender::FORMAT_JSONP:
                return json_encode($body);
        }
        return "dddd";
    }
}

$objrender=DI::get()->object_render();
$objrender->set_render_support(new outbb());
echo $objrender->set_format(ObjectRender::FORMAT_JSON)->set_object(new HTTP400Exception(null,new Exception("afsdfa",1)));