    </div></div>
        <div id='editcontent_iframedivs'>
        <div style='position: relative; padding: 5px;'>&nbsp<div style='position: absolute; right: 10px; top: 0px;'><a href='javascript:void(0);' onClick='wi3_reload_iframe();'>Herlaad pagina</a></div></div>
        <div style='height: 5px; background: #0b5aa3;background:#aaa;'></div>
        <iframe id='wi3_edit_iframe' src='<?php echo Wi3::$urlof->wi3 . "engine/edit_page/" . $pageid; ?>' ></iframe>
        <script>
            $("#wi3_edit_iframe").css("height", ($(window).height() - $("#wi3_edit_iframe").offset().top - 3) + "px");
            //$(document).bind("resize",function() {
            //    $("#wi3_edit_iframe").css("height", ($(window).height() - $("#wi3_edit_iframe").offset().top - 3) + "px");
            //});
        </script>
    </div>