$(document).ready(
    function(){
        //Gestions choix récompenses
        $(document).on(
            'click', '.radioBtn', function(){
                $('.radioBtn').each(
                    function(i){
                        $(this).addClass('unchecked');
                        $(this).removeClass('checked');
            
                        $(this).parent().parent().removeClass("active");
                        $(this).parent().parent().addClass("inactive");
                    }
                );
        
                $(this).addClass('checked');
        
                $(this).parent().parent().removeClass("inactive");
                $(this).parent().parent().addClass("active");
            }
        );
    
        //Gestions des choix mode multi-opérations
        $(document).on(
            'click', '.checkboxBtn', function(){
                /*
                $('.checkboxBtn').each(function(i){
                $(this).addClass('no-mode');
                $(this).removeClass('est-mode');
                });
                */
        
                if($(this).hasClass('est-mode')) {
                    $(this).addClass('no-mode');
                    $(this).removeClass('est-mode');
                }else{
                    $(this).addClass('est-mode');
                    $(this).removeClass('no-mode');
                }
            }
        );
    }
);

