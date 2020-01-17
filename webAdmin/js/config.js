var api = {
	"url":"http://myshop-server.com:8000/index.php/api",
	"appKey":"iWtWFgNCXSFNkSGGFZ2lKN1xIeK7cRIK",
	"version":"v1",
	"source":"admin",
	"expire":3600*1000,
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


var tableParams = {
            getParams :function(method) {
               var  params =  {
                    "method":method,
                    "source":api.source,
                    "version":api.version,
                    
                }

                params.sign = signs.gen_sign(params);
                return params;
            },
            editData:function(method,id,title,url,w,h) {


                if (title == null || title == '') {
                    title=false;
                };
                if (url == null || url == '') {
                    url="error.html";
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
                      body = layer.getChildFrame('body', index)
                      var params = tableParams.getParams(method);
                      params.id = id;
                      var iframeWin = window[layero.find('iframe')[0]['name']];

                      
                      Shop.post(params,function(rs) {
                        if(rs.code == 0) {
                            data = typeof(rs.data) == 'string' ? JSON.parse(rs.data) : rs.data;
                            iframeWin.editData(data);
                        }else {
                            layer.msg(rs.msg);
                        }
                       })
                      
                          
                      
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
} 