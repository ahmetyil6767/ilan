$(document).ready(function() {
	
	// enable fileuploader plugin
	$('input#one_cikarilan_gorsel').fileuploader({
        changeInput: '<div class="fileuploader-input">' +
					      '<div class="fileuploader-input-inner">' +
						      '<img src="js/fileuploader/icon.png">' +
							  '<h3 class="fileuploader-input-caption"><span>Bir görsel sürükle bırak</span></h3>' +
							  '<p>ya da</p>' +
							  '<div class="fileuploader-input-button"><span>Görsel Seç</span></div>' +
						  '</div>' +
					  '</div>',
        theme: 'dragdrop',
		
		
			limit: 1,
        
		captions: {
            feedback: 'İyi görünüyor!',
            feedback2: 'İyi görünüyor!',
            drop: 'İyi görünüyor!'
        },
	});

	// enable fileuploader plugin
	$('input#limon_galeri').fileuploader({
        changeInput: '<div class="fileuploader-input">' +
					      '<div class="fileuploader-input-inner">' +
							  '<h3 class="fileuploader-input-caption"><span>Galeri görsellerini sürükle bırak</span></h3>' +
							  '<p>ya da</p>' +
							  '<div class="fileuploader-input-button"><span>Görsel Seç</span></div>' +
						  '</div>' +
					  '</div>',
        theme: 'dragdrop',       
		captions: {
            feedback: 'İyi görünüyor!',
            feedback2: 'İyi görünüyor!',
            drop: 'İyi görünüyor!'
        },
	});
	
});