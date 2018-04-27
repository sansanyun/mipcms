<?php
namespace mip;
/**
*
* 汉字转拼音类
* @Author : Kin
* @Date   : 2014-03-16
* @Email  : Mr.kin@foxmail.com
*
*/

class ChinesePinyin{
	
	//utf-8中国汉字集合
	private $ChineseCharacters;
	//编码
	private $charset = 'utf-8';
	
	public function __construct(){
		if( empty($this->ChineseCharacters) ){
		  $this->ChineseCharacters = file_get_contents(EXTEND_PATH . DS . 'mip' . DS . 'ChineseCharacters.dat');	
		}
	}
	
	/*
	* 转成带有声调的汉语拼音
	* param $input_char String  需要转换的汉字
	* param $delimiter  String   转换之后拼音之间分隔符
	* param $outside_ignore  Boolean     是否忽略非汉字内容
	*/
	public function TransformWithTone($input_char,$delimiter=' ',$outside_ignore=false){
		
		$input_len = mb_strlen($input_char,$this->charset);
		$output_char = '';
		for($i=0;$i<$input_len;$i++){
			$word = mb_substr($input_char,$i,1,$this->charset);
			if(preg_match('/^[\x{4e00}-\x{9fa5}]$/u',$word) && preg_match('/\,'.preg_quote($word).'(.*?)\,/',$this->ChineseCharacters,$matches) ){
				$output_char.=$matches[1].$delimiter;
			}else if(!$outside_ignore){
				$output_char.=$word;
			}
		}
		
		return $output_char;
	}
	
	/*
	* 转成带无声调的汉语拼音
	* param $input_char String  需要转换的汉字
	* param $delimiter  String   转换之后拼音之间分隔符
	* param $outside_ignore  Boolean     是否忽略非汉字内容
	*/	
	public function TransformWithoutTone($input_char,$delimiter='',$outside_ignore=true){
		
		$char_with_tone = $this->TransformWithTone($input_char,$delimiter,$outside_ignore);
		
		$char_without_tone  =  str_replace(array('ā','á','ǎ','à','ō','ó','ǒ','ò','ē','é','ě','è','ī','í','ǐ','ì','ū','ú','ǔ','ù','ǖ','ǘ','ǚ','ǜ','ü'),
										   array('a','a','a','a','o','o','o','o','e','e','e','e','i','i','i','i','u','u','u','u','v','v','v','v','v')
										   ,$char_with_tone );
		return $char_without_tone;
		
	}
	
	/*
	* 转成汉语拼音首字母
	* param $input_char String  需要转换的汉字
	* param $delimiter  String   转换之后拼音之间分隔符
	*/	
	public function TransformUcwords($input_char,$delimiter=''){
		
		$char_without_tone = ucwords($this->TransformWithoutTone($input_char,' ',true));
		$ucwords = preg_replace('/[^A-Z]/','',$char_without_tone);
		if(!empty($delimiter)){
			$ucwords = implode($delimiter,str_split($ucwords));
		}
		return $ucwords;
		
		
	}


	
	
	
	
	
}