docker pull docker.elastic.co/elasticsearch/elasticsearch:7.2.0
docker run -p 9200:9200 -p 9300:9300 -e "discovery.type=single-node" docker.elastic.co/elasticsearch/elasticsearch:7.2.0
The vm.max_map_count kernel setting needs to be set to at least 262144 for production use. Depending on your platform:
## 注意事项
Linux

The vm.max_map_count setting should be set permanently in /etc/sysctl.conf:

$ grep vm.max_map_count /etc/sysctl.conf
vm.max_map_count=262144
To apply the setting on a live system type: sysctl -w vm.max_map_count=262144

macOS with Docker for Mac

The vm.max_map_count setting must be set within the xhyve virtual machine:

$ screen ~/Library/Containers/com.docker.docker/Data/vms/0/tty
Just press enter and configure the sysctl setting as you would for Linux:

sysctl -w vm.max_map_count=262144
Windows and macOS with Docker Toolbox

The vm.max_map_count setting must be set via docker-machine:

docker-machine ssh
sudo sysctl -w vm.max_map_count=262144

docker-compose.yml:

docker-compose up

version: '2.2'
services:
  es01:
    image: docker.elastic.co/elasticsearch/elasticsearch:7.2.0
    container_name: es01
    environment:
      - node.name=es01
      - discovery.seed_hosts=es02
      - cluster.initial_master_nodes=es01,es02
      - cluster.name=docker-cluster
      - bootstrap.memory_lock=true
      - "ES_JAVA_OPTS=-Xms512m -Xmx512m"
    ulimits:
      memlock:
        soft: -1
        hard: -1
    volumes:
      - esdata01:/usr/share/elasticsearch/data
    ports:
      - 9200:9200
    networks:
      - esnet
    image: docker.elastic.co/elasticsearch/elasticsearch:7.2.0
    container_name: es02
    environment:
      - node.name=es02
      - discovery.seed_hosts=es01
      - cluster.initial_master_nodes=es01,es02
      - cluster.name=docker-cluster
      - bootstrap.memory_lock=true
      - "ES_JAVA_OPTS=-Xms512m -Xmx512m"
    ulimits:
      memlock:
        soft: -1
        hard: -1
    volumes:
      - esdata02:/usr/share/elasticsearch/data
    networks:
      - esnet

volumes:
  esdata01:
    driver: local
  esdata02:
    driver: local

  esnet:
To stop the cluster, type docker-compose down. Data volumes will persist, so it’s possible to start the cluster again with the same data using docker-compose up. To destroy the cluster and the data volumes, just type docker-compose down -v.

Inspect status of cluster:edit
curl http://127.0.0.1:9200/_cat/health
1472225929 15:38:49 docker-cluster green 2 2 4 2 0 0 0 0 - 100.0%


#########################  ###########################


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id="sixapart-standard">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="generator" content="Movable Type  5.2.2" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<!--link rel="stylesheet" href="http://www.ruanyifeng.com/blog/styles.css" type="text/css" /-->
<link rel="start" href="http://www.ruanyifeng.com/blog/" title="Home" />
<link rel="alternate" type="application/atom+xml" title="Recent Entries" href="http://feeds.feedburner.com/ruanyifeng" />
<script type="text/javascript" src="http://www.ruanyifeng.com/blog/mt.js"></script>
<!--
<rdf:RDF xmlns="http://web.resource.org/cc/"
         xmlns:dc="http://purl.org/dc/elements/1.1/"
         xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
<Work rdf:about="http://www.ruanyifeng.com/blog/">
<dc:title>阮一峰的网络日志</dc:title>
<dc:description>Ruan YiFeng&apos;s Blog</dc:description>
<license rdf:resource="http://creativecommons.org/licenses/by-nc-nd/3.0/" />
</Work>
<License rdf:about="http://creativecommons.org/licenses/by-nc-nd/3.0/">
</License>
</rdf:RDF>
-->

<style>
body {
  background-color: #f5f5d5;
}

#container::before {
  display: block;
  width: 100%;
  padding: 10px;
  background: rgba(0,0,0,0.1);
  text-align: center;
  content: "本站显示不正常，可能因为您使用了广告拦截器。";
}

</style>
<script>
function loadjscssfile(filename, filetype){
    if (filetype=="js"){ //if filename is a external JavaScript file
        var fileref=document.createElement('script')
        fileref.setAttribute("type","text/javascript")
        fileref.setAttribute("src", filename)
    }
    else if (filetype=="css"){ //if filename is an external CSS file
        var fileref=document.createElement("link")
        fileref.setAttribute("rel", "stylesheet")
        fileref.setAttribute("type", "text/css")
        fileref.setAttribute("href", filename)
    }
    if (typeof fileref!="undefined")
        document.getElementsByTagName("head")[0].appendChild(fileref)
}
//loadjscssfile("http://www.ruanyifeng.com/blog/styles.css", "css");
loadjscssfile('/static/themes/theme_scrapbook/theme_scrapbook.css', "css");


function checker() {
// var img = document.querySelector('img[src^="http://www.ruanyifeng.com/blog/images"]');
var img = document.querySelector('a > img[src*="wangbase.com/blogimg/asset/"]');
  if (img && window.getComputedStyle(img).display === 'none'){
    var sponsor = document.querySelector('.entry-sponsor');
    var prompt = document.createElement('div');
    prompt.style = 'border: 1px solid #c6c6c6;border-radius: 4px;background-color: #f5f2f0;padding: 15px; font-size: 14px;';
  prompt.innerHTML = '<p>您使用了广告拦截器，导致本站内容无法显示。</p><p>请将 www.ruanyifeng.com 加入白名单，解除广告屏蔽后，刷新页面。谢谢。</p>';
    sponsor.parentNode.replaceChild(prompt, sponsor);
    document.querySelector('#main-content').innerHTML = '';
  }
}

setTimeout(checker, 1000);

for (var jxx = 0; jxx < 10000; jxx++) {
  for (var jxy = 0; jxy < 10000; jxy++) {
  }
}
</script>

    
    <link rel="prev" href="http://www.ruanyifeng.com/blog/2017/08/koa.html" title="Koa 框架教程" />
    <link rel="next" href="http://www.ruanyifeng.com/blog/2017/08/smart-shoes.html" title="你的鞋都比你聪明" />
    
    <title>全文搜索引擎 Elasticsearch 入门教程 - 阮一峰的网络日志</title>
</head>
<body id="scrapbook" class="mt-entry-archive one-column">
<script>
if (/mobile/i.test(navigator.userAgent) || /android/i.test(navigator.userAgent)) document.body.classList.add('mobile');

/*
window.addEventListener('load', function(event) {
  setTimeout(function () {
    hab('#sup-post-2');
    hab('#cre-inner');
  }, 1000);
});
*/
</script>
    <div id="container">
        <div id="container-inner">

            <div id="header">
    <div id="header-inner">
        <div id="header-content">


            <div id="header-name">阮一峰的网络日志 <span id="site_location"> » <a href="http://www.ruanyifeng.com/blog/" accesskey="1">首页</a></span><span id="site_archive"> » <a href="http://www.ruanyifeng.com/blog/archives.html">档案</a></span>
</div>

<div id="google_search">
<!-- SiteSearch Google -->
<form action="https://www.baidu.com/s" target="_blank" method="get" id="cse-search-box">
  <div>
  <input type="text" size="20" name="origin" class="searchbox" id="sbi" value="" />
  <input type="hidden" name="wd" value="" />
  <!--input type="image" src="/static/themes/theme_scrapbook/images/top_search_submit.gif" class="searchbox_submit" value="" alt="搜索" name="sa" onclick="this.form.wd.value = this.form.origin.value + ' inurl:www.ruanyifeng.com/blog'" /-->
  <input type="image" src="/static/themes/theme_scrapbook/images/top_search_submit.gif" class="searchbox_submit" value="" alt="搜索" name="sa" onclick="this.form.wd.value = this.form.origin.value + ' 阮一峰'" />
</div>
</form>

<!-- SiteSearch Google -->
</div>
<div id="feed_icon">
<a href="/feed.html" title="订阅Feed">
<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAUzSURBVHjavFdbbFRVFF3nPjoz7dTWTittaW0jUDRAUqaNojyqREnEQKgfUj9MqqAmhqRt/OCD4CuY+Kckoh+aiGKC+gMJbdHoRysJ8dkhhmJLNdDKtJU+6GMK87j3Hs85d2Z6HzNtMYWb3Dn3NWftvfba+5xNYDl+e6Fkj6yqb/oDRbWq14vlPBLRKCITkxf0ROLt+hNjp1PPSRK4kA3vF1dXNRcWlyA2OQU9eos9opAkAiKxD+XkKO6t15aRWO7J/MgmAZU8MEgexgZHMX518Dh72sYMmVKShnxWuWHdHtxKIDIYTgMuDzgfmSOIQkYMpdUF8OY92Hytt4/jvkg47czzU16iQovM3QFwmNck+Yyduu7D6NA0Z6JR4THntFs9V4tWQg6Ui3s6MwKDncsFTnXKLJhDSeUK3AgPtyhccDzmVs999buRt/1Vm4i0od+hX7+MRG87jPGB/w1u8FPj9xEw7McVrnYuOCvtpjTth3J/nTg99c8LRhKhr6D3dTB5R24bXFwbMXBsyZzeoXaycEpJ95TB09AGX/NpqLVNtw8urnVzLvHjFNxiFqRy2OOHuqUVnue+ACkoWzo4O6lGzTmuHq6nPvY2m9rVqjrIK2rMEKxqyG5NPAKt+wjo0LklgfNxJkZMA3KJvqRUk3z5UFY3QH14P0h+WUY79HPvgv7VuSg4ZRGY1YgZgqXmORccF17sy2ehnf9AeO085K2HQFbtXBScj0LcpgF2cN+WV+DZ/LJQu6gD4R7oV7pBJwbSgtMvfiPoVp56DySwxm7EtkMs1WdAB7qzggsDJKQYsHucSkOudrkiCPWR/fA2nYCn8SNIK4NptSMyAu3sAdDRkIsJdfth0LzSrODUoPNZ4KI9SxJI5UHk7D4GdQfz2us31c7CoHMjRkKuDPHseCMrONVhNcDJwMJpKFVvg9L4OaTiNWm1x789KCqkrXhVBiEz0WYCT2nAzQAD1/vaETv1GrRfP4Vx5cfMNcDPwvP0h0DhanPym7OIf/+O67vcJ1/PCJ4KgdzaUP6Wz+dU+5yIL6fV+PsHGAOdwlPpvvUOyeeAVGyCdqkDNB6DPjsBSrnndfOGevOh3RhGItxvA+fX1CtbGFhgYUFkFMZPR6F1HnClHq8HyubWtJexX06CRmdt33hrd7nA7SFY4qoGpnYuOKcRykPPgDCBcsHx9Iv+fNL2PueBehCWUfYQIIMGLOCcOmXDXsh1+yCt35tUPfvzGFuSvzvoinXOxqa02qOhM6733nVP2MAdaej2XN11DPKjLZCD+yBvahGCo7JfTKAN9UD7s8Oe9zUNIhz8fWI8DG2k38WCFdxugANcXrvTVd1IEbuv3Jour7Hzn7jLMBNfKs7R3i67gRVrbeCOEDhinmWhAatsqdquM2XzHZINhK2cqTjHr/XZdVJUbgN3MWAVXKbSyg9jesRW2xP9di+lwrL5ojM3m2H/kG9hwcIA37c71W6wJdW2J2S5nrjYbq/t1AHAhJsKQeyfPvf6IMJgghPJhFZ4x0KlfLFvt22du45Au/A1SOlGc0P672XXwhLtOcM0kTTEMMd0qkVmMNXxMd/tsedUjInr4SQDgOfeXMSiN0FCL5WHah4L1qqYXPJOJlttd+a5M+YpcG5poLYKQ5f+6JJ4r8bbJYP47hq4r7QAs9PjYNhHJd4o8l5taiwuOpa7AS4XKqI/5NjJbTnaWK92nLdLuhQAJayRNMiygXPBeQN+Qbvu0zDc3y+aUzhbkGR73sI7ljvUnndx2q3t+X8CDAD66FtrIL864AAAAABJRU5ErkJggg==" alt="" style="border: 0pt none;" />
</a></div>

        </div>
    </div>
