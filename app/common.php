<?php
use mip\Mip;
    function compress_html($higrid_uncompress_html_source) {
        $chunks = preg_split( '/(<pre.*?\/pre>)/ms', $higrid_uncompress_html_source, -1, PREG_SPLIT_DELIM_CAPTURE );
        $higrid_uncompress_html_source = '';
        foreach ( $chunks as $c )
        {
            if ( strpos( $c, '<pre' ) !== 0 )
            {
            $c = preg_replace( '/[\\n\\r\\t]+/', ' ', $c );
//              $c = preg_replace('/ {2,}/', '', $c);
//              $c = preg_replace('/> </', '><', $c);

                $c = preg_replace( '/ {2,}/', ' ', $c );
                $c = preg_replace( '/>\\s</', '><', $c );
                $c = preg_replace( '/\\/\\*.*?\\*\\//i', '', $c );
            }
            $higrid_uncompress_html_source .= $c;
        }
        $custom = preg_split( '/(<style mip-custom.*<\/style>)/ms', $higrid_uncompress_html_source, -1, PREG_SPLIT_DELIM_CAPTURE );
        $higrid_uncompress_html_source = '';
        foreach ( $custom as $k => $c ) {
            if ($k == 1) {
                $c = str_replace(array("", "\r", "\n", "\t", '  ', '    ', '    '), '', $c);
                }
                $higrid_uncompress_html_source .= $c;
            }
            return $higrid_uncompress_html_source;
    }
      
    function create_salt($length=12) {
          return $salt = substr(uniqid(rand()),0,$length);
    }
    
    function create_md5($string,$salt)
    {
        return md5($string.$salt);
    }
    function uuid() {
        $charid = strtolower(md5(uniqid(rand(), true)));
        return substr($charid,0,24);
    }
    function jsonError($message = '',$url=null) {
        $return['msg'] = $message;
        $return['code'] = -1;
        $return['url'] = $url;
        return json($return, 200);
    }
    function jsonSuccess($message = '',$data = '',$url=null) {
        $return['msg']  = $message;
        $return['data'] = $data;
        $return['code'] = 1;
        $return['url'] = $url;
        return json($return, 200);
    }
    
    function fetch_file_lists($dir, $file_type = null) {
       if ($file_type) {
    	    if (substr($file_type, 0, 1) == '.') {
                $file_type = substr($file_type, 1);
            }
        }
    
        $base_dir = realpath($dir);
    
        if (!file_exists($base_dir)) {
            return false;
        }
    
        $dir_handle = opendir($base_dir);
    
        $files_list = array();
    
        while (($file = readdir($dir_handle)) !== false) {
            if (substr($file, 0, 1) != '.' AND !is_dir($base_dir . '/' . $file)) {
                if (($file_type AND end(explode('.', $file)) == $file_type) OR !$file_type) {
                    $files_list[] = $base_dir . '/' . $file;
                }
            }
            else if (substr($file, 0, 1) != '.' AND is_dir($base_dir . '/' . $file)) {
                if ($sub_dir_lists = fetch_file_lists($base_dir . '/' . $file, $file_type)) {
                    $files_list = array_merge($files_list, $sub_dir_lists);
                }
            }
        }
        return $files_list;
    }
    
    function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {
        $tree = [];
        if (is_array($list)) {
            $refer = [];
            foreach ($list as $key => $data) {
                if ($data instanceof \think\Model) {
                    $list[$key] = $data->toArray();
                }
                $refer[$data[$pk]] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                if (!isset($list[$key][$child])) {
                    $list[$key][$child] = [];
                }
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }
        return $tree;
    }
    
    function msectime() {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f' , (floatval($msec) + floatval($sec)) * 1000);
        return $msectime;
    }
    function formatTime($value) {
        if(time() - $value < 60) {
            return (time() - $value).'秒前';
        }
        if(time() - $value > 60 && time() - $value < 3600) {
            return (int)((time() - $value)/60).'分钟前';
        }
        if(time() - $value > 3600 && time() - $value < 86400) {
            return (int)((time() - $value)/3600).'小时前';
        }
        if(time() - $value > 86400 && time() - $value < 172800) {
            return '1天前';
        }
        return date('m-d', $value);
    }
    
    function getAvatarUrl($uid) {
        if (MIP_HOST) {
            if (file_exists(ROOT_PATH . 'uploads'. DS .'avatar' . DS . $uid . '.jpg')) {
                return 'uploads/avatar/' . $uid .'.jpg';
            } else {
                return 'public/assets/common/images/avatar.jpg';
            }
        } else {
            if (file_exists(ROOT_PATH .'public'. DS . 'uploads'. DS .'avatar' . DS . $uid . '.jpg')) {
                return 'public/uploads/avatar/' . $uid .'.jpg';
            } else {
                return 'public/assets/common/images/avatar.jpg';
            }
        }
            
    }
        
    function getMipInfo() {
        return db('Settings')->select();
    }
        
    function getFile($url, $save_dir = '', $filename = '', $type = 0) {  
        if (trim($url) == '') {  
            return false;  
        }
        if (trim($save_dir) == '') {  
            $save_dir = './';  
        }
        if (0 !== strrpos($save_dir, '/')) {  
            $save_dir.= '/';  
        }
        //创建保存目录  
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {  
            return false;  
        }  
        //获取远程文件所采用的方法  
        if ($type) {  
            $ch = curl_init();  
            $timeout = 5;  
            curl_setopt($ch, CURLOPT_URL, $url);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);  
            $content = curl_exec($ch);  
            curl_close($ch);  
        } else {  
            ob_start();  
            readfile($url);  
            $content = ob_get_contents();  
            ob_end_clean();  
        }  
        //echo $content;  
        $size = strlen($content);  
        //文件大小  
        $fp2 = @fopen($save_dir . $filename, 'a');  
        fwrite($fp2, $content);  
        fclose($fp2);  
        unset($content, $url);  
        return array(  
            'file_name' => $filename,  
            'save_path' => $save_dir . $filename,  
            'file_size' => $size  
        );
    }
    function addFileToZip($path,$zip) {
        $handler=opendir($path);
        while(($filename=readdir($handler))!==false){
            if($filename != "." && $filename != ".."){
                if(is_dir($path."/".$filename)){
                    addFileToZip($path."/".$filename, $zip);
                }else{
                    $zip->addFile($path."/".$filename);
                }
            }
        }
        @closedir($path);
    }
    
    function pushData($api,$urls) {
        $ch = curl_init();
        $options =  array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }
    function getData($api,$postData = '') {
        if (!$api) {
            return false;
        }
        if (empty($postData)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $result = curl_exec($ch);
    
            curl_close($ch);
        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $result = curl_exec($ch);
            curl_close($ch);
        }
        if ($result) {
            return $result;
        } else {
            return null;
        }
    }
    
    function getSHA1($strToken, $intTimeStamp, $strNonce, $strEncryptMsg = '') {
        $arrParams = array(
            $strToken, 
            $intTimeStamp, 
            $strNonce,
        );
        if (!empty($strEncryptMsg)) {
            array_unshift($arrParams, $strEncryptMsg);
        }
        sort($arrParams, SORT_STRING);
        $strParam = implode($arrParams);
        return sha1($strParam);
    }
    
    function deleteHtml($str) { 
        $str = preg_replace("/(\s|\r|\n|\t|\&nbsp\;|　| |   |\xc2\xa0)/","",trim(strip_tags($str)));
        return $str; //返回字符串
    }
    
    function deleteStyle($content)
    {
        $itemInfo['content'] = htmlspecialchars_decode($content);
        preg_match_all('/<img.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/', $itemInfo['content'], $imagesArray);
        $patern = '/^^((https|http|ftp)?:\/\/)[^\s]+$/';
        foreach($imagesArray[0] as $key => $val) {
            @preg_match('/alt=".+?"/',$val,$tempAlt);
            @preg_match('/<img.+(width=\"?\d*\"?).+>/i',$val,$tempWidth);
            @preg_match('/<img.+(height=\"?\d*\"?).+>/i',$val,$tempHeight);
            @$alt = explode('=',$tempAlt[0]);
            @$alt = explode('"',$alt[1]);
            if (count($alt) == 1) {
                $alt = $alt[0];
            }
            if (count($alt) == 2) {
                $alt = $alt[1] ;
            }
            if (count($alt) == 3) {
                $alt = $alt[1] ;
            }
            if (@preg_match($patern,$imagesArray[1][$key])) {
                $src = $imagesArray[1][$key];
            } else {
                $src = config('pcDomain') . $imagesArray[1][$key];
            }
            if ($tempWidth && $tempHeight) {
                @preg_match('/\d+/i',$tempWidth[1],$width);
                if (intval($width[0]) > 320) {
                    $layout = 'layout="container"';
                    $tempImg = '<mip-img '.$layout.' alt="'.$alt.'" src="'.$src.'" popup></mip-img>';
                } else {
                    $layout = 'layout="fixed"';
                    $tempImg = '<mip-img ' .$layout. ' ' . $tempWidth[1] . '" ' . $tempHeight[1] .'" alt="'.$alt.'" src="'.$src.'" popup></mip-img>';
                }
            } else {
                $layout = 'layout="container"';
                $tempImg = '<mip-img '.$layout.' alt="'.$alt.'" src="'.$src.'" popup></mip-img>';
            }
            $itemInfo['content'] =  str_replace($val,$tempImg,$itemInfo['content']);
        }
        $itemInfo['content'] =  preg_replace("/style=.+?['|\"]/i",'', $itemInfo['content']);
        @preg_match_all('/<a[^>]*>[^>]+a>/',$itemInfo['content'],$tempLink);
        foreach($tempLink[0] as $k => $v) {
            if(strpos($v,"href")) {
                @preg_match('/href\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^"\'>\s]+))/',$v,$hrefRes);
                $matches = @preg_match($patern,$hrefRes[1]);
                if (!$matches) {
                    $itemInfo['content'] = str_replace($v,'',$itemInfo['content']);
                }
            } else {
                $itemInfo['content'] = str_replace($v,'',$itemInfo['content']);
            }
        }
        @preg_match_all('/<iframe.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>*<\/iframe>/', $itemInfo['content'], $iframeArray);
        if ($iframeArray) {
            foreach($iframeArray[0] as $key => $val) {
                $layout = 'layout="responsive"';
                $tempiframe = '<mip-iframe   width="320" height="200" '.$layout.' src="'.$iframeArray[1][$key].'"></mip-iframe>';
                $itemInfo['content'] =  str_replace($val,$tempiframe,$itemInfo['content']);
            }
        }
        @preg_match_all('/<embed.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/', $itemInfo['content'], $embedArray);
        if ($embedArray) {
            foreach($embedArray[0] as $key => $val) {
                $layout = '';
                $tempembed = '<mip-embed type="ad-comm" '.$layout.' src="'.$embedArray[1][$key].'"></mip-embed>';
                $itemInfo['content'] =  str_replace($val,$tempembed,$itemInfo['content']);
            }
        }
        @preg_match_all('/<video.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>*<\/video>/', $itemInfo['content'], $videoArray);
        if ($videoArray) {
            foreach($videoArray[0] as $key => $val) {
                $layout = '';
                $tempvideo = '<mip-video '.$layout.' src="'.$videoArray[1][$key].'"></mip-video>';
                $itemInfo['content'] =  str_replace($val,$tempvideo,$itemInfo['content']);
            }
        }
        return $itemInfo['content'];
        
    }
