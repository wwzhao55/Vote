<?php
ini_set('date.timezone','Asia/Shanghai');
require dirname(dirname(__FILE__)).'/config.php';
require dirname(__FILE__).'/medoo.php';
require dirname(__FILE__).'/function.php';
require dirname(__FILE__).'/upload.php';
require dirname(__FILE__).'/jssdk.php';

class api
{

    public function get_db()
    {
        try {
            $db = new medoo([
                // 必须配置项
                'database_type' => 'mysql',
                'database_name' => DB_NAME,
                'server' => DB_HOST,
                'username' => DB_USER,
                'password' => DB_PASSWORD,
                'charset' => 'utf8',
                'port' => DB_PORT
            ]);
        } catch (Exception $e) {
            $db = null;
        }

        return $db;
    }

    public function get_upload()
    {
        $upload = new FileUpload();
        return $upload;
    }

    public function get_jssdk($url){
        $api = new Self;
        $jssdk = new jssdk(APPID,$api->get_ticket(),$url);
        return $jssdk->getSignPackage();
    }

    public function get_access_token(){
        $api = new Self;
        $db = $api->get_db();
        //access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = $db->get('share_config',['access_token','expire_time'],['id[>]'=>0]);
        if ($data['expire_time'] < time()) {
            // 如果是企业号用以下URL获取access_token
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APPID."&secret=".APPSECRET;
            $res = json_decode(http_get($url));

            $access_token = isset($res->access_token)?$res->access_token:'';
            if ($access_token) {
                $data['expire_time'] = time() + 7000;
                $data['access_token'] = $access_token;
                $db->update('share_config',$data,['id[>]'=>0]);
            }
        } else {
            $access_token = $data['access_token'];
        }
        return $access_token;
    }

    public function get_ticket(){
        $api = new Self;
        $db = $api->get_db();
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = $db->get('share_config',['ticket','ticket_expire_time'],['id[>]'=>0]);
        if ($data['ticket_expire_time'] < time()) {
            $accessToken = $api->get_access_token();
            // 如果是企业号用以下 URL 获取 ticket
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = json_decode(http_get($url));
            $ticket = isset($res->ticket)?$res->ticket:'';
            if ($ticket) {
                $data['ticket_expire_time'] = time() + 7000;
                $data['ticket'] = $ticket;
                $db->update('share_config',$data,['id[>]'=>0]);
            }
        } else {
            $ticket = $data['ticket'];
        }

        return $ticket;
    }

}