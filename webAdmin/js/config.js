var api = {
	"url":"http://localhost:8000/api",
	"appKey":"iWtWFgNCXSFNkSGGFZ2lKN1xIeK7cRIK",
	"version":"v1",
	"source":"admin",
	"expire":3600*24*7*1000,
}

var Shop = {
	post : function(params,callback) {
		
		params.source = api.source;
		params.version = api.version;
		var  sign = signs.gen_sign(params);
		params.sign = sign;
		$.post(api.url,params,function(rs) {
			callback(rs);
		})
	}
}



//验签开始
//
 var signs = {
        gen_sign:function(params) {

            

            

            
            var sign = '';
            Object.keys(params).forEach(function(key){
             
              sign += key + params[key];
            });
      
            return md5(sign+api.appKey);

        }
}

 var dialog = {
      radio:function(params,title,url,type,cols,w,h) {
        openDialog(params,title,url,type,cols,w,h)
  	  },
	  checkbox:function(params,title,url,type,cols,w,h) {
	    openDialog(params,title,url,type,cols,w,h);
	  }   
}


function openDialog(params,title,url,type,cols,w,h) {
  if (title == null || title == '') {
            title=false;
        };
        if (url == null || url == '') {
            url="404.html";
        };
        if (w == null || w == '') {
            w=($(window).width()*0.9);
        };
        if (h == null || h == '') {
            h=($(window).height() - 50);
        };
        layer.open({
            type: 2,
            area: [w+'px', h +'px'],
            fix: false, //不固定
            maxmin: true,
            shadeClose: true,
            shade:0.4,
            title: title,
            content: url,
            success: function(layero, index){

              params.sign = signs.gen_sign(params);
              body = layer.getChildFrame('body', index)

              var iframeWin = window[layero.find('iframe')[0]['name']];
                  
              iframeWin.setData(params,type,cols);
              //窗口加载成功刷新frame
              // location.replace(location.href);
            },
            cancel:function(){
              //关闭窗口之后刷新frame
              // location.replace(location.href);
            },
            end:function(){
              //窗口销毁之后刷新frame
              // location.replace(location.href);
            }
        });
}