//-------------------------------------
font_size(750);
function font_size(devwidth){
function _size(){
var deviceWidth = document.documentElement.clientWidth;
if(deviceWidth>=devwidth) deviceWidth=devwidth;
document.documentElement.style.fontSize = deviceWidth / (devwidth/100) + 'px';
}
_size();
window.onresize=function(){
	_size();
};
}
//-------------------------------------