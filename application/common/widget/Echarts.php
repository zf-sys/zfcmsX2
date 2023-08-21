<?php
namespace app\common\widget;
use think\Controller;

class Echarts {
	

	// 柱状图
  	public function line($title='',$name='',$data=[],$style='width: 600px;height:400px;')
    {
        $tpl_id='zf_'.mt_rand().'_'.time();
        $data_json = json_encode($data);
        $zf_html = <<<INFO
<div id="$name" style="$style"></div>
	<script type="text/javascript">
        var $name = echarts.init(document.getElementById('$name'));
        var data = JSON.parse('$data_json');
        var option = {
            title: {
                text:"$title"
            },
		    tooltip: data.tooltip,
		    grid:data.grid,
		    legend: data.legend,
			xAxis: data.xAxis,
            yAxis:  data.yAxis,
            series: data.series
        };
        $name.setOption(option);
    </script>


INFO;
        return $zf_html;
    }

  

// 折线图
   public function line_stack($title='',$name='',$data=[],$style='width: 600px;height:400px;'){
   		$tpl_id='zf_'.mt_rand().'_'.time();
        $data_json = json_encode($data);
        $zf_html = <<<INFO
<div id="$name" style="$style"></div>
	<script type="text/javascript">
        var $name = echarts.init(document.getElementById('$name'));
        var data = JSON.parse('$data_json');
        var option = {
			    title: {text: "$title"},
			    tooltip: data.tooltip,
			    legend: data.legend,
			    grid:data.grid,
			    toolbox: {
			        feature: {
			            saveAsImage: {}
			        }
			    },
			    xAxis: data.xAxis,
	            yAxis:  data.yAxis,
			    series: data.series
			};

        $name.setOption(option);
    </script>
INFO;
        return $zf_html;
   }
// 饼状图
   public function pie_legend($title='',$name='',$data=[],$style='width: 600px;height:400px;'){
   		$tpl_id='zf_'.mt_rand().'_'.time();
        $data_json = json_encode($data);
        $zf_html = <<<INFO
<div id="$name" style="$style"></div>
	<script type="text/javascript">
        var $name = echarts.init(document.getElementById('$name'));
        var data = JSON.parse('$data_json');
        var option = {
			    title: {text: "$title"},
			    tooltip: data.tooltip,
			    legend: data.legend,
			    grid:data.grid,
			    toolbox: {
			        feature: {
			            saveAsImage: {}
			        }
			    },
			    series : data.series
			};
        $name.setOption(option);
    </script>
INFO;
        return $zf_html;
   }
    

   


   
}