</div>



            <div id="content">
                <div id="content-inner">


                    <div id="alpha">
                        <div id="alpha-inner">


                            <div id="entry-1950" class="entry-asset asset hentry">
                                <div class="asset-header">
<div class="asset-nav entry-nav">

<div class="entry-location">
<ul>
<li>上一篇：<a href="http://www.ruanyifeng.com/blog/2017/08/koa.html" title="Koa 框架教程">Koa&nbsp;框架教程&nbsp;&nbsp;&nbsp;</a></li>
<li>下一篇：<a href="http://www.ruanyifeng.com/blog/2017/08/smart-shoes.html" title="你的鞋都比你聪明">你的鞋都比你聪明&nbsp;&nbsp;&nbsp;</a></li>
</ul>
</div>


    
                                    <div class="entry-categories">
                                        <p>分类<span class="delimiter">：</span></p>
                                        <ul>
                                            <li><a href="http://www.ruanyifeng.com/blog/developer/" rel="tag">开发者手册</a></li>
                                        </ul>
                                    </div>
    


                                            
<div class="entry-location-mobile" style="float: right;">
<ul>
<li><a href="http://www.ruanyifeng.com/blog/2017/08/koa.html" title="Koa 框架教程">⇐&nbsp;</a></li>
<li><a href="http://www.ruanyifeng.com/blog/2017/08/smart-shoes.html" title="你的鞋都比你聪明">&nbsp;⇒</a></li>
</ul>
</div>

</div>
</div>

                          
<article class="hentry">
                                    <h1 id="page-title" class="asset-name entry-title">全文搜索引擎 Elasticsearch 入门教程</h1>
                                            <div id="share_button" class="social-share" style="float:right;padding-right:2em;padding-top:1em;"></div>

<div class="asset-meta">
                                        

                                            <p class="vcard author">作者： <a class="fn url" href="http://www.ruanyifeng.com">阮一峰</a></p>
                                    <p>日期： <a href="http://www.ruanyifeng.com/blog/2017/08/"><abbr class="published" title="2017-08-17T07:36:20+08:00">2017年8月17日</abbr></a></p>


                                    
</div>

                    
<div class="entry-sponsor">
<a title="腾讯课堂 NEXT 学院" href="https://ke.qq.com/next_detail/index.html?id=1&from=800007110" target="_blank">
<p id="sponsor-text" style="box-sizing: border-box;
    text-align: center;
    width: 100%;
    border: 1px solid #e5e4e9;
    background-color: #d0e4a9;
    margin: 1em 0 0;
    padding: 0.6em 0.6em 0.4em 0.6em;
    border-radius: 0.3em 0.3em 0.1em 0.1em;
    color: #076a66;"> 
</p>
<script>
var sponsorTxt = document.getElementById('sponsor-text');
sponsorTxt.innerHTML = '感谢 <span style="text-decoration: underline;">腾讯课堂NEXT学院</span> 赞助本站，<span style="text-decoration: underline;">腾讯前端工程师的官方课程</span> 免费试学。';
</script>
<p style="padding:0em;margin:0 0 1.5em 0;border: 1px solid #e5e4e9;border-radius: 0.1em 0.1em 0.3em 0.3em;background-color: #28344a;text-align: center;" class="entry-sponsor-img">
  <img alt="腾讯课堂 NEXT 学院" id="support-img" src="https://www.wangbase.com/blogimg/asset/201906/bg2019062510.jpg" style="border: none;width: 90%;max-width: 90%;display: inline-block;"/>

</p>
</a>
</div>


                                
                                <div class="asset-content entry-content" id="main-content">

                                    <!-- div class="asset-body" -->
                                        <p><a href="https://baike.baidu.com/item/%E5%85%A8%E6%96%87%E6%90%9C%E7%B4%A2%E5%BC%95%E6%93%8E">全文搜索</a>属于最常见的需求，开源的 <a href="https://www.elastic.co/">Elasticsearch</a> （以下简称 Elastic）是目前全文搜索引擎的首选。</p>

                                    <!-- /div -->


                                    <!-- div id="more" class="asset-more" -->
                                        <p>它可以快速地储存、搜索和分析海量数据。维基百科、Stack Overflow、Github 都采用它。</p>

<p><img src="http://www.ruanyifeng.com/blogimg/asset/2017/bg2017081701.jpg" alt="" title="" /></p>

<p>Elastic 的底层是开源库 <a href="https://lucene.apache.org/">Lucene</a>。但是，你没法直接用 Lucene，必须自己写代码去调用它的接口。Elastic 是 Lucene 的封装，提供了 REST API 的操作接口，开箱即用。</p>

<p>本文从零开始，讲解如何使用 Elastic 搭建自己的全文搜索引擎。每一步都有详细的说明，大家跟着做就能学会。</p>

<h2>一、安装</h2>

<p>Elastic 需要 Java 8 环境。如果你的机器还没安装 Java，可以参考<a href="https://www.digitalocean.com/community/tutorials/how-to-install-java-with-apt-get-on-debian-8">这篇文章</a>，注意要保证环境变量<code>JAVA_HOME</code>正确设置。</p>

<p>安装完 Java，就可以跟着<a href="https://www.elastic.co/guide/en/elasticsearch/reference/current/zip-targz.html">官方文档</a>安装 Elastic。直接下载压缩包比较简单。</p>

<blockquote><pre><code class="language-bash">
$ wget https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-5.5.1.zip
$ unzip elasticsearch-5.5.1.zip
$ cd elasticsearch-5.5.1/ 
</code></pre></blockquote>

<p>接着，进入解压后的目录，运行下面的命令，启动 Elastic。</p>

<blockquote><pre><code class="language-bash">
$ ./bin/elasticsearch
</code></pre></blockquote>

<p>如果这时<a href="https://github.com/spujadas/elk-docker/issues/92">报错</a>"max virtual memory areas vm.max<em>map</em>count [65530] is too low"，要运行下面的命令。</p>

<blockquote><pre><code class="language-bash">
$ sudo sysctl -w vm.max_map_count=262144
</code></pre></blockquote>

<p>如果一切正常，Elastic 就会在默认的9200端口运行。这时，打开另一个命令行窗口，请求该端口，会得到说明信息。</p>

<blockquote><pre><code class="language-bash">
$ curl localhost:9200

{
  "name" : "atntrTf",
  "cluster_name" : "elasticsearch",
  "cluster_uuid" : "tf9250XhQ6ee4h7YI11anA",
  "version" : {
    "number" : "5.5.1",
    "build_hash" : "19c13d0",
    "build_date" : "2017-07-18T20:44:24.823Z",
    "build_snapshot" : false,
    "lucene_version" : "6.6.0"
  },
  "tagline" : "You Know, for Search"
}
</code></pre></blockquote>

<p>上面代码中，请求9200端口，Elastic 返回一个 JSON 对象，包含当前节点、集群、版本等信息。</p>

<p>按下 Ctrl + C，Elastic 就会停止运行。</p>

<p>默认情况下，Elastic 只允许本机访问，如果需要远程访问，可以修改 Elastic 安装目录的<code>config/elasticsearch.yml</code>文件，去掉<code>network.host</code>的注释，将它的值改成<code>0.0.0.0</code>，然后重新启动 Elastic。</p>

<blockquote><pre><code class="language-bash">
network.host: 0.0.0.0
</code></pre></blockquote>

<p>上面代码中，设成<code>0.0.0.0</code>让任何人都可以访问。线上服务不要这样设置，要设成具体的 IP。</p>

<h2>二、基本概念</h2>

<h3>2.1 Node 与 Cluster</h3>

<p>Elastic 本质上是一个分布式数据库，允许多台服务器协同工作，每台服务器可以运行多个 Elastic 实例。</p>

<p>单个 Elastic 实例称为一个节点（node）。一组节点构成一个集群（cluster）。</p>

<h3>2.2 Index</h3>

<p>Elastic 会索引所有字段，经过处理后写入一个反向索引（Inverted Index）。查找数据的时候，直接查找该索引。</p>

<p>所以，Elastic 数据管理的顶层单位就叫做 Index（索引）。它是单个数据库的同义词。每个 Index （即数据库）的名字必须是小写。</p>

<p>下面的命令可以查看当前节点的所有 Index。</p>

<blockquote><pre><code class="language-bash">
$ curl -X GET 'http://localhost:9200/_cat/indices?v'
</code></pre></blockquote>

<h3>2.3 Document</h3>

<p>Index 里面单条的记录称为 Document（文档）。许多条 Document 构成了一个 Index。</p>

<p>Document 使用 JSON 格式表示，下面是一个例子。</p>

<blockquote><pre><code class="language-javascript">
{
  "user": "张三",
  "title": "工程师",
  "desc": "数据库管理"
}
</code></pre></blockquote>

<p>同一个 Index 里面的 Document，不要求有相同的结构（scheme），但是最好保持相同，这样有利于提高搜索效率。</p>

<h3>2.4 Type</h3>

<p>Document 可以分组，比如<code>weather</code>这个 Index 里面，可以按城市分组（北京和上海），也可以按气候分组（晴天和雨天）。这种分组就叫做 Type，它是虚拟的逻辑分组，用来过滤 Document。</p>

<p>不同的 Type 应该有相似的结构（schema），举例来说，<code>id</code>字段不能在这个组是字符串，在另一个组是数值。这是与关系型数据库的表的<a href="https://www.elastic.co/guide/en/elasticsearch/guide/current/mapping.html">一个区别</a>。性质完全不同的数据（比如<code>products</code>和<code>logs</code>）应该存成两个 Index，而不是一个 Index 里面的两个 Type（虽然可以做到）。</p>

<p>下面的命令可以列出每个 Index 所包含的 Type。</p>

<blockquote><pre><code class="language-bash">
$ curl 'localhost:9200/_mapping?pretty=true'
</code></pre></blockquote>

<p>根据<a href="https://www.elastic.co/blog/index-type-parent-child-join-now-future-in-elasticsearch">规划</a>，Elastic 6.x 版只允许每个 Index 包含一个 Type，7.x 版将会彻底移除 Type。</p>

<h2>三、新建和删除 Index</h2>

<p>新建 Index，可以直接向 Elastic 服务器发出 PUT 请求。下面的例子是新建一个名叫<code>weather</code>的 Index。</p>

<blockquote><pre><code class="language-bash">
$ curl -X PUT 'localhost:9200/weather'
</code></pre></blockquote>

<p>服务器返回一个 JSON 对象，里面的<code>acknowledged</code>字段表示操作成功。</p>

<blockquote><pre><code class="language-javascript">
{
  "acknowledged":true,
  "shards_acknowledged":true
}
</code></pre></blockquote>

