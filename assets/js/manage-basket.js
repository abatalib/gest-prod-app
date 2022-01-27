$().ready(function(){


    $('.save-basket').on('click', function(){
        $.ajax({
            url:'ajax-scripts',
            type:'post',
            data:{source:'save-cde'},
            success:function(rep){
                if(rep=='c')
                {
                    window.location.href="connexion";
                }
            }
        })
    });


    //pour vider le panier
    emptyBasket();

    //incrémenter/décrémenter la qté
    changeQty();

    //supprimer un produit
    delProduct();


    function emptyBasket()
    {
        $('.empty-basket').on('click', function(){
            $.ajax({
                url: 'ajax-scripts',
                type: 'post',
                data:{source:'empty-basket'},
                success:function(rep){
                    if(rep=='ok')
                    {
                        window.location.replace('.');
                    }
                }
            })
        })
    }

    function changeQty()
    {
        $('.btn-change-qty').on('click', function(){
            let ref = $(this).attr('data');
            let id = $(this).attr('id');
            let currentQty = parseInt($('#qty'+ ref).val());
            let price = parseFloat($('#px'+ref).val());
            let total = 0;

            

            if(id.substr(0,1)=='i')//i : increment sinon decrement
            {
                currentQty++;
            } else if(currentQty>1)
            {
                currentQty--;
            }

            //affectation de la nouvelle valeur qté            
            $('#qty'+ ref).val(currentQty);

            //affecation de la nouvelle valeur du total product
            total = (currentQty*price).toFixed(2);
            $('#total_product'+ref).val(total);

            //recalcul du nouveau total général
            updateTotalG();

            $.ajax({
                url:'ajax-scripts',
                type:'post',
                data:{source:'change-qty-basket',ref,currentQty}
            })
        })
    }

    function delProduct()
    {
        $('.delete-product').on('click',function(){
            let ref = $(this).attr('data');
            $.ajax({
                url:'ajax-scripts',
                type:'post',
                data:{source:'del-product-basket',ref},
                success:function(rep)
                {
                    if(rep=='ok')
                    {
                        $('#div-ref-'+ref).remove();
                        updateTotalG();
                    }
                }
            })
        })
    }

    function updateTotalG()
    {
        let new_total_g = 0;
        $('.total-price-product-basket').each(function(){
            new_total_g += $(this).val()*1;
        });

        $('#totalg').val(new_total_g.toFixed(2)+' DH');
    }
});