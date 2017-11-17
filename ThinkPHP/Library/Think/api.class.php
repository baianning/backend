<?php
/**
 *酒店调用数据类
 *@return json
 *@author 李超 <18810240401@163.com> 
*/
class Api {
   /**
    *返回酒店数据方法
   */         
  static function returnapi($str){
    //获取秘钥数据
    $data = Api::EncryptionString($str);
    $url = "http://139.210.99.29:8007/HotelQunar/GetHotelComList?$data";
    //curl 模拟get提交
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL,$url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    curl_close($curl);
    echo $data;
  }
    /**
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