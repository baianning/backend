<?php
namespace Admin\Controller;
use Think\Controller;
class NewsController extends Controller {
    public function news_list(){
        $news = M("news");
        $showNum = I("post.showNum",10);
        $page = I("post.page",0);
        $total = $news->join("relevance re on news.news_id = re.news_id","LEFT")->join("admin on news.admin_id = admin.id","LEFT")->field("news.news_id,news.title,news.profile,news.source,news.times,news.status,admin.username,news.ctime")->where("news.is_del = 0")->group("news.news_id")->order("news.ctime DESC")->select();
        $total = count($total);
        $pageary = pagination($total, $showNum, $page);
        $news_list = $news->join("relevance re on news.news_id = re.news_id","LEFT")->join("admin on news.admin_id = admin.id","LEFT")->field("news.news_id,news.title,news.profile,news.source,news.times,news.status,admin.username,news.ctime")->where("news.is_del = 0")->group("news.news_id")->order("news.ctime DESC")->limit($pageary['offset'], $showNum)->select();
        $this->assign("news_list",$news_list);
        $this->display();
    }
    public function news_add(){
/*        $title = I('post.title','','htmlspecialchars');
        $content = I('post.content','','htmlspecialchars');
        $title = I('post.title','','htmlspecialchars');*/
        $this->display();
    }
}