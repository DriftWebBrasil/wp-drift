document.addEventListener("DOMContentLoaded", function() {
    if(document.querySelector(".trabalhe-conosco input[type='file']")) {
        document.querySelectorAll(".trabalhe-conosco input[type='file']").forEach(function(e,i,a) {
            e.addEventListener("change", function(event) {
                if(event.target.parentElement.parentElement.querySelector(".nome-arquivo")) {
                    event.target.parentElement.parentElement.querySelector(".nome-arquivo").innerHTML = event.target.files.item(0).name;
                } 
            })
        });
    }
})