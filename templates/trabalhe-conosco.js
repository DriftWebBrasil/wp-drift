document.addEventListener("DOMContentLoaded", function() {
    var accordion = document.querySelectorAll(".trabalhe-content .accordion .accordion-clickable");
    if(accordion) {
      accordion.forEach(function(el,i,a) {
        el.addEventListener("click", function(e) {
          if(!(e.target.tagName == 'BUTTON')) {
            jQuery(e.target).next().slideToggle(300); jQuery(this).toggleClass('aberto');
          }    
        });
      })
    }
  
    var vagas = document.querySelector(".trabalhe-content #vagas");
    if(vagas && vagas.classList.contains("sem-vagas")) {
        var inputs = document.querySelectorAll("form input, form select, form textarea, form button, form .dft-primary-button");
        inputs.forEach(function(e,a,i) {
          e.disabled = true;
        });
    }
  
    if(document.querySelector(".trabalhe-content #input_" + site_info.form_id + "_5")) {
      document.querySelector(".trabalhe-content  #input_" + site_info.form_id + "_5").accept = "image/*, application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document";
    }
  
    
    if(document.querySelector(".trabalhe-content input[type='file']")) {
      document.querySelectorAll(".trabalhe-content input[type='file']").forEach(function(e,i,a) {
          e.addEventListener("change", function(event) {
              if(event.target.parentElement.parentElement.querySelector(".nome-arquivo")) {
                  event.target.parentElement.parentElement.querySelector(".nome-arquivo").innerHTML = event.target.files.item(0).name;
              } 
          })
      });
    }
})