<p>然后，我们发出 DELETE 请求，删除这个 Index。</p>

<blockquote><pre><code class="language-bash">
$ curl -X DELETE 'localhost:9200/weather'
</code></pre></blockquote>

<h2>四、中文分词设置</h2>

<p>首先，安装中文分词插件。这里使用的是 <a href="https://github.com/medcl/elasticsearch-analysis-ik/">ik</a>，也可以考虑其他插件（比如 <a href="https://www.elastic.co/guide/en/elasticsearch/plugins/current/analysis-smartcn.html">smartcn</a>）。</p>

<blockquote><pre><code class="language-javascript">
$ ./bin/elasticsearch-plugin install https://github.com/medcl/elasticsearch-analysis-ik/releases/download/v5.5.1/elasticsearch-analysis-ik-5.5.1.zip
</code></pre></blockquote>

<p>上面代码安装的是5.5.1版的插件，与 Elastic 5.5.1 配合使用。</p>

<p>接着，重新启动 Elastic，就会自动加载这个新安装的插件。</p>

<p>然后，新建一个 Index，指定需要分词的字段。这一步根据数据结构而异，下面的命令只针对本文。基本上，凡是需要搜索的中文字段，都要单独设置一下。</p>

<blockquote><pre><code class="language-bash">
$ curl -X PUT 'localhost:9200/accounts' -d '
{
  "mappings": {
    "person": {
      "properties": {
        "user": {
          "type": "text",
          "analyzer": "ik_max_word",
          "search_analyzer": "ik_max_word"
        },
        "title": {
          "type": "text",
          "analyzer": "ik_max_word",
          "search_analyzer": "ik_max_word"
        },
        "desc": {
          "type": "text",
          "analyzer": "ik_max_word",
          "search_analyzer": "ik_max_word"
        }
      }
    }
  }
}'
</code></pre></blockquote>

<p>上面代码中，首先新建一个名称为<code>accounts</code>的 Index，里面有一个名称为<code>person</code>的 Type。<code>person</code>有三个字段。</p>

<blockquote>
  <ul>
<li>user</li>
<li>title</li>
<li>desc</li>
</ul>
</blockquote>

<p>这三个字段都是中文，而且类型都是文本（text），所以需要指定中文分词器，不能使用默认的英文分词器。</p>

<p>Elastic 的分词器称为 <a href="https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis.html">analyzer</a>。我们对每个字段指定分词器。</p>

<blockquote><pre><code class="language-javascript">
"user": {
  "type": "text",
  "analyzer": "ik_max_word",
  "search_analyzer": "ik_max_word"
}
</code></pre></blockquote>

<p>上面代码中，<code>analyzer</code>是字段文本的分词器，<code>search_analyzer</code>是搜索词的分词器。<code>ik_max_word</code>分词器是插件<code>ik</code>提供的，可以对文本进行最大数量的分词。</p>

<h2>五、数据操作</h2>

<h3>5.1 新增记录</h3>

<p>向指定的 /Index/Type 发送 PUT 请求，就可以在 Index 里面新增一条记录。比如，向<code>/accounts/person</code>发送请求，就可以新增一条人员记录。</p>

<blockquote><pre><code class="language-bash">
$ curl -X PUT 'localhost:9200/accounts/person/1' -d '
{
  "user": "张三",
  "title": "工程师",
  "desc": "数据库管理"
}' 
</code></pre></blockquote>

<p>服务器返回的 JSON 对象，会给出 Index、Type、Id、Version 等信息。</p>

<blockquote><pre><code class="language-javascript">
{
  "_index":"accounts",
  "_type":"person",
  "_id":"1",
  "_version":1,
  "result":"created",
  "_shards":{"total":2,"successful":1,"failed":0},
  "created":true
}
</code></pre></blockquote>

<p>如果你仔细看，会发现请求路径是<code>/accounts/person/1</code>，最后的<code>1</code>是该条记录的 Id。它不一定是数字，任意字符串（比如<code>abc</code>）都可以。</p>

<p>新增记录的时候，也可以不指定 Id，这时要改成 POST 请求。</p>

<blockquote><pre><code class="language-bash">
$ curl -X POST 'localhost:9200/accounts/person' -d '
{
  "user": "李四",
  "title": "工程师",
  "desc": "系统管理"
}'
</code></pre></blockquote>

<p>上面代码中，向<code>/accounts/person</code>发出一个 POST 请求，添加一个记录。这时，服务器返回的 JSON 对象里面，<code>_id</code>字段就是一个随机字符串。</p>

<blockquote><pre><code class="language-javascript">
{
  "_index":"accounts",
  "_type":"person",
  "_id":"AV3qGfrC6jMbsbXb6k1p",
  "_version":1,
  "result":"created",
  "_shards":{"total":2,"successful":1,"failed":0},
  "created":true
}
</code></pre></blockquote>

<p>注意，如果没有先创建 Index（这个例子是<code>accounts</code>），直接执行上面的命令，Elastic 也不会报错，而是直接生成指定的 Index。所以，打字的时候要小心，不要写错 Index 的名称。</p>

<h3>5.2 查看记录</h3>

<p>向<code>/Index/Type/Id</code>发出 GET 请求，就可以查看这条记录。</p>

<blockquote><pre><code class="language-bash">
$ curl 'localhost:9200/accounts/person/1?pretty=true'
</code></pre></blockquote>

<p>上面代码请求查看<code>/accounts/person/1</code>这条记录，URL 的参数<code>pretty=true</code>表示以易读的格式返回。</p>

<p>返回的数据中，<code>found</code>字段表示查询成功，<code>_source</code>字段返回原始记录。</p>

<blockquote><pre><code class="language-javascript">
{
  "_index" : "accounts",
  "_type" : "person",
  "_id" : "1",
  "_version" : 1,
  "found" : true,
  "_source" : {
    "user" : "张三",
    "title" : "工程师",
    "desc" : "数据库管理"
  }
}
</code></pre></blockquote>

<p>如果 Id 不正确，就查不到数据，<code>found</code>字段就是<code>false</code>。</p>

<blockquote><pre><code class="language-bash">
$ curl 'localhost:9200/weather/beijing/abc?pretty=true'

{
  "_index" : "accounts",
  "_type" : "person",
  "_id" : "abc",
  "found" : false
}
</code></pre></blockquote>

<h3>5.3 删除记录</h3>

<p>删除记录就是发出 DELETE 请求。</p>

<blockquote><pre><code class="language-bash">
$ curl -X DELETE 'localhost:9200/accounts/person/1'
</code></pre></blockquote>

<p>这里先不要删除这条记录，后面还要用到。</p>

<h3>5.4 更新记录</h3>

<p>更新记录就是使用 PUT 请求，重新发送一次数据。</p>

<blockquote><pre><code class="language-bash">
$ curl -X PUT 'localhost:9200/accounts/person/1' -d '
{
    "user" : "张三",
    "title" : "工程师",
    "desc" : "数据库管理，软件开发"
}' 

{
  "_index":"accounts",
  "_type":"person",
  "_id":"1",
  "_version":2,
  "result":"updated",
  "_shards":{"total":2,"successful":1,"failed":0},
  "created":false
}
</code></pre></blockquote>

<p>上面代码中，我们将原始数据从"数据库管理"改成"数据库管理，软件开发"。 返回结果里面，有几个字段发生了变化。</p>

<blockquote><pre><code class="language-bash">
"_version" : 2,
"result" : "updated",
"created" : false
</code></pre></blockquote>

<p>可以看到，记录的 Id 没变，但是版本（version）从<code>1</code>变成<code>2</code>，操作类型（result）从<code>created</code>变成<code>updated</code>，<code>created</code>字段变成<code>false</code>，因为这次不是新建记录。</p>

<h2>六、数据查询</h2>

<h3>6.1 返回所有记录</h3>

<p>使用 GET 方法，直接请求<code>/Index/Type/_search</code>，就会返回所有记录。</p>

<blockquote><pre><code class="language-bash">
$ curl 'localhost:9200/accounts/person/_search'

{
  "took":2,
  "timed_out":false,
  "_shards":{"total":5,"successful":5,"failed":0},
  "hits":{
    "total":2,
    "max_score":1.0,
    "hits":[
      {
        "_index":"accounts",
        "_type":"person",
        "_id":"AV3qGfrC6jMbsbXb6k1p",
        "_score":1.0,
        "_source": {
          "user": "李四",
          "title": "工程师",
          "desc": "系统管理"
        }
      },
      {
        "_index":"accounts",
        "_type":"person",
        "_id":"1",
        "_score":1.0,
        "_source": {
          "user" : "张三",
          "title" : "工程师",
          "desc" : "数据库管理，软件开发"
        }
      }
    ]
  }
}
</code></pre></blockquote>

<p>上面代码中，返回结果的 <code>took</code>字段表示该操作的耗时（单位为毫秒），<code>timed_out</code>字段表示是否超时，<code>hits</code>字段表示命中的记录，里面子字段的含义如下。</p>

<blockquote>
  <ul>
<li><code>total</code>：返回记录数，本例是2条。</li>
<li><code>max_score</code>：最高的匹配程度，本例是<code>1.0</code>。</li>
<li><code>hits</code>：返回的记录组成的数组。</li>
</ul>
</blockquote>

<p>返回的记录中，每条记录都有一个<code>_score</code>字段，表示匹配的程序，默认是按照这个字段降序排列。</p>

<h3>6.2 全文搜索</h3>

<p>Elastic 的查询非常特别，使用自己的<a href="https://www.elastic.co/guide/en/elasticsearch/reference/5.5/query-dsl.html">查询语法</a>，要求 GET 请求带有数据体。</p>

<blockquote><pre><code class="language-bash">
$ curl 'localhost:9200/accounts/person/_search'  -d '
{
  "query" : { "match" : { "desc" : "软件" }}
}'
</code></pre></blockquote>

<p>上面代码使用 <a href="https://www.elastic.co/guide/en/elasticsearch/reference/5.5/query-dsl-match-query.html">Match 查询</a>，指定的匹配条件是<code>desc</code>字段里面包含"软件"这个词。返回结果如下。</p>

<blockquote><pre><code class="language-javascript">
{
  "took":3,
  "timed_out":false,
  "_shards":{"total":5,"successful":5,"failed":0},
  "hits":{
    "total":1,
    "max_score":0.28582606,
    "hits":[
      {
        "_index":"accounts",
        "_type":"person",
        "_id":"1",
        "_score":0.28582606,
        "_source": {
          "user" : "张三",
          "title" : "工程师",
          "desc" : "数据库管理，软件开发"
        }
      }
    ]
  }
}
</code></pre></blockquote>

<p>Elastic 默认一次返回10条结果，可以通过<code>size</code>字段改变这个设置。</p>

<blockquote><pre><code class="language-bash">
$ curl 'localhost:9200/accounts/person/_search'  -d '
{
  "query" : { "match" : { "desc" : "管理" }},
  "size": 1
}'
</code></pre></blockquote>

<p>上面代码指定，每次只返回一条结果。</p>

<p>还可以通过<code>from</code>字段，指定位移。</p>

<blockquote><pre><code class="language-bash">
$ curl 'localhost:9200/accounts/person/_search'  -d '
{
  "query" : { "match" : { "desc" : "管理" }},
  "from": 1,
  "size": 1
}'
</code></pre></blockquote>

<p>上面代码指定，从位置1开始（默认是从位置0开始），只返回一条结果。</p>

<h3>6.3 逻辑运算</h3>

