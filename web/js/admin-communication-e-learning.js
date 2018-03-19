$(document).ready(function(){
    /**
     * *********************************************************************************************
     * Communication - E-learning
     * Activation de pagination, barre recherche, filtre au chargement de la page de liste de post
     * *********************************************************************************************
     */
    installJPlist();
    /**
     * *********************************************************************************************
     * FIN
     * Communication - E-learning
     * Activation de pagination, barre recherche, filtre au chargement de la page de liste de post
     * *********************************************************************************************
     */

    /**
     * *********************************************************************************************
     * Communication - E-learning
     * Création e-learning
     * *********************************************************************************************
     */
    // appel de formulaire
    $('.create-e-learning-button').on('click', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        var create_e_learning_url = $('input[name=create_e_learning_url]').val();
        $.ajax({
            type: 'GET',
            url: create_e_learning_url,
            success: function(data){
                $('#create-edit-e-learning-modal').find('.modal-body-container').html(data.content);
                installWysiwyg();
                installColorPicker();
            },
            statusCode: {
                404: function(data){
                    $('#create-edit-e-learning-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Contenu non trouvé' : data.responseJSON.message;
                    $('#create-edit-e-learning-modal').find('.error-message-container.general-message').text(message);
                },
                500: function(data){
                    $('#create-edit-e-learning-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Erreur interne' : data.responseJSON.message;
                    $('#create-edit-e-learning-modal').find('.error-message-container.general-message').text(message);
                }
            },
            complete: function(){
                $('#create-edit-e-learning-modal').modal('show');
                $('.chargementAjax').addClass('hidden');
            }
        });
    });

    // soumission de formulaire
    $(document).on('click', '#create-edit-e-learning-modal .submit-block-container .btn-valider', function(e){
        e.preventDefault();
        $('.chargementAjax').removeClass('hidden');
        for (name in CKEDITOR.instances) {
            CKEDITOR.instances[name].updateElement();
        }
        var submission_type = $(this).attr('data-submission-type');
        var data = {'submission_type': submission_type};
        var target_url = $('input[name=create_e_learning_url]').val();
        var redirect_target = $('input[name=e_learning_list]');
        $('#create-edit-e-learning-modal form').ajaxSubmit({
            type: 'POST',
            url: target_url,
            data: data,
            success: function(data){
                if(data['error']){
                    $('#create-edit-e-learning-modal').find('.modal-body-container').html(data.content);
                    installWysiwyg();
                    installColorPicker();
                } else {
                    // window.location.replace(redirect_target);
                }
            },
            statusCode: {
                404: function(){
                    $('#create-edit-e-learning-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Contenu non trouvé' : data.responseJSON.message;
                    $('#create-edit-e-learning-modal').find('.error-message-container.general-message').text(message);
                },
                500: function(){
                    $('#create-edit-e-learning-modal').find('.modal-body-container').html('');
                    var message = 'undefined' === typeof data.responseJSON ? 'Erreur interne' : data.responseJSON.message;
                    $('#create-edit-e-learning-modal').find('.error-message-container.general-message').text(message);
                }
            },
            complete: function(){
                $('.chargementAjax').addClass('hidden');
            }
        });
    });


    /**
     * *********************************************************************************************
     * FIN
     * Communication - E-learning
     * Création e-learning
     * *********************************************************************************************
     */
});
