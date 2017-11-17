<?php
namespace Web\Controller;
use Think\Controller;
class CreateUrlController extends RegcoController {

    /**
     * 初始化
     */
    public function _initialize()
    {
        //引入WxPayPubHelper
        vendor('WxPay.WxPayPubHelper');
    }

    public function createQrcode()
    {
        //使用统一支付接口
        $unifiedOrder = new \UnifiedOrder_pub();
        
        //设置统一支付接口参数
        //设置必填参数
        //appid已填,商户无需重复填写
        //mch_id已填,商户无需重复填写
        //noncestr已填,商户无需重复填写
        //spbill_create_ip已填,商户无需重复填写
        //sign已填,商户无需重复填写
        $price = I("price") * 100;
        $desc = I("desc");
        $order_num = I("order_num");
        $unifiedOrder->setParameter("body","$desc");//商品描述
        //自定义订单号，此处仅作举例
        /*$timeStamp = time();
        $out_trade_no = C('WxPayConf_pub.APPID')."$timeStamp";*/
        $unifiedOrder->setParameter("out_trade_no","$order_num");//商户订单号 
        $unifiedOrder->setParameter("total_fee","$price");//总金额
        $unifiedOrder->setParameter("notify_url", "".$_SERVER['HTTP_HOST']."/index.php/Web/CreateUrl/notify");//通知地址 
        $unifiedOrder->setParameter("trade_type","NATIVE");//交易类型
        //非必填参数，商户可根据实际情况选填
        //$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号  
        //$unifiedOrder->setParameter("device_info","XXXX");//设备号 
        //$unifiedOrder->setParameter("attach","XXXX");//附加数据 
        //$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
        //$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间 
        //$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记 
        //$unifiedOrder->setParameter("openid","XXXX");//用户标识
        //$unifiedOrder->setParameter("product_id","XXXX");//商品ID
        /*echo "<pre>";
        print_r($unifiedOrder);exit();*/
        //获取统一支付接口结果
        $unifiedOrderResult = $unifiedOrder->getResult();
        
        //商户根据实际情况设置相应的处理流程
        if ($unifiedOrderResult["return_code"] == "FAIL") 
        {
            //商户自行增加处理流程
            echo "通信出错：".$unifiedOrderResult['return_msg']."<br>";
        }
        elseif($unifiedOrderResult["result_code"] == "FAIL")
        {
            //商户自行增加处理流程
            echo "错误代码：".$unifiedOrderResult['err_code']."<br>";
            echo "错误代码描述：".$unifiedOrderResult['err_code_des']."<br>";
        }
        elseif($unifiedOrderResult["code_url"] != NULL)
        {
            //从统一支付接口获取到code_url
            $code_url = $unifiedOrderResult["code_url"];
            //商户自行增加处理流程
            //......
        }
        //$info = $this -> dock_Server_pro();

        $data = array(
                    //'info' => $info,
                    'out_trade_no' => $out_trade_no,
                    'code_url' => $code_url,
                    'unifiedOrderResult' => $unifiedOrderResult,
                    'code' => 200,
            );
        //echo json_encode($data);
        $this->assign('out_trade_no',$out_trade_no);
        $this->assign('code_url',$code_url);
        $this->assign('unifiedOrderResult',$unifiedOrderResult);
        
        $this->display('qrcode');
    }

    public function notify()
    {
        //使用通用通知接口
        $notify = new \Notify_pub();
         
        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $notify->saveData($xml);
         
        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if($notify->checkSign() == FALSE){
            $notify->setReturnParameter("return_code","FAIL");//返回状态码
            $notify->setReturnParameter("return_msg","签名失败");//返回信息
        }else{
            $notify->setReturnParameter("return_code","SUCCESS");//设置返回码
        }
        $returnXml = $notify->returnXml();
        echo $returnXml;
         
        //==商户根据实际情况设置相应的处理流程，此处仅作举例=======
         
        //以log文件形式记录回调信息
        //         $log_ = new Log_();
        $log_name= __ROOT__."/Public/notify_url.log";//log文件路径
         
        $this->log_result($log_name,"【接收到的notify通知】:\n".$xml."\n");
         
        if($notify->checkSign() == TRUE){
            if ($notify->data["return_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作
                $info = M('order')->where(array('order_num'=>$out_trade_no))->find();
                log_result($log_name,"【通信出错】:\n".$xml."\n");
                if($info && $info['pay_statu'] == 101){
                    $data['statu'] = 102;
                    $data['updatetime'] = date('Y-m-d H:i:s');
                    if(M('order')->where(array('order_num'=>$out_trade_no))->data($data)->save()){
                        echo $obj->ToResultWeixin(array('return_code'=>'SUCCESS','return_msg'=>'更新订单成功'));
                    }
                }
            }
             
            //商户自行增加处理流程,
            //例如：更新订单状态
            //例如：数据库操作
            //例如：推送支付完成信息
        }
    }
}