jQuery(function($) {

    
    $("#start_view").click(function(){
        var ajax_data = {
            action: 'gdk_pass_view',
            pass_nonce: ajax.pass_nonce,
            id: $('#start_view').data('id'),
            pass: $('#pass_view').val()
        };
        $.post(ajax.url, ajax_data,
            function(c) {
                c = c.replace(/\s/g,'');
                if(c != '0')  {
                    $(".pass_viewbox").hide();
                    $(".pass_viewbox").after("<div class='content-hide-tips'><span class='rate label label-warning'>隐藏内容：</span><p>" + c + "</p></div>");
                    localStorage.setItem('pass_'+ajax_data['id'],ajax_data['pass']);
                }else{
                    swal("查看失败", "您的密码错误，请重试", "error");
                }
                
            });
    });






























});