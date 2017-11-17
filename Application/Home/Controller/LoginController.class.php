<?php
/*
  * @author:王静
  * @date:2016/11/28
*/
namespace Home\Controller;

use Think\Controller;
class LoginController extends Controller
{

    //显示登录页面
    public function Login_show(){
        die;
        header("Content-type: text/html; charset=utf-8");
        $news = M("news");
        $time = time();
        for($i=0;$i<30;$i++){
            $data[] = [
                "title"=>"标题".($i+1),
                "content"=>"内容".($i+1),
                "admin_id"=>1,
                "ctime"=>$time,
                "utime"=>$time,
                "grade"=>3
            ];
            $time += 60;
        }
//        var_dump($data);die;
        $res = $news->addAll($data);
        var_dump($res);die;
    }
    //登录操作
    public function Login()
    {  // 判断提交方式
        if(!IS_POST) echo renderJson("","参数错误",3);
        $phone = I("post.phone");
        $Vcode = I("post.Vcode");

/*        $phone = '13522807353';
        $Vcode = '1683';*/
        $result = validate_phone($phone);
        if( !$result ) echo renderJson("","手机号格式有误",3);
        $resvcode = M("vcode")->where("vcode=".$Vcode)->find();
        if( !$resvcode ) echo renderJson("","验证码有误",1);//判断验证码是否正确
        if( ( time() - $resvcode['ctime'] ) >3000000 ) echo renderJson("","验证码已过期",3);//判断验证码是否正确

        $user = M("user");
        $res = $user->where("phone='".$phone."'")->find();
        if( !$res ){
            $data = [
                "phone"=>$phone,
                "ctime"=>time(),
                "utime"=>time()
            ];
            $newid =$user->add($data);
            if( $newid ){
                session("uid",$newid);
                session("phone",$phone);
                $datas = [
                    "first_time"=>1,
                    "uid"=>$newid,
                    "nickname"=>'',
                    "head_pic"=>''
                ];

                echo renderJson($datas,"登录成功",0);
            }else{
                echo renderJson("","登录失败",1);
            }
        }else{
            session("uid",$res['uid']);
            session("phone",$res['phone']);
            $datas = [
                "uid"=>$res['uid'],
                "nickname"=>$res['nickname'],
                "head_pic"=>$res['head_pic'],
                "sign"=>$res['sign']
            ];
            if( $res['nickname']!='' ){
                $datas['first_time'] = 0;
                echo renderJson($datas,"登录成功",0);
            }else{
                $datas['first_time'] = 1;
                echo renderJson($datas,"登录成功",0);
            }
        }
    }
    //完善资料
    public function perfect_datum(){
        if(!IS_POST) echo renderJson("","参数错误",3);
        $user = M('user');
        $uid = session("uid");
        addLogs(session());
        if( !$uid ) echo renderJson("","请先登录",1);
        $nickname = I("post.nickname");
        $head_pic = I("post.head_pic");
        $data = [
            "nickname"=>$nickname,
            "head_pic"=>$head_pic,
            "utime"=>time()
        ];
        $res = $user->where("uid=".$uid)->save($data);
        if( $res ){
            $data = $user->field("uid,phone,nickname,head_pic,sign")->where("uid=".$uid)->find();
            echo renderJson($data,"添加成功",0);
        }else{
            echo renderJson("","添加失败",1);
        }
    }
    //发送短信验证码
    public function sendSMS(){
        $tel = I("post.phone");
        $result = validate_phone($tel);//验证手机号是否正确
        if( !$result ) echo renderJson("","手机号格式有误",3);

        $res = sendSms($tel); // 发送验证码
        if( $res == 0 ){
            echo renderJson("","验证码发送成功",0);
        }else{
            echo renderJson("","验证码发送失败".$res,1);
        }

    }
    //校验 验证码
    public function validate_Vcode(){
        $Vcode = I("post.Vcode");

        $resvcode = M("vcode")->where("vcode=".$Vcode)->find();

        if( !$resvcode ) echo renderJson("","验证码有误",3);//判断验证码是否正确

        if( ( time() - $resvcode['ctime'] ) >300 ) echo renderJson("","验证码已过期",3);//判断验证码是否正确

        echo renderJson("","",0);
    }
    //生成二维码
    public function scerweima($url=''){
        Vendor("phpqrcode.phpqrcode");
        $value = $url;                  //二维码内容

        $errorCorrectionLevel = 'L';    //容错级别
        $matrixPointSize = 5;           //生成图片大小

        //生成二维码图片
        $filename = '/item/Public/Qrcode/'.microtime().'.png';
        \QRcode::png($value,$filename , $errorCorrectionLevel, $matrixPointSize, 2);

        $QR = $filename;                //已经生成的原始二维码图片文件

        $QR = imagecreatefromstring(file_get_contents($QR));

        //输出图片
        imagepng($QR, '/item/Public/qrcode.png');
        imagedestroy($QR);
        return '<img src="/item/Public/qrcode.png" alt="使用微信扫描支付">';
    }
    //退出登录
    public function Login_out(){
        session(null);
        echo renderJson("","退出成功",0);
    }
    //修改资料
    public function update_datum(){
        $sign = I("post.sign",'');
        $nickname = I("post.nickname",'');
        $head_pic = I("post.head_pic",'');
        if( !$sign && !$nickname && !$head_pic ){
            echo renderJson("","",0);
        }
        $uid = session("uid");
        if( !$uid ) echo renderJson("","请先登录",1);
        if( $sign ) $data['sign'] = $sign;
        if( $nickname ) $data['nickname'] = $nickname;
        if( $head_pic ) $data['head_pic'] = $head_pic;
        $res = M("user")->where("uid =".$uid)->save($data);
        if( $res !== false ){
            $return = [
                "nickname"=>$nickname,
                "head_pic"=>$head_pic,
                "sign"=>$sign
            ];
            echo renderJson($return,"修改成功",0);
        }
        echo renderJson("","修改失败",1);
    }
}