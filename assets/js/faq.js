document.querySelectorAll(".faq-item h4").forEach(header => {

header.addEventListener("click", function () {

const item = this.parentElement;
const content = item.querySelector("p");

/* CLOSE OTHERS */
document.querySelectorAll(".faq-item").forEach(i => {
if(i !== item){
i.classList.remove("active");
i.querySelector("p").style.maxHeight = null;
}
});

/* TOGGLE */
item.classList.toggle("active");

if(item.classList.contains("active")){
content.style.maxHeight = content.scrollHeight + "px";
}else{
content.style.maxHeight = null;
}

});

});