(function(f){var e,g="";f.fn.pasteUploadImage=function(b){e=f(this);g=b;e.on("paste",function(a){var b;var d=a.originalEvent;if(d.clipboardData&&d.clipboardData.items&&(b=isImage(d)))return a.preventDefault(),a=getFilename(d)||"image.png",pasteText("{{"+a+"(uploading...)}}"),uploadFile(b.getAsFile(),a)});e.on("drop",function(a){var b;var d=a.originalEvent;if(d.dataTransfer&&d.dataTransfer.files&&(b=isImageForDrop(d)))return a.preventDefault(),a=d.dataTransfer.files[0].name||"image.png",pasteText("{{"+a+"(uploading...)}}"),uploadFile(b,a)})};pasteText=function(b){var a=e[0].selectionStart;var c=e[0].selectionEnd;var d=e.val().length;var f=e.val().substring(0,a);d=e.val().substring(c,d);e.val(f+b+d);e.get(0).setSelectionRange(a+b.length,c+b.length);return e.trigger("input")};isImage=function(b){var a;for(a=0;a<b.clipboardData.items.length;){var c=b.clipboardData.items[a];if(-1!==c.type.indexOf("image"))return c;a++}return!1};isImageForDrop=function(b){var a;for(a=0;a<b.dataTransfer.files.length;){var c=b.dataTransfer.files[a];if(-1!==c.type.indexOf("image"))return c;a++}return!1};getFilename=function(b){var a;window.clipboardData&&window.clipboardData.getData?a=window.clipboardData.getData("Text"):b.clipboardData&&b.clipboardData.getData&&(a=b.clipboardData.getData("text/plain"));a=a.split("\r");return a[0]};getMimeType=function(b,a){b=b.type;a=a.substring(a.lastIndexOf(".")+1);return b!="image/"+a?"image/"+a:b};uploadFile=function(b,a){var c=new FormData;c.append("imageFile",b);c.append("mimeType",getMimeType(b,a));
c.append("action","gdk_pasteup_imag");c.append("pui_nonce",pui_vars.pui_nonce);f.ajax({url:g,data:c,type:"post",processData:!1,contentType:!1,dataType:"json",xhrFields:{withCredentials:!0},success:function(b){return b.success?insertToTextArea(a,b.message):replaceLoadingTest(a)},error:function(b,c){replaceLoadingTest(a);console.log(b.responseText)}})};insertToTextArea=function(b,a){return e.val(function(c,d){return d.replace("{{"+b+"(uploading...)}}",'<a href="'+a+'"><img src="'+a+'" class="aligncenter size-full" /></a>\n')})};replaceLoadingTest=function(b){return e.val(function(a,c){return c.replace("{{"+b+"(uploading...)}}",b+"\n")})}})(jQuery);