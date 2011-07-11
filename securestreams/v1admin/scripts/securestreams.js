window.addEvent('domready', function(){
		
	if ($('showchangeover')) {
		$('showchangeover').addEvent('click', function(e){
			showOverlay('changeover');
			e.stop();
		});
	}
	
	if ($('showaddover')) {
		$('showaddover').addEvent('click', function(e){
			showOverlay('addover');
			e.stop();
		});
	}	
	
	if ($('addover')) {
		$('addoverbg').addEvent('click', function(e){
			hideOverlay('addover');
			e.stop();
		});
		
		$('changeoverbg').addEvent('click', function(e){
			hideOverlay('changeover');
			e.stop();
		});
		
		if (Browser.Engine.trident4) {
			$('overlay').setStyle('position','absolute');
			fixIe6Fixed('addover');
			window.addEvent('scroll', fixIe6Fixed.bind(this,'addover'));
			fixIe6Fixed('changeover');
			window.addEvent('scroll', fixIe6Fixed.bind(this,'changeover'));
		}
	}
	
});

function showOverlay(whichOverlay) {
	$(whichOverlay).setStyle('display','block');
	$(whichOverlay + 'bg').get('tween', 'opacity',{duration:125}).start(0,0.85).chain(function(){
		$(whichOverlay + 'content').get('tween', 'opacity',{duration:350}).start(0,1);
	});
}

function hideOverlay(whichOverlay) {
	$(whichOverlay + 'content').get('tween', 'opacity',{duration:350}).start(1,0);
	$(whichOverlay + 'bg').get('tween', 'opacity',{duration:125}).start(0.85,0).chain(function(){
		$(whichOverlay).setStyle('display','none');
	});
}

function fixIe6Fixed(whichOverlay) {
	$(whichOverlay).setStyles({
		'top': window.getScroll().y+'px',
		'left': window.getScroll().x+'px',
		'width': '100%',
		'height': '100%'
	});
}