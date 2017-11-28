<?php
namespace Admin\Controller;
use Think\Controller;
class AdminController extends PublicController {
    //管理员列表
    public function admin_list(){
        $param = I("");
        $showNum = I("showNum",10);
        $page = I("page",0);
        $username = $param['username'];
        $where = "1=1 and is_del = 0";
        if( $param['start'] ){
            $where.= " and ctime >= ".strtotime($param['start']);
        }
        if( $param['end'] ){
            $where.= " and ctime <= ".strtotime($param['end']);
        }
        if( $param['username'] ){
            $where.= " and username = '$username'";
        }
        if( $param['grade'] ){
            $where.= " and grade = ".$param['grade'];
        }
        $admin = M("admin");
        $url = U('News/news_list') . "?start=" . $param['start']. "&end=" . $param['end']. "&grade=" . $param['grade']."&";
        $total = $admin->where($where)->order("ctime DESC")->count();
        $pageary = pagination($total, $showNum, $page,$url);
        $list = $admin->where($where)->order("ctime DESC")->limit($pageary['offset'], $showNum)->select();
        $this->assign("list",$list);
        $this->assign("param",$param);
        $this->assign("pageary",$pageary);
        $this->display();
    }
    //添加管理员---显示页面
    public function admin_add(){
        $this->display();
    }
    //添加管理员---操作页面
    public function admin_do_add(){
        $param = I("");
        $admin = M("admin");
        $username = $param['username'];
        $admin_user = $admin->where("username='$username'")->find();
        if( $admin_user ) echo renderJson("","用户名已存在",1);
        $randnum = make_password(6);
        $password = md5(md5($param['password']).$randnum);
        $data = [
            "username"=>$param['username'],
            "head_pic"=>$param['head_pic'],
            "password"=>$password,
            "randnum"=>$randnum,
            "phone"=>$param['phone'],
            "email"=>$param['email'],
            "grade"=>$param['grade'],
            "introduce"=>$param['introduce'],
            "ctime"=>time(),
            "utime"=>time()
        ];
        $res = $admin->add($data);
        if( $res ){
            echo renderJson("","",0);
        }
        echo renderJson("","",1);
    }
    //修改管理员信息 ---显示页面
    public function admin_edit(){
        $admin_id = I("post.admin_id");
        if( $admin_id ){
            $data = M("admin")->where("id =".$admin_id)->find();
        }else{
            $data = array();
        }
        $this->assign("data",$data);
        $this->display();
    }
    //修改管理员信息 ---操作页面
    public function admin_do_edit(){
        $param = I("post.");
        $admin = M("admin");
        $username = $param['username'];
        $id = $param['id'];
        $admin_user = $admin->where("username='$username' and id <> $id")->find();
        if( $admin_user ) echo renderJson("","用户名已存在",1);

        $admin_detial = $admin->where("id=".$param['id'])->find();
        if( !$admin_detial ) echo renderJson("","",3);

        $randnum = make_password(6);
        $password = md5(md5($param['password']).$randnum);
        if( $param['password'] == $admin_detial['password'] ){
            $password = $admin_detial['password'];
            $randnum = $admin_detial['randnum'];
        }
        $data = [
            "username"=>$param['username'],
            "head_pic"=>'',
            "password"=>$password,
            "randnum"=>$randnum,
            "phone"=>$param['phone'],
            "email"=>$param['email'],
            "grade"=>$param['grade'],
            "introduce"=>$param['introduce'],
            "utime"=>time()
        ];

        $res = M("admin")->where("id=".$param['id'])->save($data);
        if( $res > 0 || $res !== false ){
            echo renderJson("","",0);
        }
        echo renderJson("","",1);
    }
    //删除管理员
    public function admin_del(){
        $admin_ids = I("post.ids");
        if( !$admin_ids ) echo renderJson("","参数错误",1);
        $ids = $admin_ids;
        if( is_array($admin_ids) ){
            $ids = implode(',',$admin_ids);
        }
        $data = [
            "is_del"=>1,
            "utime"=>time()
        ];
        $res = M("admin")->where("id in(".$ids.")")->save($data);
        if( !$res ) echo renderJson("","操作失败",1);
        echo renderJson("","操作成功",0);
    }
}