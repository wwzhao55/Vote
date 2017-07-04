<?php
/**
 * 是否是AJAx提交的
 * @return bool
 */
function isAjax(){
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
        return true;
    }else{
        return false;
    }
}

/**
 * 是否是GET提交的
 */
function isGet(){
    return $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
}

/**
 * 是否是POST提交
 * @return int
 */
function isPost() {
    return ($_SERVER['REQUEST_METHOD'] == 'POST') ? true : false;
}

/**
 * GET 请求
 * @param string $url
 */
function http_get($url){
    $oCurl = curl_init();
    if(stripos($url,"https://")!==FALSE){
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if(intval($aStatus["http_code"])==200){
        return $sContent;
    }else{
        return false;
    }
}

function is_weixin(){ 
    if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
        return true;
    }  
        return false;
}

function downloadWeixinFile($url)  {  
    $ch = curl_init($url);  
    curl_setopt($ch, CURLOPT_HEADER, 0);      
    curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头  
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
    $package = curl_exec($ch);  
    $httpinfo = curl_getinfo($ch);  
    curl_close($ch);  
    $imageAll = array_merge(array('header' => $httpinfo), array('body' => $package));   
    return $imageAll;  
}  

function checkDownload($download){
    $body_json = $download['body'];
    return  is_null(json_decode($body_json));
}
   
function saveWeixinFile($newfolder,$filename, $filecontent)  {     
    createFolder($newfolder);  
    $local_file = fopen($newfolder."/".$filename, 'w');  
    if (false !== $local_file){  
          
        if (false !== fwrite($local_file, $filecontent)) {  
          
            fclose($local_file); 
            return true; 
         
        }else{
            return false;
        }
    }else{
        return false;
    }  
}  
function createFolder($path) {  
    if (!file_exists($path))  {  
        createFolder(dirname($path));  
        mkdir($path, 0777);  
    }  
}  

function get_day_count($table_name,$db){
    $time_min = $db->min($table_name,'created_at',['id[>]'=>0]);
    $time_max = $db->max($table_name,'created_at',['id[>]'=>0]);
    $start_time = strtotime(date('Y-m-d',$time_min));
    $end_time = strtotime(date('Y-m-d',$time_max));
    $count_array = array();
    for($i=$start_time;$i<=$end_time;$i+=24*60*60){
        $count = $db->count($table_name,['created_at[<]'=>$i+60*60*24]);
        array_push($count_array, array('created_at'=>$i,'count'=>$count));
    }
    return $count_array;
}

function getRandChar($length){
    $str = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol)-1;

    for($i=0;$i<$length;$i++){
        $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
    }

   return $str;
}