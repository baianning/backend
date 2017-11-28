<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller {
    public function login(){
        // 判断提交方式
        if (IS_POST) {
            // 实例化Login对象
            $data = I("post.");
            $login = D('admin');

            $where = array();
            $where['username'] = $data['username'];
            $result = $login->where($where)->field('id,username,password,randnum')->find();
            $password = md5(md5($data['password']) . $result['randnum']);
            // 验证用户名 对比 密码
            if ($result && $result['password'] == $password) {
                session("admin_id",$result['id']);
                session("username",$result['username']);
                $this->success('登录成功，跳转到系统页面中。。。', U('index/index'));
            } else {
                $this->error('登录失败,用户名或密码不正确!');
            }
        }else{
            $this->display();
        }
    }
    public function login_out(){
        session(null);
        $this->success('退出成功，跳转到系统页面中。。。', U('login/login'));
    }
}