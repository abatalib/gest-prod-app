$().ready(function(){

    $('.progress').hide();
    $('#msg').hide();
    
    transfert();



    function transfert()
    {        
        $(document).on('submit','#frm-transfert', function(event){
            event.preventDefault();
            $(this).ajaxSubmit({
                beforeSubmit:function(){
                    $('.progress').show();
                    $('.progress-bar').width('25%');
                },
                uploadProgress: function(event, position, total, percentageComplete)
                {
                    $('.progress-bar').animate({
                        width: percentageComplete + '%'
                    }, {
                        duration: 800
                    });
                },
                success:function(rep){
                    $('.progress').hide();
                    $('#msg').show();
                    switch(rep)
                    {
                        case 'err':
                            $('#msg').html("Erreur lors de transfert!")
                            break;
                        case 'data_already_exist':
                            $('#msg').html("Erreur lors de transfert. Données déjà transférées!")
                            break;
                        case 'ok':
                            $('#msg').html("Transfert effectué!")
                            setTimeout(() => {
                                $('#msg').hide();
                            }, 4000);
                            break;
                        default:
                            window.location.replace('.')
                    }
                },
            });
            return false;
        });
    }

});