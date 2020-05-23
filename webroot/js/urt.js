$(document).ready(function(){
   $(this)
       .on("click",".btn-reload",function(e){
           console.log("Btn Reload")
           $('#reload-confirm').modal('show');
       })
       .on("click",".btn-settings",function(e){
            $.get('/ajax/settings',function(d){
                $(".settings-content").html(d);
                $('#reload').bootstrapToggle()
                $("#settings").modal('show');
            })
       })
       .on('change','select#map',function(e){
           console.log("Map Update");
           $(".map-preview").attr('src',$(this).find(':selected').attr('data-img'));
           $("#reload").bootstrapToggle('on').bootstrapToggle('disable');
       })
       .on("change",'select#gametype',function(e){
           $.get("/ajax/gametype-desc/"+$(this).val(),function(d){
              $(".gametype-preview small").html(d);
           });
       })
       .on("click",'select')
});