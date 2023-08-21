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

var useDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;

tinymce.init({
  selector: 'textarea.tinymce',
  plugins: 'paste searchreplace autolink code visualblocks visualchars fullscreen image link media table charmap hr anchor advlist lists wordcount imagetools help quickbars emoticons',
  menubar: 'file edit view insert tools table help',
  toolbar: 'undo redo | bold italic underline strikethrough | formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | removeformat | charmap emoticons | fullscreen | insertfile image media template link anchor',
  toolbar_sticky: true,
  autosave_ask_before_unload: true,
  autosave_interval: '30s',
  autosave_prefix: '{path}{query}-{id}-',
  autosave_restore_when_empty: false,
  autosave_retention: '2m',
  image_advtab: true,
  height: 600,
  image_caption: true,
  quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
  toolbar_mode: 'sliding',
  contextmenu: 'link image imagetools table',
  skin: useDarkMode ? 'oxide-dark' : 'oxide',
  content_css: useDarkMode ? 'dark' : 'default',
  content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
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
            document.querySelectorAll('.show').forEach(function(openBlock){
                new bootstrap.Collapse(openBlock);
                let prevEl = openBlock.previousElementSibling;
                prevEl.querySelectorAll('.caretIcon').forEach(function(caret){
                    if(caret.getAttribute('class') == 'caretIcon caretIconOpen'){
                        caret.setAttribute('class', 'caretIcon caretIconClose');
                    } else {
                        caret.setAttribute('class', 'caretIcon caretIconOpen');
                    } 
                });
            });
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

/*********** suppression logos *************/
$(document).on('click', '._deleteLogo', function(){
    let id = $(this).data('id');
    let entity = $(this).data('entity');
    let current = $(this).parent('div');console.log(current);
    $.confirm({
        theme: 'supervan',
        icon: 'fa-solid fa-triangle-exclamation fa-2xl text-red',
        title: '',
        content: 'Supprimer cette donnée ?<br />Attention cette action est irréversible',
        buttons: {
            confirm: {
                text: "Oui",
                action: function() {
                    $.ajax({
                        url: '/admin/' + entity + '/deleteLogo',
                        type: 'POST',
                        data: 'id=' + id,
                        dataType: false,
                        cache: false
                    })
                    .done(function(res) {
                        if (res.result == 'success') {
                            current.hide();
                        } else {
                            $('#errorIcon').show();
                            $('#alertMessage').html('Une erreur s\'est produite');
                            $('#jsAlertBox').addClass('error');
                            $('#jsAlertBox').show();
                        }
                    });
                }
            },
            cancel: {
                text: "Non"
            }
        }
    });
})