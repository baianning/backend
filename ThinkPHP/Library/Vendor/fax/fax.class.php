<?php
header('Content-type: text/html; charset=utf-8');
include_once('cls_nusoap.php');
/**
 * 发��?35fax网络传真��?
 */

class fax{
		private $UserID = '10217112';
		private $Password = '000000';
		private $sendUrl = 'http://121.58.158.42/servlet/services/FaxService';//发送地址
		private $queryUrl = 'http://121.58.158.42/servlet/services/FaxService/SendFaxToServer/QueryResultForRecvTask';//收件地址
		private $resultUrl = "http://121.58.158.42/servlet/services/FaxService/SendFaxToServer/QueryResultForSendTask";//查询发送结果
		/**
		 * 发送传��?
		 * @param  [type] $FaxNumber [要发给的传真号]010-82868396
		 * @param  [type] $html_str  [要发送的html内容]
		 * @return [array] status=0 成功；其余失败；$client是我们自己生成的作业号，用于查询发送结果
		 */
		public function sendFax($FaxNumber,$html_str,$client){
			$soap = new nusoap_client("$this->sendUrl");
			$soap->soap_defencoding = "GBK";
			$soap->decode_utf8 = false;
			$str=$this->getSendFaxXMLstr($FaxNumber,$html_str);  //
			//echo $str;
			$params=array('arg0'=>$str);
			$soap->call("SendFaxToServer",$params,'http://axis2.fax.uniproud.com');
			$responseData=$soap->responseData;
			$result=substr($responseData,strpos($responseData,'&lt;ErrorFlag>')+14,strpos($responseData,'&lt;/ErrorFlag>')-strpos($responseData,'&lt;ErrorFlag>')-14);
			// $result = iconv('gb2312','utf-8',$result);
			if($result == 0){
				$responseData = str_replace('&lt;','<',$responseData);
					$a = substr($responseData,strpos($responseData,'<SendFaxToServerResult>'));
					$len = strpos($a,"</SendFaxToServerResult>")+24;
					$str = substr($a,0,$len);
					$data = $this->simplest_xml_to_array($str);
//					var_dump($data);die;
					$arr = array('status'=>'0','jobNo'=>$data['JobNo']);
					return $arr;
			}else{
				return array('status'=>'-1'); //
			}



		}
/**
 * 接受传真清单
 * @param  string $FaxNumber [description]
 * @param  string $startTime [description]
 * @param  string $endTime   [description]
 * @return [type]            [description]
 */
		public function getReceiveFaxList($faxNumber='',$startTime='',$endTime=''){
			$soap = new nusoap_client("$this->queryUrl");
			$soap->soap_defencoding = "GBK";
			$soap->decode_utf8 = false;
			// $soap->xml_encoding = 'utf-8';
			$str=$this->getSelectFaxXMLstr($faxNumber,$startTime,$endTime);
			// var_dump($str);
			$params=array('arg0'=>$str);
		  $soap->call("QueryResultForRecvTask",$params,'http://axis2.fax.uniproud.com');
			$responseData=$soap->responseData;
			$responseData = trim($responseData);
			$a = str_replace('&lt;','<',$responseData);
			var_dump($a);
			$b = substr($a,strpos($a,'<Header>'));
			$len = strpos($b,"</Header>")+9;
			$strafter = substr($b,0,$len);
			$data = $this->simplest_xml_to_array($strafter);
			if($data['ErrorFlag'] == 0){
				$b = substr($a,strpos($a,'<QueryResultForRecvTaskResponse>'));
				$len = strpos($b,"</QueryResultForRecvTaskResponse>")+33;
				$strafter = substr($b,0,$len);
				$data = $this->simplest_xml_to_array($strafter);
				var_dump($data);
				$fileName = 'Public/Admin/fax/'.$data['QueryResultForRecvTaskResult']['RecvFaxResult']['RecvFileName'];
				var_dump($fileName);
			//	var_dump(fopen($fileName,'w'));
				$fileData = base64_decode($data['QueryResultForRecvTaskResult']['Document']);
				$result = file_put_contents($fileName,$fileData);
				var_dump($result);die;
			}

			$response = simplexml_load_string($strafter);
			//return $soap->request();die;
			$result = $this->xmlToArray($response);
			var_dump($result);die;

		}
/**
 * 查询某个作业发送结果，对方是否接受成功
 * @return status 0 标示请求成功；result=0表示成功，其余均是失败
 * @param  string $clientTaskId [客户端请求id]
 * @param  string $jobNoList    [作业号，在调用发送传真的时候会返回]
 * @param  string $startTime    [description]
 * @param  string $endTime      [description]
 */
		public function selectSendResult($clientTaskId='',$jobNoList='',$startTime='',$endTime=''){
			$soap = new nusoap_client("$this->resultUrl");
			$soap->soap_defencoding = "GBK";
			$soap->decode_utf8 = false;
			// $soap->xml_encoding = 'utf-8';
			$str=$this->sendResultXmlStr($clientTaskId,$jobNoList,$startTime,$endTime);
			//var_dump($str);die;
			$params=array('arg0'=>$str);
		 	$soap->call("QueryResultForSendTask",$params,'http://axis2.fax.uniproud.com');
			$responseData=$soap->responseData;
			// var_dump($responseData);die;
			$a = str_replace('&lt;','<',$responseData);
			$b = substr($a,strpos($a,'<Header>'));
			$len = strpos($b,"</Header>")+9;
			$strafter = substr($b,0,$len);
			// var_dump($strafter);die;
			$data = $this->simplest_xml_to_array($strafter);
			//var_dump($data);die;
			if($data['ErrorFlag'] == 0){
				$b = substr($a,strpos($a,'<SendFaxResultList>'));
				$len = strpos($b,"</SendFaxResultList>")+20;
				$strafter = substr($b,0,$len);
//				var_dump($strafter);die;
				$returnMsg = $this->simplest_xml_to_array($strafter);
//				 return $returnMsg;
				$returnarr = array('status'=>'0','result'=>$returnMsg['SendFaxResult']['result']);
				return $returnarr;
			}else{
				return array('status'=>'-1');
			}

		}


