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
  plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
  imagetools_cors_hosts: ['picsum.photos'],
  menubar: 'file edit view insert format tools table help',
  toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
  toolbar_sticky: true,
  autosave_ask_before_unload: true,
  autosave_interval: '30s',
  autosave_prefix: '{path}{query}-{id}-',
  autosave_restore_when_empty: false,
  autosave_retention: '2m',
  image_advtab: true,
  link_list: [
    { title: 'My page 1', value: 'https://www.tiny.cloud' },
    { title: 'My page 2', value: 'http://www.moxiecode.com' }
  ],
  image_list: [
    { title: 'My page 1', value: 'https://www.tiny.cloud' },
    { title: 'My page 2', value: 'http://www.moxiecode.com' }
  ],
  image_class_list: [
    { title: 'None', value: '' },
    { title: 'Some class', value: 'class-name' }
  ],
  importcss_append: true,
  file_picker_callback: function (callback, value, meta) {
    /* Provide file and text for the link dialog */
    if (meta.filetype === 'file') {
      callback('https://www.google.com/logos/google.jpg', { text: 'My text' });
    }

    /* Provide image and alt text for the image dialog */
    if (meta.filetype === 'image') {
      callback('https://www.google.com/logos/google.jpg', { alt: 'My alt text' });
    }

    /* Provide alternative source and posted for the media dialog */
    if (meta.filetype === 'media') {
      callback('movie.mp4', { source2: 'alt.ogg', poster: 'https://www.google.com/logos/google.jpg' });
    }
  },
  templates: [
        { title: 'New Table', description: 'creates a new table', content: '<div class="mceTmpl"><table width="98%%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>' },
    { title: 'Starting my story', description: 'A cure for writers block', content: 'Once upon a time...' },
    { title: 'New list with dates', description: 'New List with dates', content: '<div class="mceTmpl"><span class="cdate">cdate</span><br /><span class="mdate">mdate</span><h2>My List</h2><ul><li></li><li></li></ul></div>' }
  ],
  template_cdate_format: '[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]',
  template_mdate_format: '[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]',
  height: 600,
  image_caption: true,
  quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
  noneditable_noneditable_class: 'mceNonEditable',
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