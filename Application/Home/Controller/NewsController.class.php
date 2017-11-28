<?php
namespace Home\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");
class NewsController extends Controller {
    public function index(){
        $news = M("news");
        $user = M("user");
        $uid = session("uid");
        $page = I('post.page',0);
        $showNum = I('post.showNum',10);
        // 获取新闻类别
        $category = get_cate();
        $category_id = 1;
        if( $uid ){
            $cate_sort = $user->where("uid=".$uid)->getField("cate_sort");
            if( $cate_sort ){
                $cate_sort = trim($cate_sort,",");
                $category = get_cate($cate_sort,1); //获取该用户的分类排序
                 //获取用户分类排序的第一个分类ID
                    $sort = explode(',',$cate_sort);
                    $category_id = $sort[0];
            }
        }
        //获取热门新闻
        $hotspot = $this->get_hotspot($showNum,$page);
        /*        $total = $news->count();
                $pageary = pagination($total, $showNum, $page);
                $hotspot = $news->field("news_id,times,title,content,ctime,admin_id,source,cover_img,grade")->where("grade=3")->order("times DESC,utime DESC")->limit($pageary['offset'], $showNum)->select();*/

        //查询滚动条以及下面三个
//        $banner = $news->field("news_id,title,ctime,admin_id,source,cover_img,grade")->where("grade in (1,2)")->order("utime DESC")->select();
        $banner = $this->get_banner();
        if( $category_id == 1 ){
            $returndata = [
                "banner"=>$banner,
                "category"=>$category,
                "hotspot"=>$hotspot,
                "news"=>$hotspot
            ];
            echo renderJson($returndata,'',0);
        }
        //获取类别所属新闻
        $total = $news->join("relevance re on news.news_id = re.news_id")->where("news.grade = 3 and re.cate_id=".$category_id)->select();
        $total = count($total);
        $pageary = pagination($total, $showNum, $page);
        $news = $news->field("news.news_id,news.title,news.times,news.ctime,news.admin_id,news.source,news.cover_img,news.grade,news.profile,re.cate_id")->join("relevance re on news.news_id = re.news_id")->where("news.grade = 3 and re.cate_id=".$category_id)->limit($pageary['offset'], $showNum)->select();
        if( $news ){
            foreach ( $news as $key=>&$val ){
                $val['admin_id'] = get_admin($val['admin_id']);
            }
        }
        $returndata = [
            "banner"=>$banner,
            "category"=>$category,
            "hotspot"=>$hotspot,
            "news"=>$news
        ];
        echo renderJson($returndata,'',0);
    }
    public function get_banner(){
        $data = M("news")->field("news_id,title,ctime,admin_id,source,cover_img,grade,profile")->where("grade in (1,2)")->order("utime DESC")->select();
        return $data;
    }
    public function news_banner(){
        $data = $this->get_banner();
        echo renderJson($data,'',0);
    }
    //获取热门文章
    public function get_hotspot($showNum,$page){
        $news = M("news");
        $total = $news->where("grade = 3")->count();
        $pageary = pagination($total, $showNum, $page);
        $hotspot = $news->field("news_id,times,title,ctime,admin_id,source,cover_img,grade,profile")->where("grade=3")->order("times DESC,utime DESC")->limit($pageary['offset'], $showNum)->select();
        if( $hotspot ){
            foreach ( $hotspot as $key=>&$val ){
                $val['cate_id']=1;
                $val['admin_id'] = get_admin($val['admin_id']);
            }
        }
        return $hotspot;
    }
    //获取热门文章
    public function news_hotspot(){
        $news = M("news");
        $showNum = I("post.showNum");
        $page = I("post.page");
        $total = $news->where("grade = 3")->count();
        $pageary = pagination($total, $showNum, $page);
        $hotspot = array();
        $hotspot = $news->field("news_id,profile,times,title,ctime,admin_id,source,cover_img,grade")->where("grade = 3")->order("times DESC,utime DESC")->limit($pageary['offset'], $showNum)->select();
        if( $hotspot ){
            foreach ( $hotspot as $key=>&$val ){
                $val['cate_id']=1;
                $val['admin_id'] = get_admin($val['admin_id']);
            }
        }
        echo renderJson($hotspot,'',0);
    }
    //获取新闻详情
    public function new_detail(){
        $news_id = I("post.news_id");
//        $news_id = 94;
        if( !$news_id ) echo renderJson("","参数错误",3);
        $news = M("news");
        $news_detail = $news->field("news_id,title,content,cover_img,admin_id,source,ctime,profile")->where("news_id=".$news_id)->find();
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
            }else{
                $data =  [
                    "history"=>$news_id
                ];
            }
            $user->where("uid =".$uid)->save($data);
            $news_detail['admin_id'] = get_admin($news_detail['admin_id']);
            $news_detail['content'] = htmlspecialchars_decode($news_detail['content']);
//            var_dump($news_detail);die;
            echo renderJson($news_detail,'',0);
            }
        if( $news_detail ){
            $news_detail['admin_id'] = get_admin($news_detail['admin_id']);
            $news_detail['content'] = htmlspecialchars_decode($news_detail['content']);
        }
