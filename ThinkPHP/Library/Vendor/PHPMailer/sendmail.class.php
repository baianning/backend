<?php

include_once "class.phpmailer.php";
include_once "class.smtp.php";

class sendmail {
    /*
     * 发送邮件方法,$body要发送的内容,$addressNumber收件人的地址
     */
    public function sendmsg($body, $addressNumber) {
        //获取一个外部文件的内容 
        $mail = new PHPMailer();
//        $body = "<h1>this is success send!</h1>";
        //设置smtp参数 
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPKeepAlive = true;
        $mail->Host = "smtp.163.com";
        $mail->Port = 25;
        //填写你的email账号和密码 
        $mail->Username = "hote@tripglobal.cn";
        $mail->Password = "Hote88691111"; #注意这里要填写授权码就是我在上面网易邮箱开启SMTP中提到的，不能填邮箱登录的密码哦。 //设置发送方，最好不要伪造地址 
        $mail->From = "hote@tripglobal.cn";
        $mail->FromName = "tripglobal";
        $mail->Subject = "北京差旅天下用房预订单";
        $mail->AltBody = $body;
        $mail->WordWrap = 50; // set word wrap 
        $mail->MsgHTML($body); //设置回复地址 
        $mail->AddReplyTo("hote@tripglobal.cn", "北京差旅天下"); 
        ////添加附件，此处附件与脚本位于相同目录下否则填写完整路径 //附件的话我就注释掉了 
        #$mail->AddAttachment("attachment.jpg");
        #$mail->AddAttachment("attachment.zip"); 
        //设置邮件接收方的邮箱和姓名 
        $mail->AddAddress($addressNumber, "tp"); 
        //使用HTML格式发送邮件 
        $mail->IsHTML(true); //通过Send方法发送邮件 //根据发送结果做相应处理 
        if (!$mail->Send()) {
            return "Mailer Error:" . $mail->ErrorInfo;
        } else {
            return "1";
        }
    }

}

?>
