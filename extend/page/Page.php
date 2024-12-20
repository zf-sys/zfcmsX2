<?php
namespace page;
use think\Paginator;
class Page extends Paginator
{

    //首页
    protected function home() {
        if ($this->currentPage() > 1) {
            return "<a href='" . $this->url(1) . "' title='首页' class='pjax-link'>首页</a>";
        } else {
            return "<p class='disabled'>首页</p>";
        }
    }

    //上一页
    protected function prev() {
        if ($this->currentPage() > 1) {
            return "<a href='" . $this->url($this->currentPage - 1) . "' title='上一页' class='pjax-link'>上一页</a>";
        } else {
            return "<p class='disabled'>上一页</p>";
        }
    }

    //下一页
    protected function next() {
        if ($this->hasMore) {
            return "<a href='" . $this->url($this->currentPage + 1) . "' title='下一页' class='pjax-link'>下一页</a>";
        } else {
            return"<p class='disabled'>下一页</p>";
        }
    }

    //尾页
    protected function last() {
        if ($this->hasMore) {
            return "<a href='" . $this->url($this->lastPage) . "' title='尾页' class='pjax-link'>尾页</a>";
        } else {
            return "<p class='disabled'>尾页</p>";
        }
    }

    //统计信息
    protected function info(){
        $html='<p class="pageRemark">总共有<b>' . $this->lastPage .'</b>页，';
        $html.='<b>'. $this->total . '</b>条数据&nbsp;&nbsp;';
        $psize = input('psize',5);
//        $html.='<select id="pagesize" lay-filter="pagesize" > ';
//        foreach ([5,10,20,30,50,100] as $size){
//            if ($size == $psize){
//                $html.='<option value="'.$size.'" selected="">'.$size.' 条/页</option>';
//            }else{
//                $html.='<option value="'.$size.'">'.$size.' 条/页</option>';
//            }
//        }
//        $html.='</select> ';
//        $html.='&nbsp;&nbsp;到第<input id="pageInput" type="text" min="1" value="' . $this->currentPage . '" class="page-input">页 ';
//        $html.='<button type="button" href="javascript:void(0)" onclick="gotoPage()" class="page-btn">确定</button> ';
        $html.='</p>';
        return $html;
    }

    /**
     * 页码按钮
     * @return string
     */
    protected function getLinks()
    {

        $block = [
            'first'  => null,
            'slider' => null,
            'last'   => null
        ];

        $side   = 3;
        $window = $side * 2;

        if ($this->lastPage < $window + 6) {
            $block['first'] = $this->getUrlRange(1, $this->lastPage);
        } elseif ($this->currentPage <= $window) {
            $block['first'] = $this->getUrlRange(1, $window + 2);
            $block['last']  = $this->getUrlRange($this->lastPage - 1, $this->lastPage);
        } elseif ($this->currentPage > ($this->lastPage - $window)) {
            $block['first'] = $this->getUrlRange(1, 2);
            $block['last']  = $this->getUrlRange($this->lastPage - ($window + 2), $this->lastPage);
        } else {
            $block['first']  = $this->getUrlRange(1, 2);
            $block['slider'] = $this->getUrlRange($this->currentPage - $side, $this->currentPage + $side);
            $block['last']   = $this->getUrlRange($this->lastPage - 1, $this->lastPage);
        }

        $html = '';

        if (is_array($block['first'])) {
            $html .= $this->getUrlLinks($block['first']);
        }

        if (is_array($block['slider'])) {
            $html .= $this->getDots();
            $html .= $this->getUrlLinks($block['slider']);
        }

        if (is_array($block['last'])) {
            $html .= $this->getDots();
            $html .= $this->getUrlLinks($block['last']);
        }

        return $html;
    }

    /**
     * 渲染分页html
     * @return mixed
     */
    public function render()
    {
        //if ($this->hasPages())注释原因：当总行数小于设置的每页显示条数时依旧会显示分页栏，取消注释后相同情况下则消失。
        /*if ($this->hasPages()) {*/
        if ($this->simple) {
            return sprintf(
                '%s<div class="pagination">%s %s %s</div>',
                $this->css(),
                $this->prev(),
                $this->getLinks(),
                $this->next()
            );
        } else {
            return sprintf(
                '%s<div class="pagination">%s %s %s %s %s %s</div>%s',
                $this->css(),
                $this->home(),
                $this->prev(),
                $this->getLinks(),
                $this->next(),
                $this->last(),
                $this->info(),
                $this->js()
            );
        }
        /*}*/
    }

    /**
     * 生成一个可点击的按钮
     *
     * @param  string $url
     * @param  int    $page
     * @return string
     */
    protected function getAvailablePageWrapper($url, $page)
    {
        return '<a href="' . htmlentities($url) . '" class="pjax-link">' . $page . '</a>';
    }

