(function(){

var LinkPreview = {

	init: function(){

		var linkPreviews = document.querySelectorAll( 'a.link-preview' );

		if( ! linkPreviews || ! linkPreviews.length ) return;

		for( var linkPreview of linkPreviews ) {
			LinkPreview.refresh(linkPreview);
		}

	},

	refresh: function( linkPreview ) {

		var id = linkPreview.id;
		// TODO: for now, we refresh a link multiple times if there are multpile link-previews for the same link on the page. revisit this in the future, to make sure to only update every ID once for the whole page


		// TODO: refresh the link preview, and if we have new data, add a 'refresh' link next to the link-preview, so the user can refresh the preview html
		//console.log('refresh', id)

	}

};	
	

window.addEventListener( 'load', function(e){
	LinkPreview.init();
});

})();