//        var_dump($news_detail);die;
        echo renderJson($news_detail,'',0);
    }
    //获取某个文章的评论信息
    public function get_comment(){
        $news_id = I("post.news_id");
        $showNum = I("post.showNum",10);
        $page    = I("post.page",0);

        if( !$news_id  ) echo renderJson("","参数错误",3);
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
        if( !$news_id  ) echo renderJson("","参数错误",3);
        if( !$uid ) echo renderJson("","请先登录",2);
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
        $page = I("post.page",0);
        $category_id = I("post.category_id");
//        $category_id = 1;
        if( !$category_id ) echo renderJson("","参数错误",3);
        if( $category_id !=1 ){
            $total = $news->field("news.news_id,news.title,news.times,news.ctime,news.admin_id,news.source,news.cover_img,news.grade,re.cate_id")->join("relevance re on news.news_id = re.news_id")->where("news.grade = 3 and re.cate_id=".$category_id)->select();
            $total = count($total);
            $pageary = pagination($total, $showNum, $page);
            $news = $news->field("news.news_id,news.title,news.times,news.ctime,news.admin_id,news.source,news.cover_img,news.grade,re.cate_id")->join("relevance re on news.news_id = re.news_id")->where("news.grade = 3 and re.cate_id=".$category_id)->limit($pageary['offset'], $showNum)->select();
            if( $news ){
                foreach( $news as $key=>&$val ){
                    $val['admin_id'] = get_admin($val['admin_id']);
                }
            }
        }else{
            $news = $this->get_hotspot($showNum,$page);
        }

        echo renderJson($news,"获取成功",0);
    }
    //获取新闻分类
    public function get_category(){
        $uid = session("uid");
        if( !$uid ) echo renderJson("","请先登录",2);
        $cate_sort = M("user")->where("uid=".$uid)->getField("cate_sort");
        if( $cate_sort ){
            $cate_sort = trim($cate_sort,",");
            $cate_sort = get_cate($cate_sort,1);
        }else{
            $cate_sort = get_cate();
        }
        echo renderJson($cate_sort,"",0);
    }
    //分类排序
    public function cate_sort(){
        $cate_sort = I("post.cate_sort");
        $uid = session("uid");
        if( !$uid ) echo renderJson("","请先登录",2);
        if( !is_array($cate_sort) ) echo renderJson("","参数错误",3);
        $sort_str = implode(",",$cate_sort);
        $data = [
            "cate_sort"=>$sort_str
        ];
        $res = M("user")->where("uid=".$uid)->save($data);
        $bool = 0;
        if( $res === false ) $bool = 1;

        echo renderJson("","",$bool);
    }
    //获取某个用户的浏览历史
    public function get_history(){
        $uid = session("uid");
        $model = M();
        if( !$uid ) echo renderJson("","请先登录",2);
        $history_str = M("user")->where("uid=".$uid)->getField("history");
        if( !$history_str ) echo renderJson("","",0);
        $history_arr = explode(",",$history_str);
        $sort = array_reverse($history_arr);
        $history = implode(",",$sort);
        $sql = "select news_id,times,title,ctime,admin_id,source,profile,cover_img,grade from news where news_id in($history) order by field(news_id,$history)";
        $data = $model->query($sql);
        if( $data ){
            foreach( $data as $key=>&$val ){
                $val['admin_id'] = get_admin($val['admin_id']);
            }
        }
        $data['current_time'] = strtotime(date("Y-m-d",time()));
        echo renderJson($data,"",0);
    }
    //搜索新闻
    public function news_search(){
        $search = I("post.search",'',"htmlspecialchars");
        $showNum = I("post.showNum",10);
        $page = I("post.page",0);
        if( !$search ) echo renderJson("","参数错误",3);
        $model = M();
        $sql = "select count(*) as count from news where CONCAT(news.title,news.profile) LIKE '%".$search."%'";
        $res = $model->query($sql);
        if( empty($res) ){
            echo renderJson("","",0);
        }
        $total = $res[0]['count'];
        $pageary = pagination($total, $showNum, $page);
        $sql1 = "select news_id,times,title,ctime,admin_id,source,profile,cover_img,grade from news where CONCAT(news.title,news.profile) LIKE '%".$search."%' order by ctime DESC limit ".$pageary['offset'].",".$showNum;
        $result = $model->query($sql1);
        foreach( $result as $key=>&$val ){
            $val['admin_id'] = get_admin($val['admin_id']);
        }
        echo renderJson($result,"",0);
    }
    //删除浏览历史记录
    public function del_history(){
        $news_id = I("post.news_id");
        $flag = I("post.flag");
        $uid = session("uid");
/*        $news_id = 1;
        $flag = 0;
        $uid = 1;*/
        if( !$uid ) echo renderJson("","请先登录",2);
        if( $flag == 0 ){
            if( !$news_id ) echo renderJson("","参数错误",3);
        }
        $user = M("user");
        if( $flag == 0 ){
            $user_history = $user->where(" uid=".$uid)->getField("history");
            $history_arr = explode(",",$user_history);
            $res = array_keys($history_arr,$news_id);
            unset($history_arr[$res[0]]);
            $history = implode(",",$history_arr);
            $data = [
                "history"=>$history
            ];
            $res = $user->where("uid=".$uid)->save($data);
            if( $res ){
                echo renderJson("","删除成功",0);
            }else{
                echo renderJson("","删除失败",1);
            }
        }
        $data = [
            "history"=>''
        ];
        $res = $user->where("uid=".$uid)->save($data);
        if( $res === false ){
            echo renderJson("","删除失败",1);
        }
        echo renderJson("","删除成功",0);
    }
    //记者详情
    public function author_detail(){
        $news_id = I("post.news_id");
        $page = I("post.page",0);
        $showNum = I("post.showNum",0);
        if( !$news_id ) echo renderJson("","参数错误",3);
        $news = M("news");
        $admin = M("admin");
        $admin_id = $news->where("news_id=".$news_id)->getField("admin_id");
        $admin_detail = $admin->join(" news on news.admin_id = admin.id ","LEFT")->field("admin.id,admin.username,admin.head_pic,admin.email,admin.introduce,count(*) count")->where("admin.id=".$admin_id)->group("news.admin_id")->select();
        $total = $news->where("admin_id=".$admin_id)->count();
        $pageary = pagination($total, $showNum, $page);
        $news_list = $news->field("news_id,times,title,ctime,admin_id,source,profile,cover_img,grade")->where("admin_id=".$admin_id)->order("ctime DESC")->limit($pageary['offset'],$showNum)->select();
        $data = [
            "admin_detail"=>$admin_detail[0],
            "news_list"=>$news_list
        ];
        echo renderJson($data,"",0);
    }
}