<p>如果有多个搜索关键字， Elastic 认为它们是<code>or</code>关系。</p>

<blockquote><pre><code class="language-bash">
$ curl 'localhost:9200/accounts/person/_search'  -d '
{
  "query" : { "match" : { "desc" : "软件 系统" }}
}'
</code></pre></blockquote>

<p>上面代码搜索的是<code>软件 or 系统</code>。</p>

<p>如果要执行多个关键词的<code>and</code>搜索，必须使用<a href="https://www.elastic.co/guide/en/elasticsearch/reference/5.5/query-dsl-bool-query.html">布尔查询</a>。</p>

<blockquote><pre><code class="language-bash">
$ curl 'localhost:9200/accounts/person/_search'  -d '
{
  "query": {
    "bool": {
      "must": [
        { "match": { "desc": "软件" } },
        { "match": { "desc": "系统" } }
      ]
    }
  }
}'
</code></pre></blockquote>

<h2>七、参考链接</h2>

<ul>
<li><a href="https://www.elastic.co/guide/en/elasticsearch/reference/current/getting-started.html">ElasticSearch 官方手册</a></li>
<li><a href="https://www.elastic.co/blog/a-practical-introduction-to-elasticsearch">A Practical Introduction to Elasticsearch</a></li>
</ul>

<p>（完）</p>

                                    <!-- /div -->

                                </div>
    <script type="text/javascript" src="/newwindow.js"></script>
                                <div class="asset-footer">

<h3>文档信息</h3>
<ul>
<li>版权声明：自由转载-非商用-非衍生-保持署名（<a href="http://creativecommons.org/licenses/by-nc-nd/3.0/deed.zh">创意共享3.0许可证</a>）</li>
<li>发表日期： <abbr class="published" title="2017-08-17T07:36:20+08:00">2017年8月17日</abbr></li>

</ul>
                                </div>
</article>
                            </div>

  <div style="display: inline-block ! important;width: 100%;">
  <p style="text-align:center;font-size: 16px;">
    <a title="开课吧" target="_blank" href="https://s.growingio.com/3EQP0X">阿里前端 P7 的技术栈</a>
    <br>
    <a title="开课吧" href="https://s.growingio.com/3EQP0X" target="_blank"><img alt="开课吧" src="https://www.wangbase.com/blogimg/asset/201906/bg2019063013.jpg" style="border: 1px solid #666;display: block !important;width:100%;max-width:100%;" /></a>
  </p>
  
  <p style="text-align:center;font-size: 16px;">
    <a title="饥人谷" target="_blank" href="http://qr.jirengu.com/api/taskUrl?tid=58">饥人谷：专业前端培训机构</a>
    <br>
    <a title="饥人谷" href="http://qr.jirengu.com/api/taskUrl?tid=50" target="_blank"><img alt="饥人谷" src="https://www.wangbase.com/blogimg/asset/201904/bg2019042105.png" style="border: 1px solid #666;display: block !important;width:100%;max-width:100%;" /></a>
  </p> 
  </div>

<div id="related_entries">
<h2>相关文章</h2>
<ul>

<li><strong>2019.06.26: <a href="http://www.ruanyifeng.com/blog/2019/06/android-remote-debugging.html">远程调试 Android 设备网页</a></strong>

                           <div class="entry-body">
                              网页在手机浏览器打开时，怎么调试？

                           </div>

</li>

 
<li><strong>2019.06.25: <a href="http://www.ruanyifeng.com/blog/2019/06/open-database-relicensing.html">为什么开源数据库改变许可证？</a></strong>

                           <div class="entry-body">
                              CockroachDB 是一个开源的分布式数据库，最近改变了代码授权，放弃了 Apache 许可证。

                           </div>

</li>

 
<li><strong>2019.06.10: <a href="http://www.ruanyifeng.com/blog/2019/06/responsive-images.html">响应式图像教程</a></strong>

                           <div class="entry-body">
                              网页在不同尺寸的设备上，都有良好的显示效果，叫做"响应式设计"（responsive web design）。

                           </div>

</li>

 
<li><strong>2019.06.04: <a href="http://www.ruanyifeng.com/blog/2019/06/http-referer.html">HTTP Referer 教程</a></strong>

                           <div class="entry-body">
                              HTTP 请求的头信息里面，Referer 是一个常见字段，提供访问来源的信息。

                           </div>

</li>

 
</ul>
</div>


<div id="cre" style="display: block !important;">
<h2>广告<a href="/support.html">（购买广告位）</a></h2>
<div id="cre-inner">
<div id="cre-left">

<p style="font-size:16px;text-align:center;"><a title="开课吧" href="https://s.growingio.com/DyeVX0" target="_blank">高级前端免费视频</a></p>
<a title="开课吧" href="https://s.growingio.com/DyeVX0" target="_blank"><image alt=="开课吧" src="https://www.wangbase.com/blogimg/asset/201906/bg2019063012.jpg" style="border:none;" /></a>

</div>
<div id="cre-right">

<p style="font-size:16px;text-align:center;"><a href="http://www.ruanyifeng.com/blog/2019/01/survivor-preface.html" target="_blank">未来世界的幸存者</a></p>
<a title="未来世界的幸存者" href="http://www.ruanyifeng.com/blog/2019/01/survivor-preface.html" target="_blank"><image alt="未来世界的幸存者" src="https://www.wangbase.com/blogimg/asset/201903/bg2019032401.jpg" style="border:none;" /></a>


</div>
</div>
</div>

                    

                    <div id="comments" class="comments">


    
    
        
    <h2 class="comments-header">留言（78条）</h2>

    <div id="comments-content" class="comments-content" style="clear: left;">
        
        <div id="comment-379579" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">Arther</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-379579">
            <p>Java7也可以用。</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="August 17, 2017  8:08 AM">2017年8月17日 08:08</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-379579">#</a>
 | <a href="#comment-text" title="引用Arther的这条留言" onclick="return CommentQuote('comment-quote-379579','Arther');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-379581" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">Viky</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-379581">
            <p>求协同工作的 FileBeat、Kibana 的教程，这样日志的收集过滤才算完整吧</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="August 17, 2017  8:45 AM">2017年8月17日 08:45</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-379581">#</a>
 | <a href="#comment-text" title="引用Viky的这条留言" onclick="return CommentQuote('comment-quote-379581','Viky');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-379584" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="http://blog.antior.cn" href="http://blog.antior.cn" target="_blank" rel="nofollow">antior</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-379584">
            <p>这个软件，有什么用呢？</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="August 17, 2017  9:26 AM">2017年8月17日 09:26</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-379584">#</a>
 | <a href="#comment-text" title="引用antior的这条留言" onclick="return CommentQuote('comment-quote-379584','antior');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-379585" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">zhujun24</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-379585">
            <p>ElasticSearch 可以做拼音搜索么？比如用 bianxingjingang 或者 bxjg 搜索到“变形金刚”。</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="August 17, 2017  9:46 AM">2017年8月17日 09:46</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-379585">#</a>
 | <a href="#comment-text" title="引用zhujun24的这条留言" onclick="return CommentQuote('comment-quote-379585','zhujun24');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-379586" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">jose</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-379586">
            <p>调用过es的接口 蛮复杂的 改天实践一下</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="August 17, 2017  9:47 AM">2017年8月17日 09:47</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-379586">#</a>
 | <a href="#comment-text" title="引用jose的这条留言" onclick="return CommentQuote('comment-quote-379586','jose');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-379589" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="http://www.honpery.com" href="http://www.honpery.com" target="_blank" rel="nofollow">honpery</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-379589">
            <p>觉得阮老师应该带头普及docker，docker注定是未来 - -</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="August 17, 2017 10:22 AM">2017年8月17日 10:22</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-379589">#</a>
 | <a href="#comment-text" title="引用honpery的这条留言" onclick="return CommentQuote('comment-quote-379589','honpery');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-379590" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">卫书有道</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-379590">
            <p>太及时了~~~~~</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="August 17, 2017 11:22 AM">2017年8月17日 11:22</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-379590">#</a>
 | <a href="#comment-text" title="引用卫书有道的这条留言" onclick="return CommentQuote('comment-quote-379590','卫书有道');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-379591" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="http://liyangweb.com" href="http://liyangweb.com" target="_blank" rel="nofollow">kaopur</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-379591">
            <p>不错,正需要...</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="August 17, 2017 11:28 AM">2017年8月17日 11:28</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-379591">#</a>
 | <a href="#comment-text" title="引用kaopur的这条留言" onclick="return CommentQuote('comment-quote-379591','kaopur');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-379602" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">古德</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-379602">
            <p>跟mongodb在语法上有些像。。。。</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="August 17, 2017 12:56 PM">2017年8月17日 12:56</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-379602">#</a>
 | <a href="#comment-text" title="引用古德的这条留言" onclick="return CommentQuote('comment-quote-379602','古德');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-379603" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">xxxx</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-379603">
            <blockquote>
<pre>引用zhujun24的发言：</pre>

<p>ElasticSearch 可以做拼音搜索么？比如用 bianxingjingang 或者 bxjg 搜索到“变形金刚”。</p>

</blockquote>

<p>肯定不可以，但是你可以增加一个字段表示啊。 <br />
比如说,中国，  你再用一个 zhongguo 字段 表示。<br />
可以用自定义分词器，进行分词  <br />
搜索的时候，就可以 搜索出来 zg了。<br />
</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="August 17, 2017 12:59 PM">2017年8月17日 12:59</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-379603">#</a>
 | <a href="#comment-text" title="引用xxxx的这条留言" onclick="return CommentQuote('comment-quote-379603','xxxx');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-379604" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">Macdull</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-379604">
            <p>ES可以做数据仓库吗，或者有类似的案例吗？</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="August 17, 2017  1:20 PM">2017年8月17日 13:20</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-379604">#</a>
 | <a href="#comment-text" title="引用Macdull的这条留言" onclick="return CommentQuote('comment-quote-379604','Macdull');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-379608" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="https://github.com/cczw2010/" href="https://github.com/cczw2010/" target="_blank" rel="nofollow">awen</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-379608">
            <p>正好这两天再看相关内容，要是node平台相关介绍就更好了</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="August 17, 2017  4:01 PM">2017年8月17日 16:01</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-379608">#</a>
 | <a href="#comment-text" title="引用awen的这条留言" onclick="return CommentQuote('comment-quote-379608','awen');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-379609" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="http://www.codedq.net" href="http://www.codedq.net" target="_blank" rel="nofollow">CODE大全</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-379609">
            <p>我的博客底层的搜索用的就是Lucene。Lucene非常强大，目前公司ELK平台用到了ElasticSearch，日数据量达到了10亿级以上。</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="August 17, 2017  4:02 PM">2017年8月17日 16:02</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-379609">#</a>
 | <a href="#comment-text" title="引用CODE大全的这条留言" onclick="return CommentQuote('comment-quote-379609','CODE大全');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-379610" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="http://www.xttblog.com" href="http://www.xttblog.com" target="_blank" rel="nofollow">业余草</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-379610">
            <blockquote>
<pre>引用xxxx的发言：</pre>

<p>肯定不可以，但是你可以增加一个字段表示啊。 <br />
比如说,中国，你再用一个 zhongguo 字段 表示。<br />
可以用自定义分词器，进行分词<br />
搜索的时候，就可以 搜索出来 zg了。<br />
</p></blockquote>

