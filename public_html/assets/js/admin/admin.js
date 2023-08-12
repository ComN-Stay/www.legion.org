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
    }],
    fnDrawCallback: function() {
        $('._activeButton').bootstrapToggle();
    }
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

/********* entities activation ***********/

$('body').on('change', '._activeButton', function (e) {
    let id = $(this).data('id');
    let entity = $(this).data('entity');
    if($(this).prop("checked") == true){
        var status = 1;
    } else {
        var status = 0;
    }
    let message = (status == 1) ? 'Activation effectuée' : 'Désactivation effectuée';
    $.ajax({
        url: '/admin/' + entity + '/activation',
        type: 'POST',
        data: 'id=' + id + '&status=' + status,
        dataType: false,
        cache: false
    })
    .done(function(s) {
        $('#jsAlertBox').removeClass('success');
        $('#jsAlertBox').removeClass('error');
        $('#jsAlertBox').removeClass('notice');
        $('#jsAlertBox').removeClass('wait');
        var res = jQuery.parseJSON(s);
        if (res.result == 'success') {
            $('#successIcon').show();
            $('#alertMessage').html(message);
            $('#jsAlertBox').addClass('success');
        } else {
            $('#errorIcon').show();
            $('#alertMessage').html('Une erreur s\'est produite');
            $('#jsAlertBox').addClass('error');
        }
        $('#jsAlertBox').show();
    });
});

/************ stats visitess dashboard ****************/
$(document).on('click', '._changeStats', function(){
    let start = ($(this).data('start') != undefined) ? $(this).data('start') : $('#startStats').val();
    let datas = 'start=' + start; 
    if($(this).data('start') == undefined){
        datas = datas + '&end=' + $('#endStats').val();
    }
    $.ajax({
        url: '/admin/statistics',
        type: 'POST',
        data: datas,
        dataType: false,
        cache: false
    })
    .done(function(s) {
        var res = jQuery.parseJSON(s);
        if (res.result == 'success') {
            statChart.data.labels = res.labels;
            statChart.data.datasets[0].data = res.datas;
            statChart.update();
        } else {
            $('#chartStat').html('Une erreur s\'est produite');
        }
    });
});