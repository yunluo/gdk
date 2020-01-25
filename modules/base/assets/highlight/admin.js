(function() {
    var mce_highlight;
    tinymce.PluginManager.add('nicetheme_highlight_button', function( editor, url ) {
        editor.addButton('nicetheme_highlight_button', {
                    tooltip: '代码高亮 - 积木',
                    icon: 'code',
                    onclick: function() {
                        
                        if( screen.availWidth > 400 ){
                            var area = ['800px', '500px'];
                            var pl = '35px';
                        }else{
                            var area = ['300px', '500px'];
                            var pl = '13px';
                        }
                        

                        mce_highlight = layer.open({
                            type: 1,
                            title: '添加代码 - 代码高亮 - 积木',
                            shadeClose: true,
                            shade: 0.8,
                            area: area,
                            content: '<div class="mce-highlight form-wrap" style="padding:0 0 0 '+pl+'"><div class="form-field"><label>代码语言：</label><input id="nicetheme-highlight-lang" type="text" size="40"></div><div class="form-field"><label>代码内容：</label><textarea id="nicetheme-highlight-code-body" rows="10"></textarea></div><div class="submit"><button class="button button-primary nicetheme-highlight-insert-code">插入代码</button></div></div>'
                        }); 
                   }
          });
    });
    jQuery(document).on('click', '.nicetheme-highlight-insert-code', function(e){
        e.preventDefault();
        
        var lang = jQuery('#nicetheme-highlight-lang').val();
        var code = jQuery('#nicetheme-highlight-code-body').val();

        send_to_editor('<pre class="'+lang+'">'+code+'</pre>');

        layer.close(mce_highlight);
    });
})();