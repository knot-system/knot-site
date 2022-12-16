(function(){

var LinkPreview = {

	init: function(){

		var linkPreviews = document.querySelectorAll( 'a.link-preview-needs-refresh' );

		if( ! linkPreviews || ! linkPreviews.length ) return;

		var timeout = 1000;
		for( var linkPreview of linkPreviews ) {
			var id = linkPreview.id.replace('link-','');
			setTimeout( LinkPreview.refresh, timeout, id);
			timeout += 700;
		}

	},

	refresh: function( id ) {
		
		var url = Eigenheim.API.url+'?link_preview='+id;
		fetch( url, {
			mode: 'same-origin'
		}).then( response => response.json() ).then(function(response){

			if( ! response.success ) return;

			var data = response.data,
				html = response.html;

			if( ! data.url || ! data.id ) return;

			if( data.id != id ) return;

			// TODO: make sure to only show refresh button if content changed

			var linkPreview = document.getElementById('link-'+data.id);

			var refreshButton = document.createElement('div');
			refreshButton.classList.add('link-preview-refresh');

			refreshButton.addEventListener( 'click', function(e){
				e.preventDefault();
				this.parentNode.innerHTML = html;
			});

			linkPreview.appendChild(refreshButton);
			linkPreview.classList.remove('link-preview-needs-refresh');

		}).catch(function(error){
			console.warn('AJAX error', error); // DEBUG
		});

	}

};	
	

window.addEventListener( 'load', function(e){
	LinkPreview.init();
});

})();