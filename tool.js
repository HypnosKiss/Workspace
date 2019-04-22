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



