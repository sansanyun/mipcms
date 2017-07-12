<?php
namespace mip;
/*
*  长文章分页类
*/
class Cutpage {
        private $pagestr;//被切分的内容 
        private $pagearr;//被切分文字的数组格式
        private $sum_word;//总字数(UTF-8格式的中文字符也包括)
        private $sum_page;//总页数
        private $page_word;//一页多少字
        private $cut_tag;//自动分页符
        private $cut_custom;//手动分页符
        private $ipage;//当前切分的页数，第几页
        private $url;
        
        function __construct($pagestr,$currentPageNum = 1,$page_word = 1000) {
            $this->page_word = $page_word;
            $this->cut_tag = array("</table>", "</div>", "</p>", "<br/>", "”。", "。", ".", "！", "……", "？", ",");
            $this->cut_custom = "{nextpage}";
            $tmp_page = intval(trim($currentPageNum));
            $this->ipage = $tmp_page > 1 ? $tmp_page : 1;
            $this->pagestr = $pagestr;
        }
         
        function cut_str(){
            $str_len_word = strlen($this->pagestr);
            $i = 0;
            if ($str_len_word <= $this->page_word) {
                $page_arr[$i] = $this->pagestr;
            } else {
                if (strpos($this->pagestr, $this->cut_custom)) {
                    $page_arr = explode($this->cut_custom, $this->pagestr);
                } else {
                    $str_first = substr($this->pagestr, 0, $this->page_word);
                    foreach ($this->cut_tag as $v) {
                        $cut_start = strrpos($str_first, $v);
                        if ($cut_start) {
                            $page_arr[$i++] = substr($this->pagestr, 0, $cut_start).$v;
                            $cut_start = $cut_start + strlen($v);
                            break;
                        }
                    }
                    if (($cut_start + $this->page_word) >= $str_len_word) {
                        $page_arr[$i++] = substr($this->pagestr, $cut_start, $this->page_word);
                    } else {
                        while (($cut_start + $this->page_word) < $str_len_word) {
                            foreach ($this->cut_tag as $v) {
                                $str_tmp = substr($this->pagestr, $cut_start, $this->page_word);
                                $cut_tmp = strrpos($str_tmp, $v);
                                if ($cut_tmp) {
                                    $page_arr[$i++] = substr($str_tmp, 0, $cut_tmp).$v;
                                    $cut_start = $cut_start + $cut_tmp + strlen($v);
                                    break;
                                }
                            }
                        }
                        if (($cut_start+$this->page_word)>$str_len_word) {
                            $page_arr[$i++] = substr($this->pagestr, $cut_start, $this->page_word);
                        }
                    }
                }
            }
            $this->sum_page = count($page_arr);//总页数
            $this->pagearr = $page_arr;
            return $page_arr;
        }
        function pagenav($currentPageNum, $url) {
            $str = '';
            for($i=1; $i <= $this->sum_page; $i++) {
                if($i == $this->ipage) {
                    $str.= "<li class='active'><a>".$i."</a></li>";
                } else {
                    if ($i == 1) {
                        $str.= "<li><a href='" . $url . ".html'>".$i."</a></li>";
                    } else {
                        $str.= "<li><a href='" . $url . "_" . $i .".html'>".$i."</a></li>";
                    }
                }
            }
            return $str;      
        }
        
    }