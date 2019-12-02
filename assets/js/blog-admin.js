document.addEventListener("DOMContentLoaded", function() {

    if( document.body.classList.contains('post-type-post') && document.body.classList.contains('wp-admin') ){

        let ativo = document.querySelectorAll('label.switch.btn-posts_toggle input');
        ativo.forEach( function(a){

            a.addEventListener('change', function(event){

                let value = event.target.getAttribute('value');
                if(value == 'false'){
    
                    value = '';
    
                }
                disable();
    
                let id = event.target.parentNode.parentNode.parentNode.id.replace(/^\D+|\D+$/g, "");
    
                ajaxPosts(value, id);
    
            });

        });
        
        function disable(){

            ativo.forEach( function(el){

                el.parentNode.classList.toggle('disabled');
    
            });

        }

        function ajaxPosts(value, id){

            var xhr = new XMLHttpRequest();
            var data = new FormData();

            data.append('action', 'setPostFeatured');
            data.append('value', value);
            data.append('id', id);
            data.append('nonce', ajax_slide2.nonce);

            xhr.open('POST', ajax_slide2.ajaxurl);
            xhr.responseType = 'json';

            xhr.onload = function(response) {

                disable();
                if( xhr.status === 200 ){

                    if( xhr.response.change == 'true' ){
                        
                        let switchToggle = document.querySelectorAll('.btn-posts_toggle input');
                        let post = 'post-' + id;
                        let post2;
                        ( xhr.response.id > 0 ) ? post2 = 'post-' + xhr.response.id : '';
                        switchToggle.forEach(function(e){
                            
                            if( e.parentNode.parentNode.parentNode.id == post ){
                                
                                e.setAttribute('value', 'true');

                            } else if( e.parentNode.parentNode.parentNode.id == post2 ){

                                e.setAttribute('value', 'false');
                                e.checked = !e.checked;

                            }

                        });

                    } else{

                        let switchToggle = document.querySelectorAll('.btn-posts_toggle input');
                        let post = 'post-' + id;
                        switchToggle.forEach(function(e){

                            if( e.parentNode.parentNode.parentNode.id == post ){

                                e.setAttribute('value', 'false');

                            }

                        });

                    }

                } else {

                }

            };
            
            xhr.send(data);

        }

    }

});