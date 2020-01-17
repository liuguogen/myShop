layui.use('layer', function(){
  var layer = layui.layer;

  var userToken = getCookie('accessToken');
  if(!userToken) {
    location.href='../admin/login.html';
  }
  
});