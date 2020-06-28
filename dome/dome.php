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
    public function supportClass(){
        return bb::class;
    }
    public function getHttpCode(string $format,$body):int{
        return 404;
    }
    public function getHeader(string $format,$body):array{
        return [
            'x'=>'sss'
        ];
    }
    public function format(string $format, $body){
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

$objrender=DI::get()->objectRender();
$objrender->setRenderSupport(new outbb());
echo $objrender->setFormat(ObjectRender::FORMAT_JSON)->setObject(new HTTP400Exception(null,new Exception("afsdfa",1)));