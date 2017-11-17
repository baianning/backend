<?php
class Alipayrefund
{
    public function alipayrefundApi($batch_no, $batch_num, $detail_data)
    {
        header("Content-type:text/html;charset=utf-8");
        require_once("lib/alipay_submit.class.php");
        //构造要请求的参数数组，无需改动
        $config = aplipayconf();
        $config['cacert']    = getcwd().'\ThinkPHP\Library\Vendor\alipayrefund\cacert.pem';
        //print_r($config['cacert']);die;
        $parameter = array(
            "service" => "refund_fastpay_by_platform_pwd",
            "partner" => trim($config['partner']),
            "notify_url"	=> 'http://bj.tripg.com/index.php/Home/PayAction/alipayrefusehandle',
            "seller_user_id"	=> trim($config['partner']),
            "seller_email"	=> trim($config['seller_email']),
            "refund_date"	=> date('Y-m-d H:i:s',time()),
            "batch_no"	=> $batch_no,
            "batch_num"	=> $batch_num,
            "detail_data"	=> $detail_data,
            "_input_charset"	=> trim(strtolower($config['input_charset']))	
        );
        //print_r($parameter);die;
        //建立请求
        $alipaySubmit = new AlipaySubmit($config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        echo $html_text;
    }
}

?>