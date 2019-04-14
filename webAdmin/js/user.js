layui.use('layer', function(){
  var layer = layui.layer;

  var userTooken = getCookie('accessToken');
  if(!userTooken) {
    location.href='../page/admin/login.html';
  }
  
});