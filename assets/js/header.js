document.addEventListener(

'DOMContentLoaded',

()=>{

const header=document.querySelector(

'.isago-header'

);

window.addEventListener(

'scroll',

()=>{

if(window.scrollY>60){

header.classList.add(

'scrolled'

);

}else{

header.classList.remove(

'scrolled'

);

}

});

});