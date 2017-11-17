<?php
include_once('35fax/cls_nusoap.php');
$soap = new nusoap_client("http://202.22.252.221/servlet/services/SmsService");
$soap->soap_defencoding = "GBK";
$soap->decode_utf8 = false;
echo 'eee';
$UserID='35001211';
$Password='0000010';
$str=getSendSmsXMLstr($UserID,$Password,'13918752992','独立的我的测试短信,并返回结果');  //如果页面时utf8，需要转换成gbk字符
$params=array('arg0'=>$str);
$soap->call("SendSmsToServer",$params,'http://axis2.fax.uniproud.com');
$responseData=$soap->responseData;
//print_r($soap); echo '<br>---------<br>';
//返回ErrorFlag的结果
$result=substr($responseData,strpos($responseData,'&lt;ErrorFlag>')+14,strpos($responseData,'&lt;/ErrorFlag>')-strpos($responseData,'&lt;ErrorFlag>')-14);
print_r($result);
exit;


//获取发送短信消息体
function getSendSmsXMLstr($UserID,$Password,$mobile,$message)
{
	$ss='';
	$ss .= "<?xml version=\"1.0\" encoding=\"gb2312\"?>";
	$ss .= "<SmsInfo>";
	$ss .= "<Login>";
	$ss .= "<UserID>$UserID</UserID>";//用户名
	$ss .= "<Password>$Password</Password>";//密码
	$ss .= "</Login>";
	$ss .= "<SendTaskList>";
	$ss .= "<SendTask>";
	$ss .= "<ClientTaskID>1</ClientTaskID>";//int型，查询清单接口中短信系统会返回此字段，短信系统不效验唯一性
	$ss .= "<SmsNumber>$mobile</SmsNumber>";//短信号码.
	$ss .= "</SendTask>";
	$ss .= "</SendTaskList>";
	$ss .= "<SmsOptions>";
	$ss .= "<Priority>1</Priority>";//Priority 优先级.1-低,2-高 （非必填项，系统默认 1）
	$ss .= "<Content>$message</Content>";//短信内容
	$ss .= "</SmsOptions>";
	$ss .= "</SmsInfo>";

	return $ss;
}
?>