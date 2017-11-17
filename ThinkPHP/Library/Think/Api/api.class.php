<?php
/**
 *酒店/机票调用数据类
 *@return json
 *@author 李超 <18810240401@163.com> 
*/
class Api {
   /**
    *返回酒店数据方法
   */         
  static function returnapi($str,$data_url,$flag=1){
    switch ($flag) {
      case 1:
        //获取酒店秘钥数据
        $data = Api::EncryptionString($str);
        $url = "http://139.210.99.29:8007/$data_url?$data";
        break;
      case 2:
         //获取机票秘钥数据
        $data = Api::AirportEncryptionString($str);
        $url = "http://service.tripglobal.cn:8769/$data_url?$data";
        break;
        case 3:
         //去哪儿测试
        $data = Api::EncryptionString($str);
        $url = "http://a.tripg.com/$data_url?$data";
//        var_dump($url);die;
        break;
      default:
        $data = "无效请求";
        break;
    }
    //curl 模拟get提交
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL,$url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    curl_close($curl);
    return $data;
  }

    static function returnapipost($str,$data_url,$flag=3){
        switch ($flag) {
            case 1:
                //获取酒店秘钥数据
                $data = Api::EncryptionString($str);
                $url = "http://139.210.99.29:8007/$data_url?$data";
                break;
            case 2:
                //获取机票秘钥数据
                $data = Api::AirportEncryptionString($str);
                $url = "http://service.tripglobal.cn:8769/$data_url?$data";
                break;
            case 3:
                //去哪儿测试
                $data = Api::EncryptionString($str);
                $url = "http://a.api.tripg.com/$data_url";
                break;
            default:
                $data = "无效请求";
                break;
        }
        //curl 模拟POST提交
        $ch = curl_init();
        $res= curl_setopt ($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        $result = curl_exec ($ch);
        curl_close($ch);
        return $result;
    }

    /**
    *
    *
    *获取秘钥数据方法
    */
  static function EncryptionString($str){
    //时间参数
    $time = time();
    //参数字符串
    $str = $str.'&TimeStamp='.$time.'&Sign=5974AE4C-66FE-42F2-986F-B2D20E64068D';
    //获取newkey 
    $NewKey = Api::NewKey($str.'&Key=FE16ADE7-B5AF-4CE4-9678-5D8A5643A44C');
    //将newkey拼接到参数里面
    $str .= '&NewKey='.$NewKey;

    return $str;
  }

  static function AirportEncryptionString($str){
    //时间参数
    $time = time();
    //参数字符串
    $str = $str.'&TimeStamp='.$time.'&Sign=FE29D133-468D-403B-8428-0168C968CAC1';
    //获取newkey 
    $NewKey = Api::NewKey($str.'&Key=A640D68F-5CBF-4450-BA65-62C8DFA6DE4F');
    //将newkey拼接到参数里面
    $str .= '&NewKey='.$NewKey;

    return $str;
  }
  /**
  *数据签名 str 参数拼串
  */
  static function NewKey($str){
      //将字符串变成数组
      $strArray = explode("&",$str);
      //将数组进行排序
      sort($strArray);
      $NewKey = '';
      foreach($strArray as $key => $val){
        $NewKey .= $val;
      }
      //拼接万加密
      $NewKey = md5($NewKey);

      return $NewKey;
  }

}