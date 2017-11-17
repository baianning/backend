<?php
/**
 *酒店调用数据类
 *@author 宿伟
*/
class Hotelapi {
   static $Basic = ['Usercd'=>'SZ28276','Authno'=>'123456'];
	/*
	* 获取酒店ID
	* param $data Array 数据结构体
	* param $flag string 标识（select：查询 order：下单）
	* return Json
	*/
  	static public function gethotelID($data=array(),$flag="select") {
  		if(empty($data)){
  			return renderJson(array(),'参数有误');
  		}
      $Basic = array_merge($data,self::$Basic);
  		$data = json_encode($Basic,true);
  		$result = Hotelapi::getCurl($data,$flag);
  		return $result;
  	}

  	/*
	* 获取捷旅酒店价格/房态/预订条款/修改条款
	* param $data Array 数据结构体
	* param $flag string 标识（select：查询 order：下单）
	* return Json
	*/
  	static public function getNewHotelInfo($data=array(),$flag="select") {
  		if(empty($data)){
  			return renderJson(array(),'参数有误');
  		}


  		$Basic = array_merge($data,self::$Basic);
      $data = json_encode($Basic,true);
      //echo $data;die;
  		$result = Hotelapi::getCurl($data,$flag);
      $result=json_decode($result,true);
      $returnData = array();
      foreach($result['data'] as $key=>$val){
        $returnData[] = [
          'hoteltgid'=>$val['hotelId'],
          'hotelname'=>$val['hotelName'],
          'layout'   =>$val['roomtypeId'],
          'layout_name' =>$val['roomtypeName'],
            'roomPriceDetail'=>$val['roomPriceDetail'],
        ];

        /*foreach($val['roomPriceDetail'] as $k => $val){
            $returnData[$key]['roomPriceDetail'][$k] = [
                'advance_days'=>$val['advancedays'],
                'with_room'=>$val['allotmenttype'],  //配额类型
                'appointeddate'=>$val['appointeddate'],   //指定日期
                'day_psale'=>$val['businessprice'], //销售价

                'cashscaletype'=>$val['cashscaletype'], //tiaokuan
                'currency'=>$val['currency'], //金币类型
                'facepaytype'=>$val['facepaytype'], //现付类型
                'faceprice'=>$val['faceprice'], //现付价格
                'break_num'=>$val['includebreakfastqty2'],
                'internetprice'=>$val['internetprice'], //宽带 wifi
                'ishousing'=>$val['ishousing'], //是否甩房
                'lastupdatepricetime'=>$val['lastupdatepricetime'], //更新时间
                'netcharge'=>$val['netcharge'], //宽带收费
                'updatetime'=>$val['updatetime'], //更新时间
                'preeprice'=>$val['preeprice'],  //同行价
                'sett_type '=>$val['pricingtype '],  //支付类型
                'qtyable'=>$val['qtyable'], //当前可销售房间数量
                'is_break'=>$val['ratetypename'], //宽带收费
                'layout_id'=>$val['roomtypeid'], //宽带收费
                'layout_name'=>$val['roomtypename'], //宽带收费
                'supplierid'=>$val['supplierid'], //供应商id
                'timeselect'=>$val['advance_days'], //宽带收费
            ];
        }*/
      }


  		return $returnData;
  	}

  	/*
  	* 变价通知提示
	* param $data Array 数据结构体
	* param $flag string 标识（select：查询 order：下单）
	* return Json
	*/
  	static public function getHotelPricelist($data=array(),$flag="select"){
  		if(empty($data)){
  			return renderJson(array(),'参数有误');
  		}
  		$data = json_encode($data,true);
  		$result = Hotelapi::getCurl($data,$flag);
  		return $result;
  	}

  	/*
	* 获取捷旅最新价格代码列表
	* param $data Array 数据结构体
	* param $flag string 标识（select：查询 order：下单）
	* return Json
	*/
  	static public function gethotelNewPrice($data=array(),$flag="select") {
  		if(empty($data)){
  			return renderJson(array(),'参数有误');
  		}
  		$data = json_encode($data,true);
  		$result = Hotelapi::getCurl($data,$flag);
  		return $result;
  	}

