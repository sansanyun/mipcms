<?php
//判断是否属于序列化数据
function is_serialized( $data ) {
    $data = trim( $data );
    if ( 'N;' == $data )
        return true;
    if ( !preg_match( '/^([adObis]):/', $data, $badions ) )
        return false;
    switch ( $badions[1] ) {
        case 'a' :
        case 'O' :
        case 's' :
            if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) )
                return true;
            break;
        case 'b' :
        case 'i' :
        case 'd' :
            if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) )
                return true;
            break;
    }
    return false;
}
//代码压缩
function compress_html($higrid_uncompress_html_source) {
    $chunks = preg_split( '/(<pre.*?\/pre>)/ms', $higrid_uncompress_html_source, -1, PREG_SPLIT_DELIM_CAPTURE );
    $higrid_uncompress_html_source = '';//[higrid.net]修改压缩html : 清除换行符,清除制表符,去掉注释标记
    foreach ( $chunks as $c )
    {
        if ( strpos( $c, '<pre' ) !== 0 )
        {
//[higrid.net] remove new lines & tabs
            $c = preg_replace( '/[\\n\\r\\t]+/', ' ', $c );
// [higrid.net] remove extra whitespace
            $c = preg_replace( '/\\s{2,}/', ' ', $c );
// [higrid.net] remove inter-tag whitespace
            $c = preg_replace( '/>\\s</', '><', $c );
// [higrid.net] remove CSS & JS comments
            $c = preg_replace( '/\\/\\*.*?\\*\\//i', '', $c );
        }
        $higrid_uncompress_html_source .= $c;
    }
    return $higrid_uncompress_html_source;
}
//转换bbcode to html
function bbc2html($tmpText){
    /*[b]*/ 	$tmpText = preg_replace('#\[b\](.*)\[/b\]#isU', '<strong>$1</strong>', $tmpText);
    /*[i]*/	 	$tmpText = preg_replace('#\[i\](.*)\[/i\]#isU', '<em>$1</em>', $tmpText);
    /*[s]*/	 	$tmpText = preg_replace('#\[s\](.*)\[/s\]#isU', '<del>$1</del>', $tmpText);
    /*[br]*/	$tmpText = preg_replace('#\[br\]#isU', '<br />', $tmpText);
    /*[u]*/	 	$tmpText = preg_replace('#\[u\](.*)\[/u\]#isU', '<span style="text-decoration:underline">$1</span>', $tmpText);
    /*[color]*/ $tmpText = preg_replace('#\[color=(.*)\](.*)\[\/color\]#isU', '<span style="color:$1;">$2</span>', $tmpText);
    /*[size]*/ 	$tmpText = preg_replace('#\[size=([0-9]{1,2})\](.*)\[\/size\]#isU', '<span style="font-size:$1px;">$2</span>', $tmpText);
    /*[font]*/ 	$tmpText = preg_replace('#\[font=(.*)\](.*)\[\/font\]#isU', '<span style="font-family:$1;">$2</span>', $tmpText);
    /*[url=]*/	$tmpText = preg_replace('#\[url=(.*)\](.*)\[\/url\]#isU', '<a href="$1" target="">$2</a>', $tmpText);
    /*[url]*/	$tmpText = preg_replace('#\[url\](.*)\[\/url\]#isU', '<a href="$1" target="">$1</a>', $tmpText);
    /*[img]*/	$tmpText = preg_replace('#\[img\](.*)\[\/img\]#isU', '<img src="$1" alt="Bild" />', $tmpText);
    /*[align]*/ $tmpText = preg_replace('#\[align=(.*)\](.*)\[\/align\]#isU', '<div style="text-align:$1">$2</div>', $tmpText);
    /*[center]*/$tmpText = preg_replace('#\[center\](.*)\[\/center\]#isU', '<div style="text-align:center">$1</div>', $tmpText);
    /*[right]*/ $tmpText = preg_replace('#\[right\](.*)\[\/right\]#isU', '<div style="text-align:right">$1</div>', $tmpText);
    /*[left]*/ 	$tmpText = preg_replace('#\[left\](.*)\[\/left\]#isU', '<div style="text-align:left">$1</div>', $tmpText);
    /*[code]*/ 	$tmpText = preg_replace('#\[code\](.*)\[\/code\]#isU', '<code>$1</code>', $tmpText);
    /*[quote]*/ $tmpText = preg_replace('#\[quote\](.*)\[\/quote\]#isU', '<table width=100% bgcolor=lightgray><tr><td bgcolor=white>$1</td></tr></table>', $tmpText);
    /*[quote=]*/$tmpText = preg_replace('#\[quote=(.*)\](.*)\[\/quote\]#isU', '<table width=100% bgcolor=lightgray><tr><td bgcolor=white>$1<blockquote>$2</blockquote></td></tr></table>', $tmpText);
    /*[mail=]*/	$tmpText = preg_replace('#\[mail=(.*)\](.*)\[\/mail\]#isU', '<a href="mailto:$1">$2</a>', $tmpText);
    /*[mail]*/ 	$tmpText = preg_replace('#\[mail\](.*)\[\/mail\]#isU', '<a href="mailto:$1">$1</a>', $tmpText);
    /*[email=]*/$tmpText = preg_replace('#\[email=(.*)\](.*)\[\/email\]#isU', '<a href="mailto:$1">$2</a>', $tmpText);
    /*[email]*/ $tmpText = preg_replace('#\[email\](.*)\[\/email\]#isU', '<a href="mailto:$1">$1</a>', $tmpText);
    /*[list]*/
    while(preg_match('#\[list\](.*)\[\/list\]#is', $tmpText)){
        $tmpText = preg_replace_callback('#\[list\](.*)\[\/list\]#isU',
            create_function('$str',"return str_replace(array(\"\\r\",\"\\n\"),'','<ul>'.preg_replace('#\[\*\](.*)\$#isU',
				'<li>\$1</li>',preg_replace('#\[\*\](.*)(\<li\>|\$)#isU','<li>\$1</li>\$2',preg_replace('#\[\*\](.*)(\[\*\]|\$)#isU',
				'<li>\$1</li>\$2',\$str[1]))).'</ul>');"), $tmpText);
        $tmpText = preg_replace('#<ul></li>(.*)</ul>(<li>|</ul>)#isU', '<ul>$1</ul></li>$2', $tmpText); // Validitäts-Korrektur
    }
    /*[list=]*/
    while(preg_match('#\[list=(1|a)\](.*?)\[\/list\]#is', $tmpText)){
        $tmpText = preg_replace_callback('#\[list=.\](.*)\[\/list\]#isU',
            create_function('$str',"return str_replace(array(\"\\r\",\"\\n\"),'','<ol>'.preg_replace('#\[\*\](.*)\$#isU',
				'<li>\$1</li>',preg_replace('#\[\*\](.*)(\<li\>|\$)#isU','<li>\$1</li>\$2',preg_replace('/\[\*\](.*?)\[\/\*\]/is',
				'<li>\$1</li>\$2',\$str[1]))).'</ol>');"), $tmpText);
        $tmpText = preg_replace('#<ul></li>(.*)</ul>(<li>|</ul>)#isU', '<ul>$1</ul></li>$2', $tmpText); // Validitäts-Korrektur
    }
    return nl2br($tmpText, true);
}

  //生成混淆码
  function create_salt($length=12)
  {
      return $salt = substr(uniqid(rand()),$length);
  }
  
  //加密
    function create_md5($string,$salt)
    {
        return md5($string.$salt);
    }
    //生成唯一的ID
    function uuid() {
        mt_srand((double)microtime()*10000);
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
	  if ($file_type)
	  {
	    if (substr($file_type, 0, 1) == '.')
    {
      $file_type = substr($file_type, 1);
    }
  }

  $base_dir = realpath($dir);
 
  if (!file_exists($base_dir))
  {
    return false;
  }

  $dir_handle = opendir($base_dir);

  $files_list = array();

  while (($file = readdir($dir_handle)) !== false)
  {
    if (substr($file, 0, 1) != '.' AND !is_dir($base_dir . '/' . $file))
    {
      if (($file_type AND end(explode('.', $file)) == $file_type) OR !$file_type)
      {
        $files_list[] = $base_dir . '/' . $file;
      }
    }
    else if (substr($file, 0, 1) != '.' AND is_dir($base_dir . '/' . $file))
    {
      if ($sub_dir_lists = fetch_file_lists($base_dir . '/' . $file, $file_type))
      {
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

function formatTime($value) {
    if(time() - $value < 60) {
        return (time() - $value).'秒前';
    }
    if(time() - $value > 60 && time() - $value < 3600) {
        return ceil((time() - $value)/60).'分钟前';
    }
    if(time() - $value > 3600 && time() - $value < 86400) {
        return ceil((time() - $value)/3600).'小时前';
    }
    if(time() - $value > 86400 && time() - $value < 172800) {
        return '1天前';
    }
    return date('Y-m-d H:i:s', $value);
}
