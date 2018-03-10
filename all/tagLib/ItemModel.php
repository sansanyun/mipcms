<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace tagLib;
use mip\Paginationm;
use mip\Init;

class ItemModel extends Init
{
    public function getContentFilterByItemInfo($itemInfo)
    {
        if (!$itemInfo) {
            return false;
        }
        $itemInfo['content'] = htmlspecialchars_decode($itemInfo['content']);
        preg_match_all('/<img.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/', $itemInfo['content'], $imagesArray);
        $patern = '/^^((https|http|ftp)?:?\/\/)[^\s]+$/';
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
                $src = $this->domain . $imagesArray[1][$key];
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
        return $itemInfo;
    }
}