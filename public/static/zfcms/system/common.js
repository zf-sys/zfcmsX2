// +----------------------------------------------------------------------
// | 子枫CMS管理系统
// +----------------------------------------------------------------------
// | Copyright (c)  http://store.zf-sys.com/
// | 子枫CMS管理系统提供免费使用,可使用此框架进行二次开发
// +----------------------------------------------------------------------
// | Author: 子枫 <287851074@qq.com>
// | 子枫社区:  http://bbs.90ckm.com/
// +----------------------------------------------------------------------
var urlparse;
urlparse = document.scripts[document.scripts.length - 1].src.split("\?");
 
var values = {};
if(urlparse.length > 1){
    var parms = urlparse[1].split("&");
    for (var i = 0; i < parms.length; i++) {
        var parm = parms[i].split("=");
        values[parm[0]] = parm[1];
    }
}
// console.log(values);
// {v: "123", p: "445"}
layui.define(['jquery', 'form', 'layer', 'element','table'], function(exports) {
	var $ = layui.jquery,
		form = layui.form,
        table = layui.table,
		layer = layui.layer,
		element = layui.element;
	var menu = [];
	var curMenu;

	/**
	 * 
	 * 修改排序
	 */
	$(document).on('blur', '.edit_sort', function(){
		var id = $(this).attr('item-id')
		var  dbname= $(this).attr('item-dbname')
		var field = $(this).attr('item_f')
		if(field==undefined){
			field = 'sort'
		}
		var value = $(this).val(); //得到修改后的值
		$.get("/admin/common/value_edit",{id:id,dbname:dbname,field:field,value:value},function(res){
			if(res.result==1){
				window.location.reload();
			}else{
				layer.msg(res.msg, {icon: 2});
			}
		},"json");
	})
	$(document).on('click', '.zfcms_picshow', function(){
		var pic = $(this).attr("src")
		layer.photos({
			photos: {
				"title": "预览",
				"start": 0,
				"data": [
					{
						"alt": "",
						"pid": 1,
						"src": pic,
					}
				]
			}
		});
	})

	form.on('switch(zstatus_change)', function(data){
		var id = $(this).attr("item_id")
		var field = $(this).attr("item_f")
		var dbname = $(this).attr("item_dbname")
		var status = this.checked ? '1' : '0'
		$.get("/admin/common/value_status",{id:id,dbname:dbname,status:status,field:field},function(res){
			if(res.result==1){
				layer.msg(res.msg, {icon: 1});
			}else{
				layer.msg(res.msg, {icon: 2});
			}
		},"json");
	});
	/*
	 * @todo 弹出层，弹窗方法
	 * layui.use 加载layui.define 定义的模块，当外部 js 或 onclick调用 use 内部函数时，需要在 use 中定义 window 函数供外部引用
	 * http://blog.csdn.net/xcmonline/article/details/75647144 
	 */
	/*
	    参数解释：
	    title   标题
	    url     请求的url
	    id      需要操作的数据id
	    w       弹出层宽度（缺省调默认值）
	    h       弹出层高度（缺省调默认值）
	*/
	window.zfAdminShow = function(title, url, w, h) {
		if(values.tan_type=="newwindow"){
			//打开新窗口
			window.open(url);
		}else{
			if(title == null || title == '') {
				title = false;
			};
			if(url == null || url == '') {
				url = "404.html";
			};
			if(w == null || w == '') {
				w = '90%'
			};
			if(h == null || h == '') {
				h = '90%'
			};
			//判断w是否含有%  || 是否是数字
			if(jQuery.isNumeric(w) || w.indexOf("%")===-1) {
				w = w + 'px'
			}
			//判断h是否含有%
			if(jQuery.isNumeric(h) || h.indexOf("%")===-1){
				h = h + 'px'
			}
			layer.open({
				type: 2,
				area: [w, h],
				fix: false, //不固定
				maxmin: true,
				shadeClose: false,
				shade: 0.4,
				title: title,
				content: url,
				cancel: function(index, layero){ 
				}   
			});
		}
		
	}
	/*弹出层+传递ID参数*/
	window.zfAdminEdit = function(title, url, id, w, h) {
		if(title == null || title == '') {
			title = false;
		};
		if(url == null || url == '') {
			url = "404.html";
		};
		if(w == null || w == '') {
			w = '90%'
		};
		if(h == null || h == '') {
			h = '90%'
		};
		//判断w是否含有%
		if(jQuery.isNumeric(w) || w.indexOf("%")===-1){
			w = w + 'px'
		}
		//判断h是否含有%
		if(jQuery.isNumeric(h) || h.indexOf("%")===-1){
			h = h + 'px'
		}
		layer.open({
			type: 2,
			area: [w, h],
			fix: false, //不固定
			maxmin: true,
			shadeClose: true,
			shade: 0.4,
			title: title,
			content: url,
			success: function(layero, index) {
				//向iframe页的id=house的元素传值  // 参考 https://yq.aliyun.com/ziliao/133150
				var body = layer.getChildFrame('body', index);
				body.contents().find("#dataId").val(id);
				console.log(id);
			},
			error: function(layero, index) {
				alert("aaa");
			}
		});
	}


	/*删除
		role 控制器/方法
		id
		type
	*/
    window.btn_del = function(role,id,db,type){
		layer.confirm('确认删除？', {
		  btn: ['删除','取消'] //按钮
		}, function(){
		  //执行删除操作
		  $.get(role,{id:id,db:db},function(res){
			if(res.result==1){
			  layer.msg("删除成功", {icon: 1});
			  setTimeout(function() {
				location.reload(true);
			  }, 1000);
			}else{
			  layer.msg(res.msg, {icon: 2});
			}
		  },"json");
  
		}, function(){
			//取消的操作
		});
	  }
	/*转化
		dbname数据库名,id,现在的状态
	*/
	window.is_switch = function(dbname,id,status){
		$.ajax({
		  type:'post',
		  url:"/admin/common/is_switch",
		  data:{dbname:dbname,id:id,status:status},
		  dataType:'json',
		  success:function(res){
		  // console.log(res)
		  if(res.result==1){
			layer.msg("转换成功", {icon: 1});
			setTimeout(function() {
			window.location.reload();
			}, 100);
		  }else{
			layer.msg("转换失败", {icon: 2}); 
		  }  
		  }
		})
	  }
	/*添加
		role,
		type  1关闭子类刷新父类  0刷新子类
	*/
	window.tijiao_data = function(role,type=1){
        var index = layer.load(2);
		var data = $(".info_tj input,.info_tj select,.info_tj textarea,.info_tj option,.info_tj radio").serialize();      
      	$.ajax({
          type:'post',
          url:role,
          data:data,
          dataType:'json',
          success:function(res){
          	console.log(res)
            if(res.result==1){
              layer.msg(res.msg, {icon: 1});
              layer.close()
              setTimeout(function() {
				  if(type==1){
					window.parent.location.reload();
				  }else{
					window.location.reload();
				  }
              }, 2000);
            }else{
              layer.msg(res.msg, {icon: 2});
              layer.close(index)
            }   
          }
      	})
	 }

	 /**
     * ajax请求操作
     * @attr href或data-href 请求地址
     * @attr refresh 操作完成后是否自动刷新
     * @class confirm confirm提示内容
     */
    // $(document).on('click', '.j-ajax,.zf-ajax', function() {
    //     var that = $(this),
    //         href = !that.attr('data-href') ? that.attr('href') : that.attr('data-href'),
    //         refresh = !that.attr('refresh') ? 'true' : that.attr('refresh');
    //     if (!href) {
    //         layer.msg('请设置data-href参数');
    //         return false;
    //     }
	//
    //     if (!that.attr('confirm')) {
    //         layer.msg('数据提交中...', {time:500000});
    //         $.get(href, {}, function(res) {
    //             layer.msg(res.msg, {}, function() {
    //                 if (refresh == 'true' || refresh == 'yes') {
    //                     if (typeof(res.url) != 'undefined' && res.url != null && res.url != '') {
    //                         location.href = res.url;
    //                     } else {
    //                         location.reload();
    //                     }
    //                 }
    //             });
    //         });
    //         layer.close();
    //     } else {
    //         layer.confirm(that.attr('confirm'), {title:false, closeBtn:0}, function(index){
    //             layer.msg('数据提交中...', {time:500000});
    //             $.get(href, {}, function(res) {
    //                 layer.msg(res.msg, {}, function() {
    //                     if (refresh == 'true') {
    //                         if (typeof(res.url) != 'undefined' && res.url != null && res.url != '') {
    //                             location.href = res.url;
    //                         } else {
    //                             location.reload();
    //                         }
    //                     }
    //                 });
    //             });
    //             layer.close(index);
    //         });
    //     }
    //     return false;
    // });


    /**
     * 列表页批量操作按钮组
     * @attr href 操作地址
     * @attr data-table table容器ID
     * @class confirm 类似系统confirm
     * @attr tips confirm提示内容
     */
    // $(document).on('click', '.j-page-btns,.zf-page-btns', function(){
    //     var that = $(this),
	// 	query = '',
	// 	code = function(that) {
	// 		var href = that.attr('href') ? that.attr('href') : that.attr('data-href');
	// 		var tableObj = that.attr('data-table') ? that.attr('data-table') : 'dataTable';
	// 		if (!href) {
	// 			layer.msg('请设置data-href参数');
	// 			return false;
	// 		}
	//
	// 		if ($('.checkbox-ids:checked').length <= 0) {
	// 			var checkStatus = table.checkStatus(tableObj);
	// 			if (checkStatus.data.length <= 0) {
	// 				layer.msg('请选择要操作的数据');
	// 				return false;
	// 			}
	// 			for (var i in checkStatus.data) {
	// 				if (i > 0) {
	// 					query += '&';
	// 				}
	// 				query += 'id[]='+checkStatus.data[i].id;
	// 			}
	// 		} else {
	// 			if (that.parents('form')[0]) {
	// 				query = that.parents('form').serialize();
	// 			} else {
	// 				query = $('#pageListForm').serialize();
	// 			}
	// 		}
	//
	// 		layer.msg('数据提交中...',{time:500000});
	// 		$.post(href, query, function(res) {
	// 			layer.msg(res.msg, {}, function(){
	// 				if (res.result != 0) {
	// 					location.reload();
	// 				}else{
	// 					location.reload();
	// 				}
	// 			});
	//
	// 		});
	// 	};
    //     if (that.hasClass('confirm')) {
    //         var tips = that.attr('tips') ? that.attr('tips') : '您确定要执行此操作吗？';
    //         layer.confirm(tips, {title:false, closeBtn:0}, function(index){
    //             code(that);
    //             layer.close(index);
    //         });
    //     } else {
    //        code(that);
    //     }
    //     return false;
    // });
	//




	//选中
	$(document).on('click', '.zf_all_check', function() {
		var check = $(this).text();
		if (check == '全选') {
			$('input[name="ids[]"]').prop('checked', true);
			$(this).text('取消');
		} else {
			$('input[name="ids[]"]').prop('checked', false);
			$(this).text('全选');
		}
		form.render('checkbox');
	})
	//批量删除
	$(document).on('click', '.pl_del', function() {
		var ids = [];
		$('input[name="ids[]"]:checked').each(function(){
			ids.push($(this).val());
		});
		if(ids.length==0){
			layer.msg('请选择要删除的数据', {icon: 2});
			return false;
		}
		layer.confirm('确认删除？', {
			btn: ['删除','取消']
		}, function(){
			$.get("/admin/common/more_del",{ids:ids,dbname:'post'},function(res){
				if(res.result==1){
					layer.msg("删除成功", {icon: 1});
					setTimeout(function() {
						location.reload(true);
					}, 1000);
				}else{
					layer.msg(res.msg, {icon: 2});

				}
			},"json");

		}, function(){
			//取消的操作
		});
	})

	// 删除
	$(document).on('click', '.zf_btn_del', function() {
		var url = $(this).attr("rel");
		layer.confirm('确认删除？', {
			btn: ['删除','取消']
		}, function(){

			$.get(url, {},function(res){
				if(res.result==1){
					layer.msg("删除成功", {icon: 1});
					setTimeout(function() {
						location.reload(true);
					}, 1000);
				}else{
					layer.msg(res.msg, {icon: 2});

				}
			},"json");

		}, function(){
			//取消的操作
		});
	})
	
});