<p>搜狗有专门的插件，可以将汉字拼音化，拼音汉字化。但是对于一些拼音相同的词汇就不好处理了。<br />
例如：renming，中文有，任命，认命，人名等。</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="August 17, 2017  4:07 PM">2017年8月17日 16:07</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-379610">#</a>
 | <a href="#comment-text" title="引用业余草的这条留言" onclick="return CommentQuote('comment-quote-379610','业余草');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-379635" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="http://www.sojson.com" href="http://www.sojson.com" target="_blank" rel="nofollow">sojson</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-379635">
            <p>Elasticsearch 说说我的感受吧，我13年开始用，到现在4个年头了，我基本上是去一家公司，我就会改变原有的搜索或者NOSQL数据存储，改成Elasticsearch 。 Elasticsearch 有着以下特点：</p>

<p>第一，更新迭代快，社区活跃。文档现在着实丰富（这是选型的第一要素）。<br />
第二，从性能上来说，确实目前来说是首选。<br />
第三，目前第三方组件越来越多，只有你想不到的。<br />
... ... </p>

<p>小白（Java）可以看看这个博客，http://www.sojson.com/blog/81.html<br />
Ctrl + F 实现站内搜索，包括近义词，比如搜索ES，内容会出现Elasticsearch和ES的内容。脚本=JS=Javascript<br />
</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="August 18, 2017  1:13 PM">2017年8月18日 13:13</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-379635">#</a>
 | <a href="#comment-text" title="引用sojson的这条留言" onclick="return CommentQuote('comment-quote-379635','sojson');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-379670" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">dreamer</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-379670">
            <p>刚准备学一下ES，抱着碰一下运气的心态来看看这里有木有，哇，简直不要太开心。</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="August 20, 2017 12:25 PM">2017年8月20日 12:25</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-379670">#</a>
 | <a href="#comment-text" title="引用dreamer的这条留言" onclick="return CommentQuote('comment-quote-379670','dreamer');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-379671" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="https://www.zhihu.com/people/liu-tong-zhou-517/activities" href="https://www.zhihu.com/people/liu-tong-zhou-517/activities" target="_blank" rel="nofollow">刘同周</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-379671">
            <p>在现在利用行业门槛、低级知识互相开LIVE骗钱的时代，阮神还坚持分享知识，自由传播，真是可贵。</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="August 20, 2017  1:29 PM">2017年8月20日 13:29</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-379671">#</a>
 | <a href="#comment-text" title="引用刘同周的这条留言" onclick="return CommentQuote('comment-quote-379671','刘同周');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-379676" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">zhujun24</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-379676">
            <blockquote>
<pre>引用业余草的发言：</pre>
搜狗有专门的插件，可以将汉字拼音化，拼音汉字化。但是对于一些拼音相同的词汇就不好处理了。
例如：renming，中文有，任命，认命，人名等。
</blockquote>

<p>自己写过一个中文转拼音的工具，结合ES加正则应该可以实现拼音搜索+高亮显示关键词。<br />
<a href="https://github.com/zhujun24/chinese-to-pinyin" rel="nofollow">https://github.com/zhujun24/chinese-to-pinyin</a></p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="August 21, 2017  9:30 AM">2017年8月21日 09:30</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-379676">#</a>
 | <a href="#comment-text" title="引用zhujun24的这条留言" onclick="return CommentQuote('comment-quote-379676','zhujun24');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-379748" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">苹果虫子</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-379748">
            <blockquote>
<pre>引用xxxx的发言：</pre>

<p></p>

<p>肯定不可以，但是你可以增加一个字段表示啊。 <br />
比如说,中国，你再用一个 zhongguo 字段 表示。<br />
可以用自定义分词器，进行分词<br />
搜索的时候，就可以 搜索出来 zg了。</p>

<p><br />
</p></blockquote>

<p>有一个同义词管理， 把IK的词库中的词都加上拼音的同义词， </p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="August 22, 2017  7:18 PM">2017年8月22日 19:18</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-379748">#</a>
 | <a href="#comment-text" title="引用苹果虫子的这条留言" onclick="return CommentQuote('comment-quote-379748','苹果虫子');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-379872" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="http://www.yangtianb.com" href="http://www.yangtianb.com" target="_blank" rel="nofollow">bayker</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-379872">
            <blockquote>
<pre>引用xxxx的发言：</pre>

<p></p>

<p>肯定不可以，但是你可以增加一个字段表示啊。 <br />
比如说,中国，你再用一个 zhongguo 字段 表示。<br />
可以用自定义分词器，进行分词<br />
搜索的时候，就可以 搜索出来 zg了。</p>

<p><br />
</p></blockquote>

<p>安装ik 的拼音分词就可以了。</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="August 28, 2017 12:47 PM">2017年8月28日 12:47</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-379872">#</a>
 | <a href="#comment-text" title="引用bayker的这条留言" onclick="return CommentQuote('comment-quote-379872','bayker');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-379921" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">ejzhang</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-379921">
            <p>ElasticSearch 权威指南（中文版）<br />
<a href="https://www.elastic.co/guide/cn/elasticsearch/guide/current/index.html" rel="nofollow">https://www.elastic.co/guide/cn/elasticsearch/guide/current/index.html</a></p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="August 30, 2017  8:02 AM">2017年8月30日 08:02</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-379921">#</a>
 | <a href="#comment-text" title="引用ejzhang的这条留言" onclick="return CommentQuote('comment-quote-379921','ejzhang');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-380075" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="https://glacjay.info" href="https://glacjay.info" target="_blank" rel="nofollow">GlacJAY</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-380075">
            <p>查了下 curl 的手册，加上 -d 参数后就是 POST 请求了，不是 GET。</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="September  6, 2017  9:08 AM">2017年9月 6日 09:08</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-380075">#</a>
 | <a href="#comment-text" title="引用GlacJAY的这条留言" onclick="return CommentQuote('comment-quote-380075','GlacJAY');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-380081" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">柳汉涛</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-380081">
            <p>更新数据有个  "_update" 命令 </p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="September  6, 2017 12:46 PM">2017年9月 6日 12:46</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-380081">#</a>
 | <a href="#comment-text" title="引用柳汉涛的这条留言" onclick="return CommentQuote('comment-quote-380081','柳汉涛');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-380991" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="http://027yunwei.com" href="http://027yunwei.com" target="_blank" rel="nofollow">泽云027</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-380991">
            <blockquote>
<pre>引用GlacJAY的发言：</pre>

<p>查了下 curl 的手册，加上 -d 参数后就是 POST 请求了，不是 GET。</p>

</blockquote>

<p>ES文档中解释过这个问题。这里GET是ES里面的说法，表示是查询而非创建的“概念”；当ES的查询请求是通过json文档表达时，需要使用HTTP的POST来“实现”。</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="September 29, 2017  7:07 AM">2017年9月29日 07:07</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-380991">#</a>
 | <a href="#comment-text" title="引用泽云027的这条留言" onclick="return CommentQuote('comment-quote-380991','泽云027');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-381089" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">fafu</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-381089">
            <blockquote>
<pre>引用zhujun24的发言：</pre>

<p>ElasticSearch 可以做拼音搜索么？比如用 bianxingjingang 或者 bxjg 搜索到“变形金刚”。</p>

</blockquote>

<p>可以</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="October  9, 2017 11:24 AM">2017年10月 9日 11:24</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-381089">#</a>
 | <a href="#comment-text" title="引用fafu的这条留言" onclick="return CommentQuote('comment-quote-381089','fafu');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-381405" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">qdice007</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-381405">
            <p>"6.2 全文搜索"的上面一行,"表示匹配的程序" 是不是应该是 "表示匹配的程度"</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="October 18, 2017  1:34 PM">2017年10月18日 13:34</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-381405">#</a>
 | <a href="#comment-text" title="引用qdice007的这条留言" onclick="return CommentQuote('comment-quote-381405','qdice007');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-381595" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">Jayson</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-381595">
            <blockquote>
<pre>引用xxxx的发言：</pre>

<p></p>

<p>肯定不可以，但是你可以增加一个字段表示啊。 <br />
比如说,中国，你再用一个 zhongguo 字段 表示。<br />
可以用自定义分词器，进行分词<br />
搜索的时候，就可以 搜索出来 zg了。</p>

<p><br />
</p></blockquote>

<p>本来就有拼音分词啊，为什么不能用拼音搜索，只要在建立索引分词的时候用拼音加中文分词包，检索的时候就可以拼音中文检索啊</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="October 23, 2017  1:24 PM">2017年10月23日 13:24</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-381595">#</a>
 | <a href="#comment-text" title="引用Jayson的这条留言" onclick="return CommentQuote('comment-quote-381595','Jayson');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-381854" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">jayson</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-381854">
            <p>windows下用cmd运行curl的话  命令都不要带单引号就可以正常运行了</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="October 31, 2017  7:41 PM">2017年10月31日 19:41</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-381854">#</a>
 | <a href="#comment-text" title="引用jayson的这条留言" onclick="return CommentQuote('comment-quote-381854','jayson');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-382091" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">lance</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-382091">
            <blockquote>
<pre>引用xxxx的发言：</pre>

<p></p>

<p>肯定不可以，但是你可以增加一个字段表示啊。 <br />
比如说,中国，你再用一个 zhongguo 字段 表示。<br />
可以用自定义分词器，进行分词<br />
搜索的时候，就可以 搜索出来 zg了。</p>

<p>这世界有个东西叫拼音分词的插件</p>

<p><br />
</p></blockquote>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="November  7, 2017  3:49 PM">2017年11月 7日 15:49</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-382091">#</a>
 | <a href="#comment-text" title="引用lance的这条留言" onclick="return CommentQuote('comment-quote-382091','lance');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-382282" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">rick.liu</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-382282">
            <p>阮哥,很敬佩你的人生。不过关于ES 我几个问题要问一下：<br />
index->type->document  首先我在没在目前的版本看到关于移除type的说明， 其次，官方自己人的index下也是创建了很多个type来管理不同的mapping。所以关于同一个index下多个type对应不同的mapping会影响会检索速度，这个是在哪里体现的？。其实我个也赞同 一个index一个type，但实际官方也没采用这种方式（除filebeat之外，filebeat把不同的日志收到同一个index下，type为doc）.</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="November 13, 2017  7:27 PM">2017年11月13日 19:27</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-382282">#</a>
 | <a href="#comment-text" title="引用rick.liu的这条留言" onclick="return CommentQuote('comment-quote-382282','rick.liu');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-382356" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">刘奇</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-382356">
            <p>阮哥你好，有个问题想请教您，项目中需要用到es来实现数据统计，现在有这样一个需求不知道该如何实现，查了一下资料，也没有看到好的解决办法</p>

<p>现在有这样一组数据</p>

<p>[</p>

<p>{userId:1, name: 'liu', createTime:'2017-9-16', investMount: 1000, sex: 1},</p>

<p>{userId:1, name: 'liu', createTime:'2017-9-16', investMount: 2000, sex: 1},</p>

<p>{userId:1, name: 'liu', createTime:'2017-9-16', investMount: 3000, sex: 1},</p>

<p>{userId:2, name: 'wang', createTime:'2017-10-1', investMount: 1500, sex: 0},</p>

<p>{userId:3, name: 'zhang', createTime:'2017-10-14', investMount: 1800, sex: 1},</p>

<p>{userId:4, name: 'zhao', createTime:'2017-10-17', investMount: 4000, sex: 1}</p>

<p>]</p>

<p>比如：现在想查询注册时间(createTime)是2017-9-15到2017-10-15日之间，投资总额(同一个userId用户investMount的总和)在2000-5000之间的男性（sex==1）的用户，最后取到符合条件的userId的集合，这种需求该如何写DSL语句呢？</p>

