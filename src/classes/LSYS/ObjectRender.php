<?php
/**
 * 统一输出
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS;
use LSYS\ObjectRender\Render;
use LSYS\ObjectRender\Render\Exception;
use LSYS\ObjectRender\Render\Simple;
use LSYS\ObjectRender\RenderHttpCode;
use LSYS\ObjectRender\RenderHeader;
use LSYS\ObjectRender\RenderSupport;
class ObjectRender{
    /**
     * JSON回调KEY
     * @var string
     */
    public static $jsonp_key='callback';
    /**
     * XML根元素
     * @var string
     */
    public static $xml_root='root';
    /**
     * 把数据转JSON字符
     * @param mixed $data
     * @return string
     */
    public static function enjson($data){
        return json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    /**
     * 把数据转JSONP字符
     * @param mixed $data
     * @return string
     */
    public static function enjsonp($data){
        $data=self::enjson($data);
        $str=isset($_REQUEST[self::$jsonp_key])?$_REQUEST[self::$jsonp_key]:'';
        $str=str_replace(array("\n","\s","'",'"','`','(',')','[',']','{','}','#','!','$','?'),"", strip_tags($str));
        return $str.'('.$data.')';
    }
    /**
     * 把数据转为XML字符串
     * @param mixed $data
     * @return string
     */
    public static function enxml($data){
        $xml = new \XmlWriter();
        $xml->openMemory();
        $xml->startDocument('1.0', Core::$charset);
        if (is_array($data)){
            $xml->startElement(self::$xml_root);
            self::_arrtoxml($xml,$data);
            $xml->endElement();
        }else{
            self::_noarrtoxml($xml, self::$xml_root, $data);
        }
        return $xml->outputMemory(true);
    }
    /**
     * 非数组添加到XML对象
     * @param \XmlWriter $xml
     * @param string $key
     * @param mixed $value
     */
    private static function _noarrtoxml($xml,$key,$value){
        if (!is_scalar($value)){
            print_r($value,$value);//理论上不会进这里
        }elseif ($value===true){
            $value='true';
        }elseif ($value===false){
            $value='false';
        }
        if (is_numeric($key))$key="stack-".$key;
        $xml->writeElement(strval($key), strval($value));
    }
    /**
     * 数组添加到XML对象
     * @param \XMLWriter $xml
     * @param array $data
     */
    private static function _arrtoxml($xml,array $data){
        foreach($data as $key => $value){
            if(is_array($value)){
                $xml->startElement($key);
                self::_arrtoxml($xml,$value);
                $xml->endElement();
                continue;
            }
            self::_noarrtoxml($xml, $key, $value);
        }
    }
	/**
	 * 输出为HTML格式
	 * @var string
	 */
	const FORMAT_HTML = 'html';
	/**
	 * 输出为TEXT格式
	 * @var string
	 */
	const FORMAT_TEXT = 'text';
	/**
	 * 输出为JSON格式
	 * @var string
	 */
	const FORMAT_JSON = 'json';
	/**
	 * 输出为JSONP格式
	 * @var string
	 */
	const FORMAT_JSONP = 'jsonp';
	/**
	 * 输出为JSONP格式
	 * @var string
	 */
	const FORMAT_XML = 'xml';
	/**
	 * @var string
	 */
	protected $_header=[];
	/**
	 * @var string
	 */
	protected $_body;
	/**
	 * @var string
	 */
	protected $_format;
	/**
	 * @var Render
	 */
	protected $_render;
	/**
	 * @var Render[]
	 */
	protected $_renders=[];
	public function __construct(){
	    $this->_format=$this->_first_accept();
	    if ($this->_format!=self::FORMAT_HTML) {
	        $this->set_format($this->_format);
	    }
	}
	/**
	 * 优先接受选择
	 * @return string
	 */
	protected function _first_accept(){
	    $accepts=array(
	        self::FORMAT_HTML=>'text/html,application/xhtml+xml',
	        self::FORMAT_XML=>'text/xml,application/xml',
	        self::FORMAT_JSON=>'application/json,text/json',
	        self::FORMAT_JSONP=>'application/jsonp,application/javascript',
	        self::FORMAT_TEXT=>'text/plain',
	    );
	    if (!isset($_SERVER['HTTP_ACCEPT'])||empty($_SERVER['HTTP_ACCEPT']))return self::FORMAT_HTML;
	    foreach (explode(",",$_SERVER['HTTP_ACCEPT']) as $v){
	        foreach ($accepts as $k=>$accept){
	            foreach (explode(",",$accept) as $vv){
	                if (strpos($v, $vv)!==false)return $k;
	            }
	        }
	    }
	    return self::FORMAT_HTML;
	}
	/**
	 * 添加渲染对象
	 * 当$support_class为NULL时为默认渲染对象,未找到对应渲染类的都使用此渲染对象渲染
	 * 当$support_class不为NULL,body为 $support_class 类时,使用 $render 进行渲染
	 * $support_class　为空时，多次设置将覆盖
	 * @param string $support_class
	 * @param mixed $render
	 * @return $this
	 */
	public function set_render(Render $render,$support_class=null){
		$this->_renders[$support_class]=$render;
		return $this;
	}
	/**
	 * 添加渲染对象
	 * @param string $support_class
	 * @return $this
	 */
	public function set_render_support(RenderSupport $render_support){
	    $support_class=$render_support->support_class();
	    $support_class=is_array($support_class)?$support_class:[$support_class];
	    foreach ($support_class as $_support_class){
	        $this->set_render($render_support,$_support_class);
	    }
	    return $this;
	}
	/**
	 * 设置输出格式
	 * @param array
	 * @return $this
	 */
	public function set_format($format){
		$this->_format=$format;
		switch ($format){
		    case self::FORMAT_JSON:
		        $this->_header["Content-Type"]="application/json;charset=".Core::$charset;
		        break;
		    case self::FORMAT_JSONP:
		        $this->_header["Content-Type"]="application/x-javascript;charset=".Core::$charset;
		        break;
		    case self::FORMAT_TEXT:
		        $this->_header["Content-Type"]="text/plain;charset=".Core::$charset;
		        break;
		    case self::FORMAT_XML:
		        $this->_header["Content-Type"]="application/xml;charset=".Core::$charset;
		        break;
		    case self::FORMAT_HTML:
		        $this->_header["Content-Type"]="text/html;charset=".Core::$charset;
		        break;
		}
		return $this;
	}
	/**
	 * 获取已设置的输出格式
	 * @return string
	 */
	public function get_format(){
		return $this->_format;
	}
	/**
	 * 获取已设置HTTP状态码
	 * @return int
	 */
	public function get_http_code(){
	    return $this->_render instanceof RenderHttpCode?$this->_render->get_http_code($this->_format,$this->_body):null;
	}
	/**
	 * 设置渲染对象
	 * @param object $body
	 * @return $this
	 */
	public function set_object($object){
	    assert(is_object($object));
	    $this->_render=$this->_find_render($object);
	    $this->_body=$object;
		return $this;
	}
	/**
	 * 获取已设置的HEADER
	 */
	public function get_header($join=false){
	    if (!$this->_render instanceof RenderHeader)return [];
	    $header=$this->_render->get_header($this->_format, $this->_body);
	    $header=is_array($header)?$header+$this->_header:$this->_header;
	    if (!$join)return $header;
	    $_header=array();
	    foreach ($header as $k=>$v){
	        $_header[]=$k.":".$v;
	    }
	    return $_header;
	}
	public function render(){
	    if (!$this->_render)return '';
	    return $this->_render->format($this->_format, $this->_body);
	}
	public function __toString(){
	    try{
			if(!headers_sent())foreach ($this->get_header(true) as $v){
	            @header($v);
	        }
	        $this->get_http_code()&&@http_response_code($this->get_http_code());
	       return $this->render();
	    }catch (\Exception $e){
	       return Exception::entext($e);
	    }
	}
	protected function _sort_render(){
		$t=$this->_renders;
		unset($t['']);
		$t=array_keys($t);
		$o=[];
		foreach ($t as $v){
			$v_=$v;
			$o[$v_]=[];
			while ($t=get_parent_class($v)){
				$o[$v_][]=$t;
				$v=$t;
			}
		}
		$t=[];
		foreach ($o as $k=>$v){
			$g=$k;
			if (count($v)>0)$g=$v[count($v)-1];
			$_k=count($v);
			while (isset($t[$g][$_k])){
				$_k+=0.00001;
			}
			$t[$g][$_k]=$k;
		}
		$o=[];
		foreach ($t as $k=>$v){
			$v=array_unique($v);
			krsort($v,SORT_NUMERIC);
			$o=array_merge($o,$v);
		}
		$renders=[];
		foreach ($o as $k){
			$renders[$k]=$this->_renders[$k];
		}
		return $renders;
	}
	/**
	 * 渲染按指定格式渲染输出
	 * @return Render
	 */
	protected function _find_render($body){
		$renders=$this->_sort_render();
		foreach ($renders as $k=>$v){
		    if (is_a($body,$k)){
				$render=$v;
				break;
			}
		}
		if (!isset($render)){
		    if ((version_compare(PHP_VERSION,"7",">")&&$body instanceof \Throwable)
		        ||$body instanceof \Exception){
				$render=new Exception();
				foreach ((array)$render->support_class() as $class){
					$this->set_render($render,$class);
				}
			}else{
				if (!isset($this->_renders[''])){
					$render=new Simple;
					$this->set_render($render);
				}else $render=$this->_renders[''];
			}
		}
		return $render;
	}
}
