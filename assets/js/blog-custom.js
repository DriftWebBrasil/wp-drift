document.addEventListener("DOMContentLoaded", function() {

    if(document.querySelector(".menu-search-icon") && document.querySelector("body").classList.contains("single") ) {

        document.querySelector(".menu-search-icon").addEventListener("click", function(e) {

            document.querySelector(".menu-search-mobile").classList.toggle("aberto");

        })

    }

    if(document.querySelector(".gallery") && document.querySelector("body").classList.contains("single") ) {

        document.querySelectorAll(".gallery").forEach(function(element, key, parent) {

            var parentId = element.id;

            element.querySelectorAll(".gallery-icon a").forEach(function(element, key, parent) {

                element.dataset.lightbox = "set-" + parentId;

            });

        });

    }

});