<p>我现在对es的学习能力只能想到这一步，先根据固定的条件进行查询筛选，然后再根据用户id进行分组，查询每个用户的投资总金额，但是如何使最后结果能返回符合所有条件的用户userId的集合我还是想不出来，求指教~~</p>

<p>{</p>

<p>"query": {</p>

<p>"bool": {</p>

<p>"must": {</p>

<p>{"term": {"sex": 1}}</p>

<p>},</p>

<p>"filter": {</p>

<p>"range": {</p>

<p>"createTime": {</p>

<p>"from": "2017-9-15",</p>

<p>"to": "2017-10-15"</p>

<p>}</p>

<p>}</p>

<p>}</p>

<p>} </p>

<p>},</p>

<p>"aggs": {</p>

<p>"group_by_userId": {</p>

<p>"terms": {"field": "userId"},</p>

<p>"aggs": {</p>

<p>"sum_investMount": {</p>

<p>"sum": { "field": "investMount"}</p>

<p>}</p>

<p>}</p>

<p>}</p>

<p>}</p>

<p>}</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="November 15, 2017  9:36 PM">2017年11月15日 21:36</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-382356">#</a>
 | <a href="#comment-text" title="引用刘奇的这条留言" onclick="return CommentQuote('comment-quote-382356','刘奇');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-382520" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">Mr.J</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-382520">
            <p>@刘奇：</p>

<p>三个条件，一个是时间条件，一个是性别，一个是投资额度区间对吧？你可以使用布尔查询的and将时间和性别过符合条件的先过滤出来，然后用聚合查询根据userid分组就可以了。或者使用嵌套桶的概念，也就是你说的分组，嵌套分组，每个条件分一个组。但是感觉先用过滤的话从逻辑和性能都会好点。哦，对了，如果你想返回的数据只有userID，可以使用top_hits,_source字段来控制返回的内容。我也是刚学，说的可能不对。</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="November 20, 2017  2:46 PM">2017年11月20日 14:46</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-382520">#</a>
 | <a href="#comment-text" title="引用Mr.J的这条留言" onclick="return CommentQuote('comment-quote-382520','Mr.J');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-383073" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="http://51lucy.com" href="http://51lucy.com" target="_blank" rel="nofollow">Salamander</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-383073">
            <p>现在比较好的ES从MySQl同步数据的工具是什么？https://github.com/jprante/elasticsearch-jdbc 这个库都好久不更新了</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="December  7, 2017  4:28 PM">2017年12月 7日 16:28</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-383073">#</a>
 | <a href="#comment-text" title="引用Salamander的这条留言" onclick="return CommentQuote('comment-quote-383073','Salamander');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-383307" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="http://无" href="http://无" target="_blank" rel="nofollow">陈卓</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-383307">
            <p>多亏阮神！原本以为es是冷门项目，没想到大神也在用。涉猎广泛啊！学习了</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="December 18, 2017  3:37 PM">2017年12月18日 15:37</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-383307">#</a>
 | <a href="#comment-text" title="引用陈卓的这条留言" onclick="return CommentQuote('comment-quote-383307','陈卓');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-384032" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">xiakejie</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-384032">
            <p>2018第一签，还好评论不多，认真看完.</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="January  3, 2018  9:54 PM">2018年1月 3日 21:54</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-384032">#</a>
 | <a href="#comment-text" title="引用xiakejie的这条留言" onclick="return CommentQuote('comment-quote-384032','xiakejie');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-384950" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">高振波</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-384950">
            <p>新建Index时按照官网的例子就会返回<br />
{"error":"Content-Type header [application/x-www-form-urlencoded] is not supported","status":406}% </p>

