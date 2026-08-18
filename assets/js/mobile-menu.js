document.addEventListener("DOMContentLoaded",()=>{

const open=document.querySelector(".isago-menu-button");

const close=document.querySelector(".isago-mobile-close");

const menu=document.querySelector(".isago-mobile-menu");

const overlay=document.querySelector(".isago-mobile-overlay");

if(!open||!menu) return;

/*==========================
Open
==========================*/

open.addEventListener("click",()=>{

menu.classList.add("active");

overlay.classList.add("active");

document.body.style.overflow="hidden";

});

/*==========================
Close
==========================*/

function closeMenu(){

menu.classList.remove("active");

overlay.classList.remove("active");

document.body.style.overflow="";

}

close.addEventListener("click",closeMenu);

overlay.addEventListener("click",closeMenu);

/*==========================
ESC
==========================*/

document.addEventListener("keydown",(e)=>{

if(e.key==="Escape"){

closeMenu();

}

});

/*==========================
Accordion
==========================*/
document.querySelectorAll(".mobile-toggle").forEach(button=>{

button.addEventListener("click",()=>{

button.closest(".mobile-item").classList.toggle("open");

});

});

});