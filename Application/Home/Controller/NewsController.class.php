<?php
namespace Home\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");
class NewsController extends Controller {
    public function index(){
        $news = M("news");
        $user = M("user");
        $uid = session("uid");
        $category_id = I("post.category_id");
//        $category_id = 4;
        $page = I('post.page');
        $showNum = I('post.showNum');
        $showNum = 10;
        // 获取新闻类别
        $category = get_cate();
        if( !$category_id ) $category_id = 1;
        if( $uid ){
            $cate_sort = $user->where("uid=".$uid)->getField("cate_sort");
            if( $cate_sort ){
                $category = get_cate($cate_sort,1); //获取该用户的分类排序
                if( $category_id && $category_id !=1 ){ //获取用户分类排序的第一个分类ID
                    $sort = explode(',',$cate_sort);
                    $category_id = $sort[0];
                }
            }
        }
        //获取热门新闻
        $hotspot = $this->get_hotspot($showNum,$page);
        /*        $total = $news->count();
                $pageary = pagination($total, $showNum, $page);
                $hotspot = $news->field("news_id,times,title,content,ctime,admin_id,source,cover_img,grade")->where("grade=3")->order("times DESC,utime DESC")->limit($pageary['offset'], $showNum)->select();*/

        //查询滚动条以及下面三个
        $banner = $news->field("news_id,title,ctime,admin_id,source,cover_img,grade")->where("grade in (1,2)")->order("utime DESC")->select();

        if( $category_id == 1 ){
            $returndata = [
                "banner"=>$banner,
                "category"=>$category,
                "hotspot"=>$hotspot,
                "news"=>$hotspot
            ];
//            var_dump($returndata);die;
            echo renderJson($returndata,'',0);
        }
        //获取类别所属新闻
        $total = $news->join("relevance re on news.news_id = re.news_id")->where("news.grade = 3 and re.cate_id=".$category_id)->select();
        $total = count($total);
        $pageary = pagination($total, $showNum, $page);
        $news = $news->field("news.news_id,news.title,news.content,news.times,news.ctime,news.admin_id,news.source,news.cover_img,news.grade,re.cate_id")->join("relevance re on news.news_id = re.news_id")->where("news.grade = 3 and re.cate_id=".$category_id)->limit($pageary['offset'], $showNum)->select();
        $returndata = [
            "banner"=>$banner,
            "category"=>$category,
            "hotspot"=>$hotspot,
            "news"=>$news
        ];
//        var_dump($returndata);die;

        echo renderJson($returndata,'',0);
    }
    public function get_hotspot($showNum,$page){
        $news = M("news");
        $total = $news->count();
        $pageary = pagination($total, $showNum, $page);
        $hotspot = $news->field("news_id,times,title,content,ctime,admin_id,source,cover_img,grade")->where("grade=3")->order("times DESC,utime DESC")->limit($pageary['offset'], $showNum)->select();
        if( $hotspot ){
            foreach ( $hotspot as $key=>&$val ){
                $val['cate_id']=1;
            }
        }
        return $hotspot;
    }
    //获取新闻详情
    public function new_detail(){
        $news_id = I("post.news_id");
        if( !$news_id ) echo renderJson("","参数错误",3);
        $news = M("news");
        $news_detail = $news->field("news_id,title,content,cover_img,admin_id")->where("news_id=".$news_id)->find();
        $res = $news->where("news_id=".$news_id)->setInc('times');
        $uid = session("uid");
//        $uid = 1;
        if( $uid ){
            $user = M("user");
            $history = $user->where("uid=".$uid)->getField("history");
            if( $history ){
                $arr = explode(',',$history);
                $result = array_search($news_id,$arr);
                if( $result !== false ){
                    unset($arr[$result]);
                }
                array_push($arr,$news_id);
                $newarr = implode(',',$arr);
                $data = [
                    "history"=>$newarr
                ];
                $user->where("uid =".$uid)->save($data);
                echo renderJson($news_detail,'',0);
            }
        }
        echo renderJson($news_detail,'',0);
    }
    //获取某个文章的评论信息
    public function get_comment(){
        $news_id = I("post.news_id");
        $showNum = I("post.showNum",10);
        $page    = I("post.page",0);
        $uid = session("uid");
//        $news_id = 2;

        if( !$news_id  ) echo renderJson("","参数错误",3);
//        if( !$uid ) echo renderJson("","",1);
        $comment = M("comment");
        $count = $comment->alias("ct")->join(" user u on ct.uid = u.uid ","LEFT")->field("ct.id,ct.content,ct.ctime,u.uid,u.nickname,u.head_pic")->where(" news_id={$news_id} ")->select();

        $total = count($count);

        $pageary = pagination($total, $showNum, $page);

        $data = $comment->alias("ct")->join(" user u on ct.uid = u.uid ","LEFT")->field("ct.id,ct.content,ct.ctime,u.uid,u.nickname,u.head_pic")->where(" news_id={$news_id} ")->order("ct.ctime DESC")->limit($pageary['offset'],$showNum)->select();

        if( $data ){
            foreach( $data as $key=>&$val ){
                $val['content'] = htmlspecialchars_decode($val['content']);
            }
        }
        $newdata = [
            "result"=>$data,
            "count"=>$total
        ];
        echo renderJson($newdata);
    }
    //添加某个文章的评论信息
    public function add_comment(){
        $news_id = I("post.news_id");
        $content = I("post.content","","htmlspecialchars");
        $uid = session("uid");
//        $news_id = 1;
//        $uid = 1;
        if( !$news_id  ) echo renderJson("","参数错误",3);
        if( !$uid ) echo renderJson("","请先登录",3);
        $comment = M("comment");
        $data = [
            "content"=>$content,
            "uid"=>$uid,
            "news_id"=>$news_id,
            "ctime"=>time(),
        ];
        $res = $comment->add($data);
        if( !$res ) echo renderJson("","添加失败",0);

        echo renderJson("","添加成功",0);

    }
    //获取类别所属新闻
    public function cate_news(){
        $news = M("news");
        $showNum = I("post.showNum",10);
        $page = I("post.page",3);
        $category_id = I("post.category_id");
//        $category_id = 11;
        if( !$category_id ) echo renderJson("","参数错误",3);

        $total = $news->field("news.news_id,news.title,news.content,news.times,news.ctime,news.admin_id,news.source,news.cover_img,news.grade,re.cate_id")->join("relevance re on news.news_id = re.news_id")->where("news.grade = 3 and re.cate_id=".$category_id)->select();
        $total = count($total);
//        var_dump($total);die;
        $pageary = pagination($total, $showNum, $page);
//        var_dump($pageary['offset']);die;
        $news = $news->field("news.news_id,news.title,news.content,news.times,news.ctime,news.admin_id,news.source,news.cover_img,news.grade,re.cate_id")->join("relevance re on news.news_id = re.news_id")->where("news.grade = 3 and re.cate_id=".$category_id)->limit($pageary['offset'], $showNum)->select();
        echo renderJson($news,"获取成功",0);
    }
}