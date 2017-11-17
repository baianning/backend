<?php
namespace Qiniu;

use Qiniu\Storage\UploadManager;
use Qiniu\Storage\BucketManager;
class Filemanage {
	public $auth;
	public $bucket = 'wenl';// 要上传的空间
    public $baseUrl = 'http://oa6mg4jjn.bkt.clouddn.com';

	private $ak = 'EtLGle-iYA6vhg-9qaZgUiKiSk4DQyfsS5uk01Uw';
    private $sk = 'bbEMCQQNM0tNMNPFpzCPcwlLM9gvqM146N4JqsvQ';

    public function __construct(){
    	$this->auth = new Auth( $this->ak, $this->sk );
    }

    public function upload($file){

        // 生成上传 Token
        $token = $this->auth->uploadToken($this->bucket);
        // 要上传文件的本地路径
        $filePath = $file['tmp_name'];

        // 上传到七牛后保存的文件名
        $key = $file['name'];

        // 初始化 UploadManager 对象并进行文件的上传
        $uploadMgr = new UploadManager();

        // 调用 UploadManager 的 putFile 方法进行文件的上传
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        echo "\n====> putFile result: \n";
        if ($err !== null) {
            var_dump($err);
        } else {
            var_dump($ret);
        }
    }

    public function download(){
        $key = '新建文本文档.txt';
        //baseUrl构造成私有空间的域名/key的形式
        $authUrl = $this->auth->privateDownloadUrl($this->baseUrl.'/'.$key);
        $file = @fopen($authUrl,"r");
        if (!$file) {
            $this->exitjson("对不起,你要下载的文件不存在。");
        }else {
            header("content-type: application/octet-stream");
            header("content-disposition: attachment; filename=" . $key);
            while (!feof ($file)) {
                echo fread($file,50000);
            }
            fclose ($file);
        }
    }
  

    public function show(){
        $bucketMgr = new BucketManager($this->auth);

        // 要列取的空间名称
        $bucket = '';

        // 要列取文件的公共前缀
        $prefix = '';
        $marker = '';
        $limit = 3;

        list($iterms, $marker, $err) = $bucketMgr->listFiles($this->bucket, $prefix, $marker, $limit);
        if ($err !== null) {
            echo "\n====> list file err: \n";
            var_dump($err);
        } else {
            echo "Marker: $marker\n";
            echo "\nList Iterms====>\n";
            var_dump($iterms);
        }
    }

    public function delete(){
        
        //初始化BucketManager
        $bucketMgr = new BucketManager($this->auth);

        //你要测试的空间， 并且这个key在你空间中存在
        $key = '新建文本文档.txt';

        //删除$bucket 中的文件 $key
        $err = $bucketMgr->delete($this->bucket, $key);
        echo "\n====> delete $key : \n";
        if ($err !== null) {
            var_dump($err);
        } else {
          echo "Success!";
        }
    }
}

