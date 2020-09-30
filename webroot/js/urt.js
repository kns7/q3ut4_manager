function loader(status,overlay){
    if(overlay === undefined){
        overlay = false;
    }
    if(status){
        $(".loader").fadeIn(200);
        if(overlay){ $(".overlay").fadeIn(10); }
    }else{
        if($(".overlay").is(":visible")){ $(".overlay").fadeOut(10); }
        $(".loader").fadeOut(200);
    }
}

function reloadStatus(){
    loader(true);
    $.ajax({
        url: "/ajax/status",
        method: "GET",
        success: function(datas,status){
            $(".map-img").attr('src',datas.mapimg);
            $(".map-name").text(datas.mapname);
            $(".timelimit-status").text(datas.timelimit);
            $(".roundtime-status").text(datas.roundtime);
            $(".gametype-name").text(datas.gametypename)
            $(".gametype-description").text(datas.gametypedescription)
            if(datas.players.length > 0){
                $(".alert-noplayers").addClass("hidden");
                $("table.table-players tbody").html("")
                for(i=0;i<datas.players.length;i++){
                    $("table.table-players tbody").append("<tr><td>"+datas.players[i].name+"</td><td class='text-right'>"+datas.players[i].score+"</td><td class='text-right'>"+datas.players[i].ping+"</td></tr>")
                }
                $("table.table-players").removeClass("hidden");
            }else{
                $(".alert-noplayers").removeClass("hidden");
                $("table.table-players").addClass("hidden");
            }
            loader(false);
        }
    })
}

// Daemon
setTimeout(function(){
    reloadStatus();
    setTimeout(arguments.callee,20000);
},20000);



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
       .on("change",".form-settings .form-control",function(e){
           $(this).attr('data-changes','1');
       })
       .on('click','.btn-reload-confirm',function(e){
           e.preventDefault();
           e.stopPropagation();
           $("#reload-confirm").modal("hide");
           loader(true,true);
           $.ajax({
               method: "POST",
               url: "/ajax/action/reload",
               success: function(xhr){
                   loader(false);
               }
           })
       })
       .on("click",".btn-saveparams",function(e){
           e.preventDefault();
           e.stopPropagation();
           loader(true,true);
           var data = {};
           $(".form-settings .form-control").each(function(){
               if($(this).attr('data-changes') == "1" || $(this).attr('id') == "reload"){
                   if($(this).attr('id') == "reload"){
                       data[$(this).attr('id')] = document.getElementById('reload').checked;
                   }else{
                       data[$(this).attr('id')] = $(this).val();
                   }
               }
           });
           $("#settings").modal("hide");
           $.ajax({
               method: "POST",
               url: "/ajax/action/saveParams",
               data: data,
               success: function(d){
                   loader(false);
               },
               error: function(d){
                   loader(false);
               }
           })
       })
});

