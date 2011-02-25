clickMenu = function(menu) {
   var getEls = document.getElementsByTagName("LI");
   var getAgn = getEls;

   for (var i=0; i<getEls.length; i++) {
         getEls[i].onclick=function() {
            for (var x=0; x<getAgn.length; x++) {
            getAgn[x].className=getAgn[x].className.replace("click", "");
            }
            this.className+=" click";
            }
         }
      }