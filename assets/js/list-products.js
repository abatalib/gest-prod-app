$().ready(function(){
    let currentCategory = '';

    //construire la datatable
    displayDatatable();

    //au lancement de la page insérer
    //les catégories en les triant ASC
    executeInsertionCategory('asc');

    //pour mettre à jout le panier
    updateBasket();

    //ajouter un produit dans le panier
    addProductInBasket();

    //garder hover sélection catégorie
    //et récupérer les products de la catégorie
    onSelectCategory();

    //si on a cliquer sur le lien tous
    //pour afficher tous les produits
    displayAllProducts();

    //afficher la photo en modal
    viewPhotoInModal();

    //insérer les catégories en les triant
    //selon le bouton cliqué
    insertCategoryBySorting();

    

    function displayDatatable(idCategory = '')
    {
     dataTable = $('#listProducts').DataTable({
        columnDefs: [
          {
            targets: 0,
            className:      'details-control',
            orderable:      false,
            data:           null,
            defaultContent: ''
          },
          {
            targets: [7,8,9],
            visible: false
          }
        ],
        dom: 'Bfrtip', 
          "order": [[ 2, "asc" ]],
          scrollY:        '80vh',
          scrollX: true,
          scrollCollapse: true,
          paging:         true,
          pagingType: 'numbers',
                      language: {
                      processing:     "Traitement en cours...",
                      search:         "<b style='color:#333'>Filtre</b> ",
                      lengthMenu:    "Afficher _MENU_ &eacute;l&eacute;ments",
                      info:           "<h6>Affichage de l'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments</h6>",
                      infoEmpty:      "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
                      infoFiltered:   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
                      infoPostFix:    "",
                      loadingRecords: "Chargement en cours...",
                      zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
                      emptyTable:     "Aucune donnée disponible dans le tableau",
                      paginate: {
                          first:      "Premier",
                          previous:   "Pr&eacute;c&eacute;dent",
                          next:       "Suivant",
                          last:       "Dernier"
                      },
  
                      aria: {
                          sortAscending:  ": activer pour trier la colonne par ordre croissant",
                          sortDescending: ": activer pour trier la colonne par ordre décroissant"
                      },
  
                      responsive: {
                          details: {
                              type: 'column',
                              target: 'tr'
                          }
                      },
                  },
      "processing" : true,
      "serverSide" : true,
      "order" : [],
      "searching" : false,
      "ajax" : {
       url:"ajax-scripts",
       type:"POST",
       data:{
          idCategory,source:'get-products-of-selected-category'
       }
      }
     });
    }
    

    function displayAllProducts()
    {
        $('#displayAllCategory').on('click', function(){
            //si la variable contient une valeur, donc on a déjà
            //sélectionné une catégorie => la désectionner puis
            //réinitialiser la variable currentCategory
            if(currentCategory!='')
            {
               $('#'+currentCategory).removeClass('activated');
   
               currentCategory='';
   
               if ( $.fn.DataTable.isDataTable( '#listProducts' ) ) {
                   $('#listProducts').DataTable().destroy();
                };
   
               displayDatatable();
               $('.lib-category').html('Toutes les catégories');
            }
        });
    }

    function onSelectCategory()
    {
        $(document).on('click','.link-category',function(){
            let idCategory = $(this).attr('id');
            let name_category = $(this).attr('name');
            
            //test si on a déjà choisi une category
            //on la déselectionne
            if(currentCategory!='')
            {
                $('#'+currentCategory).removeClass('activated');
            }
            
            //on garde en mémoire la catégorie sélectionnée
            currentCategory=idCategory;
            
            //on garde la catégorie sélectionnée
            $('#'+idCategory).addClass('activated');

            //on détruit la datatable pour en créer une autre
            if ( $.fn.DataTable.isDataTable( '#listProducts' ) ) {
                $('#listProducts').DataTable().destroy();
             };

            if(idCategory != '')
            {
                displayDatatable(idCategory);
                $('.lib-category').html('Catégorie : '+name_category);
            }
            else
            {
                $('.lib-category').html('');
                alert('Veuillez choisir une catégorie');
                displayDatatable();
            }
        });
    }


    function viewPhotoInModal()
    {
        $(document).on('click', '.thumb-image-product', function(){
            let path_photo = $(this).attr('src');

            if(path_photo=='')
            {
                return;
            }

            $('.view-photo').attr('src',path_photo);
            $('#displayPhoto').modal('show');
        });
    }

    function insertCategoryBySorting()
    {
        $(document).on('click', '.btn-sort', function(){
            let sort = $(this).attr('data');
            if($(this).attr('id')=="asc")
            {
                $('#asc').css('color','#990000');
                $('#desc').css('color','#333');
            } else {
                $('#desc').css('color','#990000');
                $('#asc').css('color','#333');
            }
            executeInsertionCategory(sort);
        });
    }

    function executeInsertionCategory(sort)
    {
        $.ajax({
            url:'ajax-scripts',
            type: 'post',
            data:{sort,source:'get-categories-sidebar'},
            success:function(rep){
                if(rep=='err')
                {
                    alert("Erreur lors du chargement de la liste des catégories!")
                } else {
                    $('#div-category').html(rep);
                }
            }
        })
    }

    function openRow (row) {
        return `<div class="detail-row-row">
            <div class="row-title-element">
                Description
            </div>
            <div class="row-element">
                `+row[7]+`
            </div>
            <div class="row-title-element">
                Fabricants
            </div>
            <div class="row-element">
                `+row[8]+`
            </div>
            <div class="row-title-element">
                Photo
            </div>
            <div class="row-element">
                `+row[9]+`
            </div>
        </div>`;
      }
        
        
    //écoute pour ouvrir ou fermer la ligne
    $('body #listProducts tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = dataTable.row( tr );

        if ( row.child.isShown() ) {
            //ligne déjà ouverte on l'a ferme
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            //ligne fermée on l'ouvert
            row.child(openRow(row.data())).show();
            tr.addClass('shown');
        }
    } );

    function updateBasket()
    {
        $.ajax({
            url:'ajax-scripts',
            type:'post',
            data:{source:'update-basket'},
            success:function(rep){
                $('#div-basket').html(rep);
            }
        });
    } 

    function addProductInBasket()
    {
        $(document).on('click','.btn-add-in-basket', function(){
            let ref = $(this).attr('id');
            let qte = $('#qte'+ref).val();
            
    
            if(ref=='' || ref<1 || !$.isNumeric(ref) || qte=='' || qte<1 || !$.isNumeric(qte))
            {
                return
            }
    
            $.ajax({
                url:'ajax-scripts',
                type:'post',
                data:{ref,qte,source:'add-in-basket'},
                success:function(rep){
                    if(rep=='err')
                    {
                        alert("Erreur lors de l'opération");
                        return
                    }
                }
            });
    
            updateBasket();
        })
    }
});