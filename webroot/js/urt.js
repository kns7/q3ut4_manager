$(document).ready(function(){
   $(this)
       .on("click",".btn-reload",function(e){
           console.log("Btn Reload")
           $('#reload-confirm').modal('show');
       })
       .on("click",".btn-settings",function(e){

       })
});