/**
 * js获取url中的参数值 正则法
 * @param name
 * @returns {*}
 */
function getQueryString(name) {
    var reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)', 'i');
    var r = window.location.search.substr(1).match(reg);
    if (r != null) {
        return unescape(r[2]);
    }
    return null;
}

/**
 * split拆分法
 * @returns {Object}
 * @constructor
 */
function GetRequest() {
    var url = location.search; //获取url中"?"符后的字串
    var theRequest = new Object();
    if (url.indexOf("?") != -1) {
        var str = url.substr(1);
        strs = str.split("&");
        for (var i = 0; i < strs.length; i++) {
            theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
        }
    }
    return theRequest;
}

//var Request = new Object();
//Request = GetRequest();

/**
 * 指定取
 * @param name
 * @returns {string}
 * @constructor
 */
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg); //获取url中"?"符后的字符串并正则匹配
    var context = "";
    if (r != null)
        context = r[2];
    reg = null;
    r = null;
    return context == null || context == "" || context == "undefined" ? "" : context;
}

/**
 * 单个参数的获取方法
 * @constructor
 */
function GetRequest() {
    var url = location.search; //获取url中"?"符后的字串
    if (url.indexOf("?") != -1) {  //判断是否有参数
        var str = url.substr(1); //从第一个字符开始 因为第0个是?号 获取所有除问号的所有符串
        strs = str.split("=");  //用等号进行分隔 （因为知道只有一个参数 所以直接用等号进分隔 如果有多个参数 要用&号分隔 再用等号进行分隔）
        alert(strs[1]);     //直接弹出第一个参数 （如果有多个参数 还要进行循环的）
    }
}

//获取地址栏参数，name:参数名称
function getUrlParms(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) {
        //return unescape(r[2]);
        return decodeURI(r[2]);
    }
    return null;
}

//滚动条自动加载-》》》》》
function scrollSelf(callback, extra_params) {
    $(window).scroll(function (event) {
        /*
        1、判断滚动条滚动到最底端：scrollTop == (offsetHeight – clientHeight)
        2、在滚动条距离底端50px以内：(offsetHeight – clientHeight) – scrollTop <= 50
        3、在滚动条距离底端5%以内：scrollTop / (offsetHeight – clientHeight) >= 0.95
        */
        var scrollHe = $(window).scrollTop();//当前滚动高度
        var scrollHeight = $(document).height();   //当前页面的总高度
        var clientHeight = $(this).height();    //当前可视的页面高度
        //if(scrollHeight - clientHeight - scrollHe <= 50) { //到达底部100px时,加载新内容
        if (scrollHe / (scrollHeight - clientHeight) >= 0.99) { //到达底部1%时,加载新内容
            // 这里加载数据..
            if (typeof callback === 'function') {
                callback.apply(this, [this, extra_params]);
            }
        }
    });
}

JSONToCSVConvertor(data, "Vehicle Report", true);

