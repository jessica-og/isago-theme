document.addEventListener("DOMContentLoaded",()=>{

document.querySelectorAll(

".menu-item-has-children"

).forEach(item=>{

item.addEventListener("mouseenter",()=>{

item.classList.add("open");

});

item.addEventListener("mouseleave",()=>{

item.classList.remove("open");

});

});

});