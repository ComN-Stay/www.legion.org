/********* Datatable ************/
new DataTable('._datatable', {
    order: [[1, 'asc']],
    language: {
        url: '/assets/js/libraries/datatable-i18n-fr.json'
    },
    rowReorder: true,
    columnDefs: [ {
        'targets': [$('thead tr th').length -1],
        'orderable': false, 
    }]
});

/********* TinyMCE ************/
tinymce.init({
    selector: '.tinymce',
    plugins: 'a_tinymce_plugin',
    a_plugin_option: true,
    a_configuration_option: 400
  });

/********* Delete alert ************/
$(document).on('click', '._deleteBtn', function(e) {
    e.preventDefault();
    let parent = $(this).parent('form');
    $.confirm({
        theme: 'supervan',
        icon: 'fa-solid fa-triangle-exclamation fa-2xl text-red',
        title: '',
        content: 'Supprimer cette donnée ?<br />Attention cette action est irréversible',
        buttons: {
            confirm: {
                text: "Oui",
                action: function() {
                    parent.submit();
                }
            },
            cancel: {
                text: "Non"
            }
        }
    });
});

/********* Close alerts ************/
$(document).ready(function () {
    $( '.alert-close' ).click(function() {
        $( this ).parent().parent().fadeOut();
    });
});

/****** sidebar dropdown *********/
document.addEventListener("DOMContentLoaded", function(){
    document.querySelectorAll('.sidebar .nav-link').forEach(function(element){
        element.addEventListener('click', function (e) {
            let nextEl = element.nextElementSibling;
            let parentEl  = element.parentElement;	
            if(nextEl) {
                e.preventDefault();
                new bootstrap.Collapse(nextEl);
                element.querySelectorAll('.caretIcon').forEach(function(caret){
                    if(caret.getAttribute('class') == 'caretIcon caretIconOpen'){
                        caret.setAttribute('class', 'caretIcon caretIconClose');
                    } else {
                        caret.setAttribute('class', 'caretIcon caretIconOpen');
                    } 
                });
            }
        }); 
    });
});