  	/*
	* 获取酒店信息
	* param $data Array 数据结构体
	* param $flag string 标识（select：查询 order：下单）
	* return Json
	*/
  	static public function gethotelInfo($data=array(),$flag="select") {
  		if(empty($data)){
  			return renderJson(array(),'参数有误');
  		}

  		$Basic = array_merge($data,self::$Basic);
        $data = json_encode($Basic,true);
  		$result = Hotelapi::getCurl($data,$flag);
  		$result=json_decode($result,true);
         /* echo "<pre>";
          print_r($result);die;*/
  		$returnData = array();
  		 foreach($result['data'] as $key=>$val){
  		 	$returnData[] = [
  				'hoteltgid'=>$val['hotelid'],
  				'hotelname'=>$val['namechn'],
                'hotelenname'=>$val['nameeng'],
                'email'=>$val['email'],

                'province_id'=>$val['state'],
  				'cityid'=>$val['city'],
  				'sectionId'=>$val['zone'],
                'bizsectionid'=>$val['bd'],

                'address'=>$val['addresschn'],
                'starratedid'=>$val['star'],
                'hotelstatus'=>$val['active'],
                'insideremark'=>$val['interiornotes'],
                'serviceItemid'=>implode(',',explode('|',$val['facilities'])),
                'longitude'=>$val['jingdu'],
                'latitude'=>$val['weidu'],
                'introducechn'=>$val['introducechn'],
                'centraltel'=>$val['centraltel'],
                'hotelnumber'=>$val['floor'],
                'fax'=>$val['fax'],
                'outsideremark'=>$val['outeriornotes'],
                'createtime'=>strtotime($val['createtime']),
                'rooms'=>$val['rooms'],
  			];
        /*    foreach($val['rooms'] as $k => $val){
                $returnData[$key]['room'][$k] = [
                    'layout_name'=>$val['namechn'],
                    'use_area'=>$val['acreages'],  //客房面积
                    'roomqty'=>$val['roomqty'],   //房间数量
                    'bedqty'=>$val['bedqty'], //房间床数量

                    'is_break'=>$val['allowaddbed'],
                    'allowaddbedqty'=>$val['allowaddbedqty'], //加床数量
                    'allowaddbedsize'=>$val['allowaddbedsize'], //许寸加允床尺
                    'nosm'=>$val['nosm'], //该房型有无无烟房
                    'in_floor'=>$val['floordistribution'],
                    'nettype'=>$val['nettype'], //宽带还是拨号
                    'roomfacid'=>$val['roomfacilities'],
                    'room_text'=>$val['remark2'],
                    'remark'=>$val['remark'], //房型设施
                    'updatetime'=>$val['updatetime'], //更新时间
                    'active'=>$val['active'],  //是否生效
                    'layoutid'=>$val['roomtypeid']
                ];
             }*/
  		 }
  		return $returnData;
  	}

  	/*
	*下单前检验最新价格、房态接口
	* param $data Array 数据结构体
	* param $flag string 标识（select：查询 order：下单）
	* return Json
  	*/
  	static function checklatestPrice($data=array(),$flag="select"){
  		if(empty($data)){
  			return renderJson(array(),'参数有误');
  		}

  		$result = Hotelapi::getCurl($data,$flag);
  		return $result;
  	}


  	/*
	* 新增订单
	* param $data Array 数据结构体
	* param $flag string 标识（select：查询 order：下单）
	* return Json
  	*/
  	static function newOrder($data=array(),$flag="order"){
  		if(empty($data)){
  			return renderJson(array(),'参数有误');
  		}
  		header("Content-type:text/xml");
  		$data = arrayToXml($data);
  		$result = Hotelapi::getCurl($data,$flag);
  		return $result;
  	}

  	/*
	* 取消订单s
	* param $data Array 数据结构体
	* param $flag string 标识（select：查询 order：下单）
	* return Json
  	*/
  	static function cancelOrder($data=array(),$flag="order"){
  		if(empty($data)){
  			return renderJson(array(),'参数有误');
  		}
  		header("Content-type:text/xml");
  		$data = arrayToXml($data);
  		$result = Hotelapi::getCurl($data,$flag);
  		return $result;
  	}

  	/*
	* 获取订单状态
	* param $data Array 数据结构体
	* param $flag string 标识（select：查询 order：下单）
	* return Json
  	*/
  	static function getOrderStatus($data=array(),$flag="order"){
  		if(empty($data)){
  			return renderJson(array(),'参数有误');
  		}
  		header("Content-type:text/xml");
  		$data = arrayToXml($data);
  		$result = Hotelapi::getCurl($data,$flag);
  		return $result;
  	}

  	/*
	* 获取屏蔽酒店ID
	* param $data Array 数据结构体
	* param $flag string 标识（select：查询 order：下单）
	* return Json
  	*/
  	static function checkShieldId($data=array(),$flag="select"){
  		if(empty($data)){
  			return renderJson(array(),'参数有误');
  		}
  		$result = Hotelapi::getCurl($data,$flag);
  		return $result;
  	}



  	/*
	* 获取促销信息
	* param $data Array 数据结构体
	* param $flag string 标识（select：查询 order：下单）
	* return Json
  	*/
  	static function cuxiaoxinxi($data=array(),$flag="select"){
  		if(empty($data)){
  			return renderJson(array(),'参数有误');
  		}
  		$result = Hotelapi::getCurl($data,$flag);
  		return $result;
  	}

  	/*
	* 获取加床价格信息
	* param $data Array 数据结构体
	* param $flag string 标识（select：查询 order：下单）
	* return Json
  	*/
  	static function jiachuangxinxi($data=array(),$flag="select"){
  		if(empty($data)){
  			return renderJson(array(),'参数有误');
  		}
  		$result = Hotelapi::getCurl($data,$flag);
  		return $result;
  	}

  		/*
	* 获取加早价格信息
	* param $data Array 数据结构体
	* param $flag string 标识（select：查询 order：下单）
	* return Json
  	*/
  	static function zaocanxinxi($data=array(),$flag="select"){
  		if(empty($data)){
  			return renderJson(array(),'参数有误');
  		}
  		$result = Hotelapi::getCurl($data,$flag);
  		return $result;
  	}




  	//curl公共方法
  	private function getCurl($data=array(),$flag="select"){
  		if($flag=='select'){
  			$url = "http://58.250.56.213:8061/common/service.do";
  		}elseif($flag=="order"){
  			$url = "http://58.250.56.213:8061/common/orderService.do";
  		}
  		$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    	// post数据
    	curl_setopt($ch,CURLOPT_POST,1);
    	// post的变量
    	curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
    	$output = curl_exec($ch);
    	curl_close($ch);
    	return $output;
  	}

}