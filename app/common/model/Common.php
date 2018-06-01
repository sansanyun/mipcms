<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\common\model;
use mip\Init;
class Common
{
    public function getContentFilterByContent($content)
    {
        $itemInfo['content'] = $content;
        $itemInfo['content'] =  preg_replace("/style=.+?['|\"]/i",'', $itemInfo['content']);
        preg_match_all('/<img.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/', $itemInfo['content'], $imagesArray);
        $patern = '/^^((https|http|ftp)?:?\/\/)[^\s]+$/';
        foreach($imagesArray[0] as $key => $val) {
            @preg_match("/alt=[\'|\"](.*?)[\'|\"]/",$val,$tempAlt);
            if ($tempAlt) {
                $alt = $tempAlt[1];
            }
            @preg_match("/width=[\'|\"](.*?)[\'|\"]/",$val,$tempWidth);
            @preg_match("/height=[\'|\"](.*?)[\'|\"]/",$val,$tempHeight);
            $src = $imagesArray[1][$key];
            if (@preg_match($patern,$src)) {
               $src = $src;
            } else {
                if (strpos($src,';base64,') === false) {
                    $src = config('domainStatic') . $src;
                }
            }
            if ($tempWidth && $tempHeight) {
                if ($tempWidth > 500) {
                    $layout = '';
                    $tempImg = '<mip-img '.$layout.' alt="'.$alt.'" src="'.$src.'" popup></mip-img>';
                } else {
                    $layout = 'layout="fixed"';
                    $tempImg = '<mip-img ' .$layout. ' ' . $tempWidth[0] . ' ' . $tempHeight[0] .' alt="'.$alt.'" src="'.$src.'" popup></mip-img>';
                }
            } else {
                $layout = '';
                $tempImg = '<mip-img '.$layout.' alt="'.$alt.'" src="'.$src.'" popup></mip-img>';
            }
            $itemInfo['content'] =  str_replace($val,$tempImg,$itemInfo['content']);
        }
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

}