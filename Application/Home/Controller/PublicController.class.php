<?php
namespace Admin\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");
class PublicController extends Controller {
    //构造方法
    function __construct()
    {
        parent::__construct();
        $this->is_login();
    }
    //验证是否登录
    public function is_login()
    {
        /*        if (session('op_time') == "") {
                    echo "
                <script>
                    alert('您长时间为操作，请先登录');
                    parent.location.href='" . U('login/login') . "';
                    </script>";
                    exit;
                }*/

        if (session('admin_id') == "") {
            echo "
            <script>
            alert('您未登录，请先登录');
             parent.location.href='" . U('login/login') . "';
            </script>";
            exit;
        }
    }
}