		function getSendFaxXMLstr($FaxNumber,$html_str,$client)
		{
			$ss='';
			$ss .= "<?xml version=\"1.0\" encoding=\"gb2312\"?>";
			$ss .= "<FaxInfo>";
			$ss .= "<Login>";
			$ss .= "<UserID>$this->UserID</UserID>";//用户��?
			$ss .= "<Password>$this->Password</Password>";
			$ss .= "</Login>";
			$ss .= "<SendTaskList>";
			//SendTask
			$ss .= "<SendTask>";
			$ss .= "<ClientTaskID>$client</ClientTaskID>";//
			$ss .= "<FaxNumber>$FaxNumber</FaxNumber>";//
			$ss .= "</SendTask>";
			$ss .= "</SendTaskList>";
			$ss .= "<FaxOptions>";
			$ss .= "<Priority>1</Priority>";//
			$ss .= "<Resolution>2</Resolution>";//
		  $ss .= "<FaxSidFlag>2</FaxSidFlag>";//
		  $ss .= "<FaxSid>2</FaxSid>";//
			$ss .= "</FaxOptions>";
		  $ss .= "<DocumentList>";
		  $ss .= "<Document FileName=\"application/html\" EncodingType=\"base64\"  DocumentExtension=\"html\" >";
		    //$ss .= encode_base64("/Users/hanxu/data/hotel_order_demo.docx");
		  $ss .= base64_encode($html_str);
		  $ss .= "</Document>";
		  $ss .= "</DocumentList>";
			$ss .= "</FaxInfo>";

			return $ss;
		}

		function getSelectFaxXMLstr($faxNumber='',$startTime='',$endTime=''){
			$ss  = '';
			$ss .= "<?xml version=\"1.0\" encoding=\"gb2312\"?>";
			$ss .= "<FaxInfo>";
			$ss .= "<QueryResultForRecvTask>";
			$ss .= "<Login>";
			$ss .= "<UserID>10217112</UserID>";//用户��?
			$ss .= "<Password>000000</Password>";
			$ss .= "</Login>";
		  $ss .= "<FaxNumberListFilter>$faxNumber</FaxNumberListFilter>";
			$ss .= "<StartTimeFilter>$startTime</StartTimeFilter>";
			$ss .= "<EndTimeFilter>$endTime</EndTimeFilter>";
			$ss .= "</QueryResultForRecvTask>";
			$ss .= "</FaxInfo>";
			return $ss;

		}



		public function sendResultXmlStr($clientTaskId,$jobNoList,$startTime,$endTime){
			$ss  = '';
			$ss .= "<?xml version=\"1.0\" encoding=\"gb2312\"?>";
			$ss .= "<FaxInfo>";
			$ss .= "<QueryResultForSendTask>";
			$ss .= "<Login>";
			$ss .= "<UserID>$this->UserID</UserID>";
			$ss .= "<Password>$this->Password</Password>";
			$ss .= "</Login>";
			$ss .= "<FaxClientIDListFilter>$clientTaskId</FaxClientIDListFilter>";//客户端TaskID
			$ss .= "<JobNoListFilter>$jobNoList</JobNoListFilter>";//作业号
			$ss .= "<StartTimeFilter>$startTime</StartTimeFilter>";
			$ss .= "<EndTimeFilter>$endTime</EndTimeFilter>";
			$ss .= "</QueryResultForSendTask>";
			$ss .= "</FaxInfo>";
			return $ss;
		}

		function xmlToArray($xml){
        $ret = array();
        if($xml instanceOf SimpleXMLElement){
            $xmlDoc = $xml;
        }
        else{
            $xmlDoc = simplexml_load_string($xml, 'SimpleXMLIterator');
            if(!$xmlDoc){      // xml字符串格式有问题
                return null;
            }
        }

        for($xmlDoc->rewind(); $xmlDoc->valid(); $xmlDoc->next()){
            $key = $xmlDoc->key();       // 获取标签��?
            $val = $xmlDoc->current();   // 获取当前标签
            if($xmlDoc->hasChildren()){     // 如果有子元素
                $ret[$key] = xmlToArray($val);  // 子元素变量递归处理返回
            }
            else{
                $ret[$key] = (string)$val;
            }
        }
        return $ret;
    }
		function simplest_xml_to_array($xmlstring) {
    return json_decode(json_encode((array) simplexml_load_string($xmlstring)), true);
}


}
