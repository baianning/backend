<?php

namespace Home\Model;  //给当前文件规划命名目录
use Think\Model;		//引入对应命名文件



class UserModel extends Model {
    protected $tableName = 'user';

    //添加
    function addData($data){
        $exist = $this->where("username='{$data['username']}'")->find();
        if($exist){
            return 'no';
        }
        $res = $this->add($data);
        if($res){
            $data1 = [
                'uid'=>$res,
                'group_id'=>$data['role'],
            ];

            M('auth_group_access')->add($data1);
            return "ok";
        }else{
            return $res->getDbError();
        }
    }

    //修改
    function upData($data){
        $res = $this->where("id={$data['id']}")->find();
        if(!$res){
            return "非法操作";
        }
        $result = $this->where("id={$data['id']}")->save($data);
        if($result>=0){
            $role =  M('auth_group_access')->where("uid = {$data['id']}")->select();
            if($role){
                $data1 = [
                    'group_id'=>$data['role'],
                ];
                M('auth_group_access')->where("uid = {$data['id']}")->save($data1);
            }else{
                $data1 = [
                    'group_id'=>$data['role'],
                    'uid'=>$data['id'],
                ];

                M('auth_group_access')->add($data1);
            }


            return "ok";
        }else{
            return $this->getDbError();
        }
    }

    function updatePass($data){
        $userInfo = $this->where("id={$data['uid']}")->find();
        if(empty($userInfo)){
            return '0';
        }
        if($data['oldpass']!=$userInfo['password']){
            return  '1';
        }
        $res = $this->where("id={$data['uid']}")->setField('password',$data['password']);
        if($res>=0){
            return 'ok';
        }else{
            return $this->getDbError();
        }
    }

    function _before_insert(&$data,$option){
        $data['ctime']=time();
        $data['utime']=time();
        $data['is_del']=0;

    }

    function _before_update(&$data,$option){
        $data['utime']=time();
    }


    /**
     * 自动验证
     * self::EXISTS_VALIDATE 或者0 存在字段就验证（默认）
     * self::MUST_VALIDATE 或者1 必须验证
     * self::VALUE_VALIDATE或者2 值不为空的时候验证
     */
    protected $_validate = array(
        array('username', 'require', '用户名不能为空！'), //默认情况下用正则进行验证
        // 正则验证密码 [需包含字母数字以及@*#中的一种,长度为6-22位]
        array('password', '/^([a-zA-Z0-9@*#]{6,22})$/', '密码格式不正确,请重新输入！', 0),
        array('repassword', 'password', '确认密码不正确', 0, 'confirm'), // 验证确认密码是否和密码一致
        array('phone', '/^1[34578]\d{9}$/', '手机号码格式不正确', 0), // 正则表达式验证手机号码
    );





}

