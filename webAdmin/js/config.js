var api = {
	"url":"http://localhost:8000/api",
}

var Shop = {
	post : function(method,data,callback) {
		$.post(api.url,{"method":method,"source":"admin","data":JSON.stringify(data)},function(rs) {
			callback(rs);
		})
	}
}