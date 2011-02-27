function scontent_up(id){
  scontet_loading(id);
  scontent_ajax(id,"voteup");
}
function scontent_down(id){
  scontet_loading(id);
  scontent_ajax(id,"votedown");
}
function scontent_report(id){
  scontent_ajax,(id,"report");
}
function scontent_ajax(id,type){
     jQuery.ajax({
        cache: false,
        data: "task="+type+"&article_id="+id,
        type: "GET",
        dataType: "json",
        url: uribase+"plugins/content/scontent/scontent.php",
        success: function(data){ voteresults(data,id); }
    });
}
function voteresults(data,id) {
      jQuery("div.vote_result-"+id).fadeOut("fast");
       if(data.count>=0) {
        if(data.count==0) {
       jQuery("div.totalvotes-"+id).attr("class", "totalvotes-"+id+" neutral").html(data.count);
       } else {
       jQuery("div.totalvotes-"+id).attr("class", "totalvotes-"+id+" up").html("+"+data.count);
       }
      } else {
      jQuery("div.totalvotes-"+id).attr("class", "totalvotes-"+id+" down").html(data.count);
      }
      jQuery("div.vote_result-"+id).fadeIn("slow");
      jQuery("div.status-"+id).html(data.msg);
}
function scontet_loading(id) {
   jQuery("div.status-"+id).html("<img src=\""+uribase+"plugins/content/scontent/images/loading.gif\"/img>");
   return;
}
jQuery.noConflict();