    /**
     * 生成一个禁用的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getDisabledTextWrapper($text)
    {
        return '<p class="pageEllipsis">' . $text . '</p>';
    }

    /**
     * 生成一个激活的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getActivePageWrapper($text)
    {
        return '<a href="" class="cur">' . $text . '</a>';
    }

    /**
     * 生成省略号按钮
     *
     * @return string
     */
    protected function getDots()
    {
        return $this->getDisabledTextWrapper('...');
    }

    /**
     * 批量生成页码按钮.
     *
     * @param  array $urls
     * @return string
     */
    protected function getUrlLinks(array $urls)
    {
        $html = '';

        foreach ($urls as $page => $url) {
            $html .= $this->getPageLinkWrapper($url, $page);
        }

        return $html;
    }

    /**
     * 生成普通页码按钮
     *
     * @param  string $url
     * @param  int    $page
     * @return string
     */
    protected function getPageLinkWrapper($url, $page)
    {
        if ($page == $this->currentPage()) {
            return $this->getActivePageWrapper($page);
        }

        return $this->getAvailablePageWrapper($url, $page);
    }
    protected function js(){
        return '<script type="text/javascript">
		var page=document.getElementById("pagesize"); 
		page.addEventListener("change", function() {
            var _index = this.selectedIndex;
            var size = this.options[_index].value;
            var url="'.$this->url(1).'";	
            if(url.indexOf("&psize") !=-1) {
                var index=url.lastIndexOf("&psize");
                url=url.substring(0,index);
            }
            url+="&psize="+size;
            window.location.href=url
		});
		layui.use([ "form"], function(){
            var $ = layui.$
            ,form = layui.form
            form.on("select(pagesize)",function(data){
                // var move_cid = data.elem[data.elem.selectedIndex].text
                var size = data.value
                var url="'.$this->url(1).'";	
                if(url.indexOf("&psize") !=-1) {
                    var index=url.lastIndexOf("&psize");
                    url=url.substring(0,index);
                }
                url+="&psize="+size;
                window.location.href=url
            })
        })

		function gotoPage(){
			var input=document.getElementById("pageInput");
			var url="'.$this->url(1).'";
			if(url.indexOf("&page") !=-1) {
				var index=url.lastIndexOf("&page");
				url=url.substring(0,index);
			}
			url+="&page="+input.value;
			window.location.href=url
		}
		</script>';
    }

    /**
     * 分页样式
     */
    protected function css(){
        return '  <style type="text/css">
			.page-box,
			.page-box * {
				text-align:center;
				box-sizing: content-box;
			}
            .pagination p{
                margin:0;
                cursor:pointer
            }
            .pagination{
                height:40px;
                padding:20px 0px;
            }
            .pagination a{
                display:block;
                float:left;
                margin-right:10px;
                padding:2px 12px;
                height:24px;
                border:1px #cccccc solid;
                background:#fff;
                text-decoration:none;
                color:#000000;
                font-size:12px;
                line-height:20px;
            }
            .pagination a:hover{
                color:#077ee3;
                background: white;
                border:1px #077ee3 solid;
            }
            .pagination a.cur{
                border:none;
                background:#077ee3;
                color:#fff;
            }
			.pagination p{
                float:left;
                padding:2px 12px;
                font-size:12px;
                height:26px;
                line-height:20px;
                color:#bbb;
                margin-right:8px;
            }
            .pagination p.disabled{
                border:1px #ccc solid;
                background:#fcfcfc;
				cursor:not-allowed;
            }
            .pagination p.pageRemark{
				display:block;
                margin-right:10px;
				font-size:14px;
                color:#666;
            }
			/*屏幕宽度小于992px时*/
			@media all and (max-width: 992px) {
				.pagination p.pageRemark{
					display: none;
				}
			}
            .pagination p.pageRemark b{
                color:red;
            }
			.pagination p select{
				font-size:12px;
				margin: 0 5px;
				padding: 0 10px;
				text-align: center;
				height: 24px;
				line-height: 24px;
				border: 1px solid #e2e2e2;
				border-radius: 2px;
				vertical-align: top;
				background-color: #fff;
				box-sizing: border-box;
            }
            .pagination p.pageEllipsis{
                border-style:none;
                background:none;
                padding:4px 0px;
                color:#808080;
				cursor: not-allowed;
            }
            .dates li {font-size: 14px;margin:20px 0}
            .dates li span{float:right}
			.page-input{
				display: inline-block;
				width: 40px;
				margin: 0 5px;
				padding: 0 5px;
				text-align: center;
				height: 24px;
				line-height: 24px;
				border: 1px solid #e2e2e2;
				border-radius: 2px;
				vertical-align: top;
				background-color: #fff;
				box-sizing: border-box;
			}
			.page-btn{
                font-size:12px;
				margin: 0 5px;
				padding: 0 10px;
				text-align: center;
				height: 24px;
				line-height: 24px;
				border: 1px solid #e2e2e2;
				border-radius: 2px;
				vertical-align: top;
				background-color: #fff;
				box-sizing: border-box;
            }
            .page-btn:hover{
                color:#077ee3;
                background: white;
                border:1px #077ee3 solid;
            }
        </style>';
    }
}