function JSONToCSVConvertor(JSONData, ReportTitle, ShowLabel) {
    var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;
    var CSV = '';
    CSV += ReportTitle + '\r\n\n';
    if (ShowLabel) {
        var row = "";
        for (var index in arrData[0]) {
            row += index + ',';
        }
        row = row.slice(0, -1);
        CSV += row + '\r\n';
    }
    for (var i = 0; i < arrData.length; i++) {
        var row = "";
        for (var index in arrData[i]) {
            row += '"' + arrData[i][index] + '",';
        }
        row.slice(0, row.length - 1);
        CSV += row + '\r\n';
    }
    if (CSV == '') {
        alert("Invalid data");
        return;
    }
    var fileName = "MyReport_";
    fileName += ReportTitle.replace(/ /g, "_");
    var uri = 'data:text/csv;charset=utf-8,' + escape(CSV);
    var link = document.createElement("a");
    link.href = uri;
    link.style = "visibility:hidden";
    link.download = fileName + ".csv";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

/**
 * 是否去除所有空格
 * @param str
 * @param is_global 如果为g或者G去除所有的
 * @returns
 */
function Trim(str,is_global) {
    var result;
    result = str.replace(/(^\s+)|(\s+$)/g,"");
    if(is_global.toLowerCase()=="g") {
        result = result.replace(/\s/g,"");
    }
    return result;
}

/**
 * 空格输入去除
 * @param e
 * @returns {Boolean}
 */
function inputSapceTrim(e,this_temp) {
    this_temp.value = Trim(this_temp.value,"g");
    var keynum;
    if(window.event) {// IE
        keynum = e.keyCode
    }
    else if(e.which){ // Netscape/Firefox/Opera
        keynum = e.which
    }
    if(keynum == 32){
        return false;
    }
    return true;
}

/**
 * 禁止空格输入
 * @param e
 * @returns {Boolean}
 */
function banInputSapce(e) {
    var keynum;
    if(window.event){ // IE
        keynum = e.keyCode
    }
    else if(e.which){ // Netscape/Firefox/Opera
        keynum = e.which
    }
    if(keynum == 32){
        return false;
    }
    return true;
}

/**
 * layer弹窗提示信息
 * @param msg 提示信息
 * @param icon 颜色 1-绿色、2-红色、3-橙色、4-灰色、
 * @param time 关闭时间
 * @param callback 回调函数
 */
function layerMsg(msg, icon, time, callback) {
    msg = msg ? msg : '成功';
    time = time ? time : 3000;
    icon = icon ? icon : 1;
    layer.msg(msg, {
        icon: icon,
        time: time //2秒关闭（如果不配置，默认是3秒）
    }, function () {
        if (typeof callback === "function") {
            callback();
        }
    });
}

/**
 *
 * @param msg 提示信息
 * @param element 选择器
 * @param position 位置 1-上 2-右（默认） 3-下 4-左
 * @param time 销毁时间
 * @param color 背景颜色 #3595CC
 */
function layerTips(msg, element, position = 2, time = 0, color = '#FF0000') {
    layer.tips(msg, element, {
        tips: [position, color],
        time: time
    });
}

/**
 * 刷新
 * @param time
 * @param $option
 */
function reload(time = 1000,$option) {
    setTimeout(function () {
        window.location.reload($option);
    }, time);
}

/**
 * 异步重载数据
 * @param element
 * @param url
 * @param data
 * @param callback
 */
function asyncReloadData(element,url,data,callback) {
    element.load(url, data, function() {
                     if (typeof callback === "function") {
                         callback();
                     }
                 }
    );
}

//----- @Desc计算文字的长度 -----
function getTextWidth(str) {
    var width = 0;
    var html = document.createElement('span');
    html.innerText = str;
    html.className = 'getTextWidth';
    document.querySelector('body').appendChild(html);
    width = document.querySelector('.getTextWidth').offsetWidth;
    document.querySelector('.getTextWidth').remove();
    return width;
}
//----- END -----

/**
 * 阻止浏览器的默认行为
 * @param e
 * @returns {boolean}
 */
function stopDefault( e ) {
    console.log(e);
    //阻止默认浏览器动作(W3C)
    if (e && e.preventDefault)
        e.preventDefault();
    //IE中阻止函数器默认动作的方式
    else
        window.event.returnValue = false;
    return false;
}

/**
 * 停止冒泡行为
 * @param e
 */
function stopBubble(e) {
    console.log(e);
    //如果提供了事件对象，则这是一个非IE浏览器
    if (e && e.stopPropagation)
    //因此它支持W3C的stopPropagation()方法
        e.stopPropagation();
    else
    //否则，我们需要使用IE的方式来取消事件冒泡
        window.event.cancelBubble = true;
}

/**
 * 同步执行
 * @param callback1
 * @param callback2
 * @param callback1_param
 * @param callback2_param
 * @returns {Promise<void>}
 */
async function doSomeWork(callback1,callback2,callback1_param=[],callback2_param=[],$is_rely=true) {
    // callback.apply(this, [param1, param2]);
    await callback1.apply(this,callback1_param);
    callback2.apply(this,callback2_param);
}

/**
 * 等待执行
 * @param time
 * @returns {Promise<any>}
 */
function sleep (time) {
    // 用法
    /*sleep(500).then(() => {
     // 这里写sleep之后需要去做的事情
     });*/
    return new Promise((resolve) => setTimeout(resolve, time));
}

/**
 * 同步执行
 * @param callback1
 * @param callback2
 * @param callback1_param
 * @param callback2_param
 * @param $is_rely
 */
function sync(callback1,callback2,callback1_param=[],callback2_param=[],$is_rely=true) {
    // callback.apply(this, [param1, param2]);
    new Promise(function (resolve) {
        let result = callback1.apply(this, callback1_param);
        resolve(result);
    }).then(function (val) {
        if ($is_rely === true) {
            if (val) {
                callback2.apply(this,callback2_param);
            }else {
                console.log(val);
            }
        } else {
            callback2.apply(this,callback2_param);
        }
    });
}



