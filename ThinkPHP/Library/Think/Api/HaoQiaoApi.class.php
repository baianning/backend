<?php

/*
 * 对接好巧酒店接口api
 */

class HaoQiaoApi {
/***************测试账号配置****************/
/*    public $url = "http://test.api.haoqiao.cn";
    protected $orgin = 'H5849';
    protected $username = 'BJcltxXmlTest';
    protected $authkey = '45pg0p0b9nefqve86c43';
    protected $version = '2.0';*/
/************正式账号配置************/
    public $url = "http://api.haoqiao.cn";//正式请求地址
    protected $orgin = 'H5849';
    protected $username = 'BJcltxXml';
    protected $authkey = 'ff49hu4Agozue3skfbfi';
    protected $version = '2.0';
    /**
     * 组装请求数据的XML格式
     * @param  [type] $data 请求的参数
     *                $urlType 请求的地址 空为price 1为order
     * @return [type]       XML
     * 
     */
    public function getXMLData($requestXML,$urlType='') {
        $sid = session_id().time().  mt_rand(100, 999);
        $xml = '';
        $xml .= "<?xml version='1.0' encoding='utf-8' ?>";
        $xml .= "<AvailabilityRequest SessionId='{$sid}'>";
        $xml .= "<Authentication>";
        $xml .= "<OrgID>$this->orgin</OrgID>";
        $xml .= "<UserName>$this->username</UserName>";
        $xml .= "<AuthKey>$this->authkey</AuthKey>";
        $xml .= "<Version>$this->version</Version>";
        $xml .= "</Authentication>";
        $xml .= $requestXML;
        $xml .= "</AvailabilityRequest>";
        //\Think\Log::write($xml);
        //return $xml;
        if($urlType=='1'){
           $url = $this->url . '/order';
        }else{
           $url = $this->url . '/price';
        }
        
        return $this->getResponse($xml, $url);
    }

    /**
     * 传入xml参数请求，返回请求接口后转换为array的数据
     * @param  [type] $xml [description]
     * @return [type]      [description]
     */
    public function getResponse($xml, $requesturl) {
        \Think\Log::write('RequestMethod---->'.$requesturl.'========='.$xml,'','','Application/Runtime/Logs/Admin/haoqiao.log');
        $responseData = request_post($requesturl, $xml);
        \Think\Log::write('ResponseData---->'.$responseData,'','','Application/Runtime/Logs/Admin/haoqiao.log');
        //var_dump($responseData);
        
        //$data = $this->xmlToArray($responseData);
       $data = $this->xmlToArr(simplexml_load_string($responseData));
        //dump($data);die;
        return $data;
    }

    public function xmlToArray($xml) {
        $ret = array();
        if ($xml instanceOf SimpleXMLElement) {
            $xmlDoc = $xml;
        } else {
            $xmlDoc = simplexml_load_string($xml, 'SimpleXMLIterator');
            if (!$xmlDoc) {      // xml字符串格式有问题
                return null;
            }
        }

        for ($xmlDoc->rewind(); $xmlDoc->valid(); $xmlDoc->next()) {
            $key = $xmlDoc->key();       // 获取标签��?
            echo $key;echo '<hr/>';
            $val = $xmlDoc->current();   // 获取当前标签
            echo $val;
            if ($xmlDoc->hasChildren()) {     // 如果有子元素
                
                $ret[$key] = $this->xmlToArray($val);  // 子元素变量递归处理返回
                
            } else {
                $ret[$key] = (string) $val;
            }
        }
        return $ret;
    }

    public function simplest_xml_to_array($xmlstring) {
        return json_decode(json_encode((array) simplexml_load_string($xmlstring)), true);
    }

    
    function xmlToArr($xml) 
    {
        if (!($xml->children()))  // 单个子元素，转为字符串返回
        {
            return (string) $xml;
        }
        // 直接遍历子元素对象
        foreach ($xml->children() as $child)
        {
            $name = $child->getName();  // 获取元素名
            if (count($xml->$name) ==1 )
            {
                $element[$name] = $this->xmlToArr($child);
            } 
            else  // 如果同名子元素有多个，装在一个数组里边
            {
                $element[][$name] = $this->xmlToArr($child);
            }
        }
        return $element;
    }       
    

}
