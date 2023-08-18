# InputTag 组件

## 简介

InputTag 组件。按回车键(Enter)生成标签！按回退键(Backspace)删除标签！



## 效果

[在线演示](https://www.jq22.com/yanshi23961)

![](https://images.gitee.com/uploads/images/2021/1225/132536_6b9bfaae_5507348.png)



![](https://images.gitee.com/uploads/images/2021/1225/132713_904ac333_5507348.gif)



## 示例

**JQuery 方式引入**

```html
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>JQuery标签输入框</title>
    <!-- 引入css -->
    <link rel="stylesheet" href="./inputTag.css">
</head>
<body>
<div>
    <div class="fairy-tag-container">
        <input type="text" class="fairy-tag-input tag1" autocomplete="off" value="">
    </div>

    <div id="tag1"></div>

    <div class="fairy-tag-container">
        <input type="text" class="fairy-tag-input tag2" autocomplete="off" value="">
    </div>

    <div id="tag2"></div>
</div>
<script src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
<!-- 引入js -->
<script src="./inputTag.js"></script>
<script>
    inputTag.render({
        elem: '.tag1',
        data: ['hello', 'world', 'tom', 'jerry'],//初始值
        permanentData: ['hello'],//不允许删除的值
        removeKeyNum: 8,//删除按键编号 默认，BackSpace 键
        createKeyNum: 13,//创建按键编号 默认，Enter 键
        beforeCreate: function (data, value) {//添加前操作，必须返回字符串才有效
            return val + '(XoX)';
        },
        onChange: function (data, value, type) {
            console.log(arguments);
            $('#tag1').text(JSON.stringify(data));
        }
    });

    inputTag.render({
        elem: '.tag2',
        data: ['你好', '世界', '汤姆', '杰瑞'],
        permanentData: ['世界'],
        onChange: function (data, value, type) {
            console.log(arguments);
            $('#tag2').text(JSON.stringify(data));
        }
    });
</script>
</body>
</html>
```



**Layui 方式引入**

```html
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Layui标签输入框</title>
    <!-- 引入css -->
    <link rel="stylesheet" href="./inputTag.css">
</head>
<body>
<div>
    <div class="fairy-tag-container">
        <input type="text" class="fairy-tag-input tag1" autocomplete="off" value="">
    </div>

    <div id="tag1"></div>

    <div class="fairy-tag-container">
        <input type="text" class="fairy-tag-input tag2" autocomplete="off" value="">
    </div>

    <div id="tag2"></div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/layui/2.6.8/layui.js" integrity="sha512-lH7rGfsFWwehkeyJYllBq73IsiR7RH2+wuOVjr06q8NKwHp5xVnkdSvUm8RNt31QCROqtPrjAAd1VuNH0ISxqQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!-- 引入js -->
<script>
    layui.config({
        base: './'
    }).use(['inputTag', 'jquery'], function () {
        var $ = layui.jquery, inputTag = layui.inputTag;

        inputTag.render({
            elem: '.tag1',
            data: ['hello', 'world', 'tom', 'jerry'],//初始值
            permanentData: ['hello'],//不允许删除的值
            removeKeyNum: 8,//删除按键编号 默认，BackSpace 键
            createKeyNum: 13,//创建按键编号 默认，Enter 键
            beforeCreate: function (data, value) {//添加前操作，必须返回字符串才有效
            	return val + '(XoX)';
        	},
            onChange: function (data, value, type) {
                console.log(arguments);
                $('#tag1').text(JSON.stringify(data));
            }
        });

        inputTag.render({
            elem: '.tag2',
            data: ['你好', '世界', '汤姆', '杰瑞'],
            permanentData: ['世界'],
            onChange: function (data, value, type) {
                console.log(arguments);
                $('#tag2').text(JSON.stringify(data));
            }
        });
    })
</script>
</body>
</html>
```

