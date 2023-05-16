/*
NOTE: this file refreshes link previews in the background,
if they need refreshing, and then shows a reload button
(.link-preview-refresh) next to the link preview; the html
updates after the user clicks the button, so no layout shift
happens on its own.
you can remove this file in your custom themes functions.php via:
remove_script('js/eigenheim.js');
*/
(function(){


var LinkPreview = {

	init: function(){

		var linkPreviews = document.querySelectorAll( 'a.link-preview-needs-refresh' );

		if( ! linkPreviews || ! linkPreviews.length ) return;

		var timeout = 700;
		for( var linkPreview of linkPreviews ) {
			var id = linkPreview.id.replace('link-','');
			setTimeout( LinkPreview.refresh, timeout, id);
			timeout += 300;
		}

	},

	refresh: function( id ) {
		
		var url = Eigenheim.API.url+'?link_preview='+id;
		fetch( url, {
			mode: 'same-origin'
		}).then( response => response.json() ).then(function(response){

			if( ! response.success ) return;

			var data = response.data;

			if( ! data.url || ! data.id ) return;

			if( data.id != id ) return;

			var linkPreview = document.getElementById('link-'+data.id);

			var previewHash = linkPreview.dataset.previewHash;

			if( previewHash && data.preview_html_hash == previewHash ) {
				return;
			}


			var refreshButton = document.createElement('div');
			refreshButton.classList.add('link-preview-refresh');

			refreshButton.addEventListener( 'click', function(e){
				e.preventDefault();
				this.parentNode.innerHTML = data.preview_html;
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