<p>查了一下需要添加头文件请求 ： -H "Content-Type: application/json" </p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="January 24, 2018  5:13 PM">2018年1月24日 17:13</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-384950">#</a>
 | <a href="#comment-text" title="引用高振波的这条留言" onclick="return CommentQuote('comment-quote-384950','高振波');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-384987" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">DD</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-384987">
            <p>大神们知道怎么新开一个节点嘛。按照教程本人启动了es照理说是一个节点，现在想再启动一个，是直接开个新的cmd然后bin里把es再跑一遍吗？</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="January 25, 2018  2:09 PM">2018年1月25日 14:09</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-384987">#</a>
 | <a href="#comment-text" title="引用DD的这条留言" onclick="return CommentQuote('comment-quote-384987','DD');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-385250" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">lancelot</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-385250">
            <p>windows 用curl时，除了省略所有的单引号外需要注意外，-d 后的{"name":"zhu"}要改为{\"name\":\"zhu\"}</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="January 30, 2018  4:21 PM">2018年1月30日 16:21</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-385250">#</a>
 | <a href="#comment-text" title="引用lancelot的这条留言" onclick="return CommentQuote('comment-quote-385250','lancelot');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-386419" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">李朋印</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-386419">
            <p>阮哥你好，我想请教下，类似mysql的like查询时，会默认将要搜索的字符串进行分词分析，，如果我不想分词分析，直接搜索，，，是不是只能修改es里面字段为未分析的？  是否会有相应的查询方式可以实现？？谢谢了</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="March  7, 2018 11:53 AM">2018年3月 7日 11:53</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-386419">#</a>
 | <a href="#comment-text" title="引用李朋印的这条留言" onclick="return CommentQuote('comment-quote-386419','李朋印');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-386827" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">dingjie</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-386827">
            <p>我在mac 里面执行了你上面的例子报错，麻烦你有时间看看<br />
 curl -X PUT 'localhost:9200/accounts' -d '<br />
{<br />
  "mappings": {<br />
    "person": {<br />
      "properties": {<br />
        "user": {<br />
          "type": "text",<br />
          "analyzer": "ik_max_word",<br />
          "search_analyzer": "ik_max_word"<br />
        },<br />
        "title": {<br />
          "type": "text",<br />
          "analyzer": "ik_max_word",<br />
          "search_analyzer": "ik_max_word"<br />
        },<br />
        "desc": {<br />
          "type": "text",<br />
          "analyzer": "ik_max_word",<br />
          "search_analyzer": "ik_max_word"<br />
        }<br />
      }<br />
    }<br />
  }<br />
}'<br />
{"error":"Content-Type header [application/x-www-form-urlencoded] is not supported","status":406}%</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="March 19, 2018  6:51 PM">2018年3月19日 18:51</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-386827">#</a>
 | <a href="#comment-text" title="引用dingjie的这条留言" onclick="return CommentQuote('comment-quote-386827','dingjie');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-386830" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">dignjie</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-386830">
            <p> 我解决了上面的问题但是又报了curl -H "Content-Type: application/json"  -X PUT  'localhost:9200/accounts' -d '{"mappings":{"person":{"properties":{"user":{"type":"text","analyzer":"ik_max_word","search_analyzer":"ik_max_word"},"title":{"type":"text","analyzer":"ik_max_word","search_analyzer":"ik_max_word"},"desc":{"type":"text","analyzer":"ik_max_word","search_analyzer":"ik_max_word"}}}}}'<br />
{"error":{"root_cause":[{"type":"mapper_parsing_exception","reason":"analyzer [ik_max_word] not found for field [title]"}],"type":"mapper_parsing_exception","reason":"Failed to parse mapping [person]: analyzer [ik_max_word] not found for field [title]","caused_by":{"type":"mapper_parsing_exception","reason":"analyzer [ik_max_word] not found for field [title]"}},"status":400}%这个</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="March 19, 2018  7:15 PM">2018年3月19日 19:15</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-386830">#</a>
 | <a href="#comment-text" title="引用dignjie的这条留言" onclick="return CommentQuote('comment-quote-386830','dignjie');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-386832" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">dignjie</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-386832">
            <p>不好意思， 原来是我安装了ki没有重启</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="March 19, 2018  7:29 PM">2018年3月19日 19:29</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-386832">#</a>
 | <a href="#comment-text" title="引用dignjie的这条留言" onclick="return CommentQuote('comment-quote-386832','dignjie');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-386957" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">test</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-386957">
            <p>“如果要执行多个关键词的and搜索，必须使用布尔查询。”<br />
可以用以下方式：<br />
"match" : {<br />
            "message" : {<br />
                "query" : "this is a test",<br />
                "operator" : "and"<br />
            }<br />
        }</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="March 22, 2018  2:36 PM">2018年3月22日 14:36</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-386957">#</a>
 | <a href="#comment-text" title="引用test的这条留言" onclick="return CommentQuote('comment-quote-386957','test');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-387004" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">陈学礼</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-387004">
            <p>路途坎坷，看楼主这篇文章我要先去安装VM虚拟机，然后在虚拟机中安装linux系统，然后还要安装jdk，然后才能安装楼主的elasticsearch，这中间遇到很多问题，还好都被解决了，总算把楼主的教程看完，谢谢楼主，如果楼主还有精力，希望顺便讲解一下Spring boot + elasticsearch + mysql的用法，综合在实际操作中的步骤，谢谢楼主，楼主辛苦了</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="March 23, 2018  5:10 PM">2018年3月23日 17:10</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-387004">#</a>
 | <a href="#comment-text" title="引用陈学礼的这条留言" onclick="return CommentQuote('comment-quote-387004','陈学礼');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-387384" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">虎口脱险</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-387384">
            <blockquote>
<pre>引用陈学礼的发言：</pre>

<p>路途坎坷，看楼主这篇文章我要先去安装VM虚拟机，然后在虚拟机中安装linux系统，然后还要安装jdk，然后才能安装楼主的elasticsearch，这中间遇到很多问题，还好都被解决了，总算把楼主的教程看完，谢谢楼主，如果楼主还有精力，希望顺便讲解一下Spring boot + elasticsearch + mysql的用法，综合在实际操作中的步骤，谢谢楼主，楼主辛苦了</p>

</blockquote>

<p>为什么这些东西需要楼主去讲，而不是试着自己去实践</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="April  2, 2018 10:17 AM">2018年4月 2日 10:17</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-387384">#</a>
 | <a href="#comment-text" title="引用虎口脱险的这条留言" onclick="return CommentQuote('comment-quote-387384','虎口脱险');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-387762" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="http://www.qiehe.net" href="http://www.qiehe.net" target="_blank" rel="nofollow">茄盒</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-387762">
            <p>学习中， `docker` 里还好自带有 `image`, 不过这下载速度也是要了命了，加了国内镜像也没啥用</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="April  9, 2018  5:17 PM">2018年4月 9日 17:17</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-387762">#</a>
 | <a href="#comment-text" title="引用茄盒的这条留言" onclick="return CommentQuote('comment-quote-387762','茄盒');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-388291" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">小陈</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-388291">
            <p>通俗易懂，峰哥出手果然不一样</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="April 26, 2018  3:15 PM">2018年4月26日 15:15</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-388291">#</a>
 | <a href="#comment-text" title="引用小陈的这条留言" onclick="return CommentQuote('comment-quote-388291','小陈');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-388794" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">bog.king</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-388794">
            <p>那是不是说，适合用eleatic做站内搜索引擎？</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="May 11, 2018  3:58 PM">2018年5月11日 15:58</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-388794">#</a>
 | <a href="#comment-text" title="引用bog.king的这条留言" onclick="return CommentQuote('comment-quote-388794','bog.king');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-388796" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">兰</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-388796">
            <blockquote>
<pre>引用陈学礼的发言：</pre>

<p>路途坎坷，看楼主这篇文章我要先去安装VM虚拟机，然后在虚拟机中安装linux系统，然后还要安装jdk，然后才能安装楼主的elasticsearch，这中间遇到很多问题，还好都被解决了，总算把楼主的教程看完，谢谢楼主，如果楼主还有精力，希望顺便讲解一下Spring boot + elasticsearch + mysql的用法，综合在实际操作中的步骤，谢谢楼主，楼主辛苦了</p>

</blockquote>

<p>只是学一下你完全可以安windows版啊</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="May 11, 2018  4:17 PM">2018年5月11日 16:17</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-388796">#</a>
 | <a href="#comment-text" title="引用兰的这条留言" onclick="return CommentQuote('comment-quote-388796','兰');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-390053" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">Jeffy</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-390053">
            <p><a href="http://localhost:9200/accounts/person/_search/" rel="nofollow">http://localhost:9200/accounts/person/_search/</a><br />
get:"max_score": 1,<br />
post:"max_score": 0.25316024<br />
为啥会这样呢?获取应该是get呀</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="June 22, 2018  4:04 PM">2018年6月22日 16:04</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-390053">#</a>
 | <a href="#comment-text" title="引用Jeffy的这条留言" onclick="return CommentQuote('comment-quote-390053','Jeffy');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-390496" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="http://www.cnblogs.com/cmbyn/" href="http://www.cnblogs.com/cmbyn/" target="_blank" rel="nofollow">yanbin</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-390496">
            <p>你好，我是一个技术小白，老板目前要求我把公司wiki的搜索引擎改成es。请问这个任务具有可行操作性吗？如果可以的话，请问从零开始学习操作需要大概多长时间来完成呀？</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="July  3, 2018 11:46 AM">2018年7月 3日 11:46</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-390496">#</a>
 | <a href="#comment-text" title="引用yanbin的这条留言" onclick="return CommentQuote('comment-quote-390496','yanbin');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-390819" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="http://www.hchstudio.cn/" href="http://www.hchstudio.cn/" target="_blank" rel="nofollow">whforever</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-390819">
            <p>讲的很清晰，解决了搞了半天的问题</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="July 11, 2018  7:38 PM">2018年7月11日 19:38</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-390819">#</a>
 | <a href="#comment-text" title="引用whforever的这条留言" onclick="return CommentQuote('comment-quote-390819','whforever');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-390927" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">震灵</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-390927">
            <p>搜索貌似应该是发POST请求吧？</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="July 14, 2018  2:36 PM">2018年7月14日 14:36</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-390927">#</a>
 | <a href="#comment-text" title="引用震灵的这条留言" onclick="return CommentQuote('comment-quote-390927','震灵');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-391122" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">小罗</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-391122">
            <p>新版页面看不到代码块，360浏览器</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="July 19, 2018  3:25 PM">2018年7月19日 15:25</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-391122">#</a>
 | <a href="#comment-text" title="引用小罗的这条留言" onclick="return CommentQuote('comment-quote-391122','小罗');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-391261" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="https://github.com/OMGZui" href="https://github.com/OMGZui" target="_blank" rel="nofollow">omgzui</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-391261">
            <p>由于老师使用的是5.5.1，新版的6.3.1会报错{"error":"Content-Type header [application/x-www-form-urlencoded] is not supported","status":406}</p>

<p>解决办法：在每个请求上加上-H 'Content-Type:application/json'即可<br />
</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="July 22, 2018 11:45 AM">2018年7月22日 11:45</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-391261">#</a>
 | <a href="#comment-text" title="引用omgzui的这条留言" onclick="return CommentQuote('comment-quote-391261','omgzui');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-391493" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">helloworld</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-391493">
            <p>阮神的分享精神真的值的点赞！ 佩服</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="July 27, 2018  2:13 PM">2018年7月27日 14:13</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-391493">#</a>
 | <a href="#comment-text" title="引用helloworld的这条留言" onclick="return CommentQuote('comment-quote-391493','helloworld');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-392107" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="http://kuli.ren" href="http://kuli.ren" target="_blank" rel="nofollow">王业鑫</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-392107">
            <p>此文章通俗易懂，但是知识建立在使用的基础上，没有针对集群等做深入的分析</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="August 13, 2018 11:29 AM">2018年8月13日 11:29</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-392107">#</a>
 | <a href="#comment-text" title="引用王业鑫的这条留言" onclick="return CommentQuote('comment-quote-392107','王业鑫');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-392288" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="https://denganliang.com" href="https://denganliang.com" target="_blank" rel="nofollow">hemingway</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-392288">
            <p>照着你的例子练习了一遍，谢谢阮大神。</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="August 17, 2018  7:41 PM">2018年8月17日 19:41</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-392288">#</a>
 | <a href="#comment-text" title="引用hemingway的这条留言" onclick="return CommentQuote('comment-quote-392288','hemingway');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-392508" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">梦飞扬</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-392508">
            <p>一启动就被killed了, 查了下是内存的问题, 但是怎么解决呢? 求指导</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="August 24, 2018  8:35 PM">2018年8月24日 20:35</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-392508">#</a>
 | <a href="#comment-text" title="引用梦飞扬的这条留言" onclick="return CommentQuote('comment-quote-392508','梦飞扬');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-392644" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">德金</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-392644">
            <p>大神厉害，非常感谢，果断用起来</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="August 28, 2018  5:01 PM">2018年8月28日 17:01</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-392644">#</a>
 | <a href="#comment-text" title="引用德金的这条留言" onclick="return CommentQuote('comment-quote-392644','德金');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-393269" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">糖太粽</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-393269">
            <p>谢谢阮神 目前在学习</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="September 20, 2018  2:47 PM">2018年9月20日 14:47</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-393269">#</a>
 | <a href="#comment-text" title="引用糖太粽的这条留言" onclick="return CommentQuote('comment-quote-393269','糖太粽');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-394055" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">青城之树</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-394055">
            <p>感谢   条件检索用post</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="October 15, 2018  4:53 PM">2018年10月15日 16:53</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-394055">#</a>
 | <a href="#comment-text" title="引用青城之树的这条留言" onclick="return CommentQuote('comment-quote-394055','青城之树');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-395219" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="http://igeneral.top" href="http://igeneral.top" target="_blank" rel="nofollow">xhigeneral</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-395219">
            <blockquote>
<pre>引用Jeffy的发言：</pre>

<p><a href="http://localhost:9200/accounts/person/_search/" rel="nofollow">http://localhost:9200/accounts/person/_search/</a><br />
get:"max_score": 1,<br />
post:"max_score": 0.25316024<br />
为啥会这样呢?获取应该是get呀</p>

</blockquote>

<p>elastic的get常常都会带body的json，而post也是可以访问的。<br />
</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="November 21, 2018 11:17 PM">2018年11月21日 23:17</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-395219">#</a>
 | <a href="#comment-text" title="引用xhigeneral的这条留言" onclick="return CommentQuote('comment-quote-395219','xhigeneral');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-396603" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">FrozenSt</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-396603">
            <blockquote>
<pre>引用陈学礼的发言：</pre>

<p>路途坎坷，看楼主这篇文章我要先去安装VM虚拟机，然后在虚拟机中安装linux系统，然后还要安装jdk，然后才能安装楼主的elasticsearch，这中间遇到很多问题，还好都被解决了，总算把楼主的教程看完，谢谢楼主，如果楼主还有精力，希望顺便讲解一下Spring boot + elasticsearch + mysql的用法，综合在实际操作中的步骤，谢谢楼主，楼主辛苦了</p>

</blockquote>

<p>您要的这些，一本书可都讲不完。<br />
饭还是要一口一口吃。直接吃第三碗饭是吃不饱的。</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="December  5, 2018  4:17 PM">2018年12月 5日 16:17</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-396603">#</a>
 | <a href="#comment-text" title="引用FrozenSt的这条留言" onclick="return CommentQuote('comment-quote-396603','FrozenSt');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-401858" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">袖长风</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-401858">
            <p>我安装了elasticsearch-6.5.4 和 analysis-ik 6.5.4;<br />
重启：可以看到 ik plugin被加载<br />
[2019-01-08T17:25:10,333][INFO ][o.e.p.PluginsService     ] [00eqG7f] loaded plugin [analysis-ik]<br />
然后测试：<br />
 curl -XGET "http://localhost:9200/index/_analyze?pretty" -H 'Content-Type: application/json' -d'<br />
{<br />
   "text":"中华人民共和国MN","tokenizer": "ik_max_word"<br />
}'<br />
能够正常分词；<br />
但是！建立索引，设置mapping的时候出错了<br />
测试：<br />
curl -XPUT <a href="http://localhost:9200/index" rel="nofollow">http://localhost:9200/index</a><br />
curl -XPOST <a href="http://localhost:9200/index/fulltext/_mapping?pretty" rel="nofollow">http://localhost:9200/index/fulltext/_mapping?pretty</a> -H 'Content-Type:application/json' -d'<br />
{<br />
        "properties": {<br />
            "content": {<br />
                "type": "text",<br />
                "analyzer": "ik_max_word",<br />
                "search_analyzer": "ik_max_word"<br />
            }<br />
        }</p>

<p>}'</p>

<p>结果<br />
{<br />
  "error" : {<br />
    "root_cause" : [<br />
      {<br />
        "type" : "mapper_parsing_exception",<br />
        "reason" : "analyzer [ik_max_word] not found for field [content]"<br />
      }<br />
    ],<br />
    "type" : "mapper_parsing_exception",<br />
    "reason" : "analyzer [ik_max_word] not found for field [content]"<br />
  },<br />
  "status" : 400<br />
}</p>

<p>为什么找不到分词器呢？都折磨我两天了...<br />
</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="January  8, 2019  6:29 PM">2019年1月 8日 18:29</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-401858">#</a>
 | <a href="#comment-text" title="引用袖长风的这条留言" onclick="return CommentQuote('comment-quote-401858','袖长风');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-404481" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="http://alivl.com" href="http://alivl.com" target="_blank" rel="nofollow">艾郦湾</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-404481">
            <p>目前只有三万的数据 想通过elasticsearch做搜索 但是三万的数据在mysql里 这里只是讲解了增删改查 具体怎么样吧三万的数据能够快速的转移到elasticsearch上呢？</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="January 16, 2019  6:13 PM">2019年1月16日 18:13</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-404481">#</a>
 | <a href="#comment-text" title="引用艾郦湾的这条留言" onclick="return CommentQuote('comment-quote-404481','艾郦湾');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-405875" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">互联网非法移民</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-405875">
            <p>@zhujun24 multi_field+拼音analyzer就可以实现</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="January 24, 2019  5:38 AM">2019年1月24日 05:38</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-405875">#</a>
 | <a href="#comment-text" title="引用互联网非法移民的这条留言" onclick="return CommentQuote('comment-quote-405875','互联网非法移民');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-408257" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">mrs.utopian</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-408257">
            <p>୧(๑•̀◡•́๑)૭，很好写得，通俗易懂最难得！</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="February 15, 2019  4:40 PM">2019年2月15日 16:40</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-408257">#</a>
 | <a href="#comment-text" title="引用mrs.utopian的这条留言" onclick="return CommentQuote('comment-quote-408257','mrs.utopian');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-409550" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="https://newgr8player.github.io" href="https://newgr8player.github.io" target="_blank" rel="nofollow">晓晓</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-409550">
            <blockquote>
<pre>引用zhujun24的发言：</pre>

<p>ElasticSearch 可以做拼音搜索么？比如用 bianxingjingang 或者 bxjg 搜索到“变形金刚”。</p>

</blockquote>

<p>可以的，但是需要安装插件<br />
具体你可以看下这个项目，https://github.com/medcl/elasticsearch-analysis-pinyin<br />
但是已经有一段时间没更新了</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="February 28, 2019 10:21 AM">2019年2月28日 10:21</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-409550">#</a>
 | <a href="#comment-text" title="引用晓晓的这条留言" onclick="return CommentQuote('comment-quote-409550','晓晓');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-409694" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="https://github.com/leefyi" href="https://github.com/leefyi" target="_blank" rel="nofollow">leefyi</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-409694">
            <p>照着基础来了一遍。除去版本更新了以外，其它都差不多。 也算 体验了下～ 谢谢 阮大</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="March  3, 2019  9:44 PM">2019年3月 3日 21:44</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-409694">#</a>
 | <a href="#comment-text" title="引用leefyi的这条留言" onclick="return CommentQuote('comment-quote-409694','leefyi');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-409860" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="http://blog.justwe.site" href="http://blog.justwe.site" target="_blank" rel="nofollow">高飞</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-409860">
            <blockquote>
<pre>引用陈学礼的发言：</pre>

<p>路途坎坷，看楼主这篇文章我要先去安装VM虚拟机，然后在虚拟机中安装linux系统，然后还要安装jdk，然后才能安装楼主的elasticsearch，这中间遇到很多问题，还好都被解决了，总算把楼主的教程看完，谢谢楼主，如果楼主还有精力，希望顺便讲解一下Spring boot + elasticsearch + mysql的用法，综合在实际操作中的步骤，谢谢楼主，楼主辛苦了</p>

</blockquote>

<p>可以看官方的docker版安装啊, 简单的让你想哭... <a href="https://www.elastic.co/guide/en/elasticsearch/reference/current/docker.html" rel="nofollow">https://www.elastic.co/guide/en/elasticsearch/reference/current/docker.html</a><br />
</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="March 11, 2019  4:17 PM">2019年3月11日 16:17</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-409860">#</a>
 | <a href="#comment-text" title="引用高飞的这条留言" onclick="return CommentQuote('comment-quote-409860','高飞');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-409931" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="http://dawda" href="http://dawda" target="_blank" rel="nofollow">efasscaed</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-409931">
            <p>增加中文分词插件应当是如下配置文件<br />
	./bin/plugin install <a href="https://github.com/medcl/elasticsearch-analysis-ik/releases/download/v5.5.1/elasticsearch-analysis-ik-5.5.1.zip" rel="nofollow">https://github.com/medcl/elasticsearch-analysis-ik/releases/download/v5.5.1/elasticsearch-analysis-ik-5.5.1.zip</a></p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="March 15, 2019  3:14 PM">2019年3月15日 15:14</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-409931">#</a>
 | <a href="#comment-text" title="引用efasscaed的这条留言" onclick="return CommentQuote('comment-quote-409931','efasscaed');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-410052" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">鄉民</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-410052">
            <p>寫得真好, 一看就明白了</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="March 21, 2019  5:13 PM">2019年3月21日 17:13</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-410052">#</a>
 | <a href="#comment-text" title="引用鄉民的这条留言" onclick="return CommentQuote('comment-quote-410052','鄉民');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-410194" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">小恩</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-410194">
            <p>最好的入门文档,没有之一</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="March 28, 2019  2:05 AM">2019年3月28日 02:05</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-410194">#</a>
 | <a href="#comment-text" title="引用小恩的这条留言" onclick="return CommentQuote('comment-quote-410194','小恩');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-410430" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">小白菜不菜</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-410430">
            <blockquote>
<pre>引用omgzui的发言：</pre>

<p>由于老师使用的是5.5.1，新版的6.3.1会报错{"error":"Content-Type header [application/x-www-form-urlencoded] is not supported","status":406}</p>

<p>解决办法：在每个请求上加上-H 'Content-Type:application/json'即可</p>

</blockquote>

<p>没错，在6.0版本之后，请求都需要加 -H 'Content-Type:application/json'</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="April  8, 2019  5:30 PM">2019年4月 8日 17:30</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-410430">#</a>
 | <a href="#comment-text" title="引用小白菜不菜的这条留言" onclick="return CommentQuote('comment-quote-410430','小白菜不菜');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-410771" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">张小智</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-410771">
            <p>提交问题<br />
要让所有人访问是给配置文件加http.host为0.0.0.0 而不是改默认的network.host为0.0.0.0<br />
</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="April 24, 2019  8:15 AM">2019年4月24日 08:15</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-410771">#</a>
 | <a href="#comment-text" title="引用张小智的这条留言" onclick="return CommentQuote('comment-quote-410771','张小智');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-411074" class="comment">
    <div  class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author"><a title="https://eaglexiang.org" href="https://eaglexiang.org" target="_blank" rel="nofollow">eaglex</a></span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-411074">
            <p>通俗易懂，适合入门，非常感谢</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="May  9, 2019  4:08 PM">2019年5月 9日 16:08</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-411074">#</a>
 | <a href="#comment-text" title="引用eaglex的这条留言" onclick="return CommentQuote('comment-quote-411074','eaglex');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    
        
        <div id="comment-411157" class="comment">
    <div id="comment-last" class="inner">
        <div class="comment-header">
            <div class="asset-meta">
<p>
                <span class="byline">
                    

                    <span class="vcard author">mrs.utopian</span>

 说：
                </span>
</p>
            </div>
        </div>
        <div class="comment-content" id="comment-quote-411157">
            <p>获益良多</p>
        </div>
<div class="comment-footer">
<div class="comment-footer-inner">
<p>
                   <abbr class="published" title="May 13, 2019  4:40 PM">2019年5月13日 16:40</abbr>
 | <a href="http://www.ruanyifeng.com/blog/2017/08/elasticsearch.html#comment-411157">#</a>
 | <a href="#comment-text" title="引用mrs.utopian的这条留言" onclick="return CommentQuote('comment-quote-411157','mrs.utopian');">引用</a>
</p>
</div>
</div>
    </div>
</div>
        
    </div>
        
    


    
    
    <div class="comments-open" id="comments-open">
        <h2 class="comments-open-header">我要发表看法</h2>
        <div class="comments-open-content">

        
            <div id="comment-greeting"></div>

            <form method="post" action="http://www.ruanyifeng.com/cgi-bin/mtos/mt-comments.cgi" name="comments_form" id="comments-form" onsubmit="return pleaseWait();">
                <input type="hidden" name="static" value="1" />
                <input type="hidden" name="entry_id" value="1950" />
                <input type="hidden" name="__lang" value="en" />
                <input type="hidden" name="parent_id" value="" id="comment-parent-id" />
                <input type="hidden" name="armor" value="1" />
                <input type="hidden" name="preview" value="" />
                <input type="hidden" name="sid" value="" />
                <div id="comments-open-data">
            <div id="comments-open-text">
                    <p><label for="comment-text">您的留言
                    （HTML标签部分可用）</label></p>
                    <p><textarea id="comment-text" name="text" rows="10" cols="50"></textarea></p>
                </div>
                    <div id="comment-form-name">
                        <p><label for="comment-author">您的大名：</label></p>
                        <p><input id="comment-author" name="author" size="30" value="" />  <span class="hint"> &laquo;-必填</span></p>
                    </div>
                    <div id="comment-form-email">
                        <p><label for="comment-email">电子邮件：</label></p>
                        <p><input id="comment-email" name="email" size="30" value="" />  <span class="hint"> &laquo;-必填，不公开</span></p>
                    </div>
                    <div id="comment-form-url">
                        <p><label for="comment-url">个人网址：</label></p>
                        <p><input id="comment-url" name="url" size="30" value="" />  <span class="hint"> &laquo;-我信任你，不会填写广告链接</span></p>
                    </div>
                    <div id="comment-form-remember-me">
                        <p>
                        <label for="comment-bake-cookie">记住个人信息？</label><input type="checkbox" id="comment-bake-cookie" name="bakecookie" onclick="!this.checked?forgetMe(document.comments_form):rememberMe(document.comments_form)" value="1" accesskey="r" /></p>
                    </div>
                </div>
                    <div id="comment-form-reply" style="display:none">
                    <input type="checkbox" id="comment-reply" name="comment_reply" value="" onclick="mtSetCommentParentID()" />
                    <label for="comment-reply" id="comment-reply-label"></label>
                </div>
                <div id="comments-open-captcha"></div>
                <div id="comments-open-footer">
<div id="wait" style="display:none;">
<p><strong>正在发表您的评论，请稍候</strong></p>
<p>
<input type="text" name="percent" size="3" style="font-family:Arial; color:black;text-align:center; border-width:medium; border-style:none;">           
<input type="text" name="chart" size="46" style="font-family:Arial;font-weight:bolder; color:black; padding:0px; border-style:none;">
</p>
</div>

                    <p><input type="submit" accesskey="s" name="post" id="comment-submit" value="发表" />  <span class="hint"> &laquo;- 点击按钮</span></p>
                </div>
            </form>


        </div>
    </div>

    


</div>




                        </div>
                    </div>

                </div>
            </div>
        <script type="text/javascript" src="http://www.ruanyifeng.com/blog/js/prism.js"></script>
        <script type="text/javascript" src="/blog/checker.js"></script> 
            <div id="footer">
<div id="footer-inner">
   <div id="footer-content">
   <!--p><a title="微博" href="http://weibo.com/ruanyf" target="_blank">微博</a> | <a title="Twitter" target="_blank" href="https://twitter.com/ruanyf">推特</a> | <a title="GitHub" target="_blank" href="https://github.com/ruanyf">GitHub</a></p-->
   <p>2019 © <a title="电子邮件" href="/contact.html" target="_blank">联系方式</a> | <a title="订阅" href="https://app.feedblitz.com/f/f.fbz?Sub=348868" target="_blank">邮件订阅</a></p>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-46829782-1', 'ruanyifeng.com');
  ga('send', 'pageview');

</script>

<script type="text/javascript" src="/blog/stats.js"></script>
<script>
var supportImg = document.querySelector('#support-img');

if (supportImg && _hmt) {
  _hmt.push(['_trackEvent', 'banner', 'load']);
  supportImg.addEventListener('click', function () {
    _hmt.push(['_trackEvent', 'banner', 'click']);
  }, false);
}

var homepageImg = document.querySelector('#homepage_sponsor');
if (homepageImg && _hmt) {
  _hmt.push(['_trackEvent', 'homepage-banner', 'load']);
  homepageImg.addEventListener('click', function () {
    _hmt.push(['_trackEvent', 'homepage-banner', 'click']);
  }, false);
}
</script>



        </div>
    </div>
</div>


<div id="share_button_proto" style="display:none;">
<a class="bshareDiv" href="http://www.bshare.cn/share">分享按钮</a>



<script type="text/javascript" charset="utf-8" src="http://static.bshare.cn/b/buttonLite.js#uuid=15e016b4-0028-44f1-a40d-a3c9d9c13c28&style=10&bgcolor=#fff&bp=weixin,qqim,qzone,qqmb,sinaminiblog,fanfou,xueqiu,douban,facebook,twitter,gplus,instapaper&ssc=false"></script>
<script type="text/javascript" charset="utf-8">
bShare.addEntry({
    title: document.getElementById("page-title").innerHTML,
url:window.location.href
});
</script>
</div>

<script>
document.getElementById("share_button").innerHTML=document.getElementById("share_button_proto").innerHTML;
</script>





        </div>
    </div>
</body>
</html>