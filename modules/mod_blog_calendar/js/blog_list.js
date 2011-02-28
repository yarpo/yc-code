    function toggle_visibility(id) {
       var e = document.getElementById(id);
       if(e.style.display == 'block'){
          e.style.display = 'none';
		  document.getElementById('toggle-'+id).innerHTML="&#9658;";
		  }
       else{
          e.style.display = 'block';
		  document.getElementById('toggle-'+id).innerHTML="&#9660;";
		  }
		  
    }