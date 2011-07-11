/*	
CASH Music Flower bootstrap script
more information/downloads available at: http://cashmusic.org/tools/
*/
var flowerUID;
window.addEvent('domready', function(){
	flowerUID = new FlowerCore();
	flowerUID.addEvent('moduleLoad', function(modulename){
		if (modulename == 'soundplayer') {
			(function(){
				flowerUID.getModule('soundplayer').Playlists.each(function(pl, key){
					pl.playCurrentSound = function(){
						var unlockRequest = new Request({method:'post',url:'unlock.php'});
						unlockRequest.addEvent('onSuccess', function () {
							this.makeCurrent();
							if (this.currentSound.sound.paused) {
								this.currentSound.sound.setPosition(0);
								this.currentSound.sound.resume();
							} else {
								this.currentSound.sound.play();
							}
							this.SoundPlayer.fireEvent('play',[this.currentSound.sound.url,this.currentSound.title,this.currentSound.artist]);
						}.bind(this));
						unlockRequest.send('unlock=1');
					};
				});
				flowerUID.getModule('soundplayer').playCurrentSound();
			}).delay(250);
		}
	});
	flowerUID.bootstrap();
});
