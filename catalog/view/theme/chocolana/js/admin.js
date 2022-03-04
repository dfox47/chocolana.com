var $ = jQuery;



$(window).on('load', function () {
	var a = {"Ё":"YO","Й":"I","Ц":"TS","У":"U","К":"K","Е":"E","Н":"N","Г":"G","Ш":"SH","Щ":"SCH","З":"Z","Х":"H","Ъ":"'","ё":"yo","й":"i","ц":"ts","у":"u","к":"k","е":"e","н":"n","г":"g","ш":"sh","щ":"sch","з":"z","х":"h","ъ":"'","Ф":"F","Ы":"I","В":"V","А":"a","П":"P","Р":"R","О":"O","Л":"L","Д":"D","Ж":"ZH","Э":"E","ф":"f","ы":"i","в":"v","а":"a","п":"p","р":"r","о":"o","л":"l","д":"d","ж":"zh","э":"e","Я":"Ya","Ч":"CH","С":"S","М":"M","И":"I","Т":"T","Ь":"'","Б":"B","Ю":"YU","я":"ya","ч":"ch","с":"s","м":"m","и":"i","т":"t","ь":"'","б":"b","ю":"yu"};



	// category
	if ( $('#form-category').length ) {
		if ( $('#input-name1').length ) {
			$('#input-name1, #input-name2, #input-name3').bind('change keyup input click', function() {
				seo_url__for_category();
			});

			seo_url__for_category();
		}
	}
	// product
	else if ( $('#form-product').length ) {
		if ( $('#input-name1').length ) {
			$('#input-name1, #input-name2, #input-name3').bind('change keyup input click', function() {
				seo_url__for_product();
			});

			seo_url__for_product();
		}
	}



	// copy option name & value to all languages
	if ( $('input[name="option_description[2][name]"]').length ) {
		$('input[name="option_description[2][name]"]').bind('change keyup input click', function() {
			option_name_to_all_languages();
		});

		option_name_to_all_languages();



		$('input[name*="[option_value_description][2]"]').bind('change keyup input click', function() {
			option_value_to_all_languages();
		});

		option_value_to_all_languages();
	}



	// copy title to meta
	if ( $('#input-name2').length ) {
		$('#input-name2').bind('change keyup input click', function() {
			copy_title_to_meta();
		});

		copy_title_to_meta();
	}



	// SEO url from SKU [START]
	function seo_from_sku() {
		var sku = $('#input-sku').val();

		$('input[name="product_seo_url[0][2]"]').val(sku);
	}

	if ( $('#input-sku').length ) {
		$('#input-sku').bind('change keyup input click', function() {
			seo_from_sku();
		});
	}

	// seo_from_sku();
	// SEO url from SKU [END]



	function copy_title_to_meta() {
		var title1 = $('#input-name1').val();
		var title2 = $('#input-name2').val();
		var title3 = $('#input-name3').val();

		$('#input-meta-title1').val(title1);
		$('#input-meta-title2').val(title2);
		$('#input-meta-title3').val(title3);
	}

	function option_name_to_all_languages() {
		var option_name = $('input[name="option_description[2][name]"]').val();

		$('input[name="option_description[1][name]"]').val(option_name);
		$('input[name="option_description[3][name]"]').val(option_name);
	}

	function option_value_to_all_languages() {
		$('input[name*="[option_value_description][2]"]').each(function () {
			var option_value = $(this).val();

			$(this).parent().next().find('input').val(option_value);
			$(this).parent().next().next().find('input').val(option_value);
		});
	}

	function seo_url__for_category() {
		// for categories
		$('input[name="category_seo_url[0][1]"]').val('en-' + transliterate( $('#input-name1').val().replace(/ /g, '')) );
		$('input[name="category_seo_url[0][2]"]').val(transliterate( $('#input-name2').val().replace(/ /g, '')) );
		$('input[name="category_seo_url[0][3]"]').val('bg-' + transliterate( $('#input-name3').val().replace(/ /g, '')) );
	}

	function seo_url__for_product() {
		// for products
		$('input[name="product_seo_url[0][1]"]').val('en-' + transliterate( $('#input-name1').val().replace(/ /g, '')) );
		$('input[name="product_seo_url[0][2]"]').val('ru-' + transliterate( $('#input-name2').val().replace(/ /g, '')) );
		$('input[name="product_seo_url[0][3]"]').val('bg-' + transliterate( $('#input-name3').val().replace(/ /g, '')) );
	}



	// from cyrillic to latin
	function transliterate(word) {
		return word.split('').map(function (char) {
			return a[char] || char;
		}).join('').toLowerCase();
	}
});


