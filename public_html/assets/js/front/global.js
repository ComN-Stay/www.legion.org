/********* address autocomplete **************/
$(document).on('keyup', '#company_address', function(){
    let datas = $(this).val();
	if(datas.length < 3) {
		$('#result').html('');
		return false;
	}
    $.ajax({
        url:'https://api-adresse.data.gouv.fr/search/?q=' + datas.replace(' ', '%20') + '&limit=7',
        type: 'GET',
        dataType: false,
        data: {'ville': datas},
        async: true,
        success: function (result)
        {
			let res = result;
            $('#result').html('').show();
			let list = '';
			for (i=0; i<res['features'].length; i++) {
                let obj = res['features'][i]['properties'];
                let geo = res['features'][i]['geometry']['coordinates'];console.log(geo)
				list += '<li class="resultTerm" data-address="' + obj['name'] + '" data-zip="' + obj['postcode'] + '" data-town="' + obj['city'] + '" data-lng="' + geo[0] + '" data-lat="' + geo[1] + '">';
                list += obj['label'] + '<br />' + obj['context'] + '</li>';
			}
			$('#result').html('<ul>' + list + '</ul>');
        }
    });
    return false;
});

$(document).on('click', '.resultTerm', function(){
    $('#company_address').val($(this).data('address'));
    $('#company_zip_code').val($(this).data('zip'));
    $('#company_town').val($(this).data('town'));
    $('#company_longitude').val($(this).data('lng'));
    $('#company_latitude').val($(this).data('lat'));
    $('#result').hide().html('');
});

/*********** check all checkboxes on user form when is_admin is checked *************/
$(document).on('click', '#user_is_admin', function(){
    let checked = $(this).prop('checked');
    $('input[type=checkbox]').each(function(){
        if(checked == true) {
            $(this).prop('checked', true);
        } else {
            $(this).prop('checked', false);
        }
    });
});