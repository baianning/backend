<?php
namespace Admin\Controller;
use Think\Controller;
class AdminController extends Controller {
    //管理员列表
    public function admin_list(){
        $param = I("post.");
        $admin = M("admin");
        $list = $admin->select();
        $this->assign("list",$list);
        $this->display();
    }
    //添加管理员---显示页面
    public function admin_add(){
        $this->display();
    }
    //添加管理员---操作页面
    public function admin_do_add(){
        $param = I("post.");
        $randnum = make_password(6);
        $password = md5(md5($param['password']).$randnum);
        $data = [
            "username"=>$param['username'],
            "head_pic"=>'',
            "password"=>$password,
            "randnum"=>$randnum,
            "phone"=>$param['phone'],
            "email"=>$param['email'],
            "grade"=>$param['grade'],
            "introduce"=>$param['introduce'],
            "ctime"=>time(),
            "utime"=>time()
        ];
        $res = M("admin")->add($data);

    }
}