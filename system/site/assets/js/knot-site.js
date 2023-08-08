/*
NOTE: this file refreshes link previews in the background,
if they need refreshing, and then shows a reload button
(.link-preview-refresh) next to the link preview; the html
updates after the user clicks the button, so no layout shift
happens on its own.
you can remove this file in your custom themes functions.php via:
remove_script('js/knot-site.js');
*/
(function(){


var LinkPreview = {

	elements: [],

	init: function(){

		var elements = document.querySelectorAll( 'a.link-preview-needs-refresh' );

		if( ! elements || ! elements.length ) return;

		LinkPreview.elements = Array.from(elements);

		setTimeout( LinkPreview.loadNextLink, 1000 );

	},

	loadNextLink: function(){

		if( LinkPreview.elements.length <= 0 ) return;

		var link = LinkPreview.elements.shift(),
			id = link.id.replace('link-', '');

		LinkPreview.refresh( id );

	},

	refresh: function( id ) {
		
		var url = Knot.API.url+'?link_preview='+id;
		fetch( url, {
			mode: 'same-origin'
		}).then( response => response.json() ).then(function(response){

			if( ! response.success ) {
				LinkPreview.loadNextLink();
				return;
			}

			var data = response.data;

			if( ! data.url || ! data.id ) {
				LinkPreview.loadNextLink();
				return;
			}

			if( data.id != id ) {
				LinkPreview.loadNextLink();
				return;
			}

			var linkPreviewElement = document.getElementById('link-'+data.id);

			var previewHash = linkPreviewElement.dataset.previewHash;

			if( previewHash && data.preview_html_hash == previewHash ) {
				LinkPreview.loadNextLink();
				return;
			}

			// NOTE: check if element is below viewport; if so, replace the HTML directly, if not, show a refresh button. We do this so that we don't have a layout shift above or in the viewport.

			var bounding = linkPreviewElement.getBoundingClientRect(),
				linkPreviewElementTopOffset = bounding.top,
				viewportHeight = window.innerHeight,
				refreshInline = false;

			if( linkPreviewElementTopOffset > viewportHeight ) {
				refreshInline = true;
			}

			if( refreshInline ) {

				linkPreviewElement.innerHTML = data.preview_html;
				
			} else {

				var refreshButton = document.createElement('div');
				refreshButton.classList.add('link-preview-refresh');

				refreshButton.addEventListener( 'click', function(e){
					e.preventDefault();
					this.parentNode.innerHTML = data.preview_html;
				});

				linkPreviewElement.appendChild(refreshButton);

			}

			linkPreviewElement.classList.remove('link-preview-needs-refresh');

			LinkPreview.loadNextLink();

		}).catch(function(error){
			console.warn('AJAX error', error); // DEBUG
		});

	}

};


window.addEventListener( 'load', function(e){
	LinkPreview.init();
});

})();