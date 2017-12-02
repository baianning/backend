<?php
namespace Admin\Controller;
use Think\Controller;
class NewsController extends PublicController {
    public function news_list(){

        $param = I("");
//        var_dump($param);die;
        $where = "1=1 and news.is_del = 0";
        if( $param['start'] ){
            $where.= " and news.ctime >= ".strtotime($param['start']);
        }
        if( $param['end'] ){
            $where.= " and news.ctime <= ".strtotime($param['end']);
        }
        if( $param['grade'] ){
            $where.= " and news.grade = ".$param['grade'];
        }
        if( $param['category_id'] ){
            $where.= " and re.cate_id = ".$param['category_id'];
        }
        $news = M("news");
        $showNum = I("showNum",10);
        $page = I("page",0);
        $url = U('News/news_list') . "?start=" . $param['start']. "&end=" . $param['end']. "&grade=" . $param['grade']."&";
        $total = $news->join("relevance re on news.news_id = re.news_id","LEFT")->join("admin on news.admin_id = admin.id","LEFT")->field("news.news_id,news.title,news.profile,news.source,news.times,news.status,admin.username,news.ctime,re.cate_id")->where($where)->order("news.utime DESC")->select();
        $total = count($total);
        $pageary = pagination($total, $showNum, $page,$url);
        $news_list = $news->join("relevance re on news.news_id = re.news_id","LEFT")->join("admin on news.admin_id = admin.id","LEFT")->field("news.news_id,news.title,news.profile,news.source,news.times,news.status,admin.username,news.ctime,re.cate_id")->where($where)->order("news.utime DESC")->limit($pageary['offset'], $showNum)->select();
        $newcate  = array();
        if( $news_list ){
            $category = get_cate();
            foreach( $category as $k=>$v ){
                $newcate[$v['id']] = $v['category'];
            }
            foreach ($news_list as $key=>&$val){
                $val['cate_id'] = $newcate[$val['cate_id']];
            }
        }

        $this->assign("news_list",$news_list);
        $this->assign("total",$total);
        $this->assign("pageary",$pageary);
        $this->assign("param",$param);
        $this->display();
    }
    public function news_add(){
        $category = get_cate();
        $this->assign("category",$category);
        $this->display();
    }
    public function news_do_add(){
        $param = I("");
        $admin_id = session("admin_id");
        $data = [
            "title"=>$param['title'],
            "profile"=>$param['profile'],
            "cover_img"=>$param['cover_img'],
            "content"=>$param['content'],
            "source"=>$param['source'],
            "grade"=>$param['grade'],
            "status"=>$param['status'],
            "admin_id"=>$admin_id,
            "ctime"=>time(),
            "utime"=>time()
        ];

        $res = M("news")->add($data);
        if( $res ){
            $redata = [
                "news_id"=>$res,
                "cate_id"=>$param['category_id'],
                "ctime"=>time()
            ];
            M("relevance")->add($redata);
            echo renderJson("","",0);
        }
        echo renderJson("","添加失败",1);
    }
    public function news_del(){
        $news_ids = I("post.news_ids");
        if( !$news_ids ) echo renderJson("","参数错误",1);
        $ids = $news_ids;
        if( is_array($news_ids) ){
            $ids = implode(',',$news_ids);
        }
        $data = [
            "is_del"=>1,
            "utime"=>time()
        ];
        $res = M("news")->where("news_id in(".$ids.")")->save($data);
        if( !$res ) echo renderJson("","操作失败",1);
        echo renderJson("","操作成功",0);
    }
    public function news_edit(){
        $news_id = I("news_id");
        if( !$news_id ) $this->error("参数错误");
        $news_detail = M("news")->where("news_id =".$news_id)->find();
        $news_detail['content'] = htmlspecialchars_decode($news_detail['content']);
        $news_detail['cate_id'] = M("relevance")->where("news_id=".$news_id)->getField("cate_id");
        $category = get_cate();
        $this->assign("category",$category);
        $this->assign("news_detail",$news_detail);
        $this->display();
    }
    public function news_do_edit(){
        $param = I();
        $data = array();
        $news = M("news");
        $detail = $news->where("news_id=".$param['news_id'])->find();
        $admin_id = session("admin_id");
        $data = [
            "title"=>$param['title'],
            "profile"=>$param['profile'],
            "content"=>$param['content'],
            "source"=>$param['source'],
            "grade"=>$param['grade'],
            "status"=>$param['status'],
            "admin_id"=>$admin_id,
            "utime"=>time()
        ];

        if( $param['cover_img']  ) $data['cover_img'] = $param['cover_img'];
        $res = $news->where("news_id=".$param['news_id'])->save($data);
        if( $res >0 || $res!==false ){
            $relevance = M("relevance");
            $rele = $relevance->where("news_id = {$param['news_id']} and cate_id = {$param['category_id']} and is_del = 0")->find();
            if( !$rele ){
                $data = [
                    "news_id"=>$param['news_id'],
                    "cate_id"=>$param['category_id'],
                    "ctime"=>time()
                ];
                $relevance->add($data);
            }
            echo renderJson("","",0);
        }
        echo renderJson("","修改失败",1);
    }
}