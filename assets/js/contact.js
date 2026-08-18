document.addEventListener("DOMContentLoaded", () => {

/* =========================
FAQ TOGGLE
========================= */
document.querySelectorAll('.faq-item h4').forEach(item => {
item.addEventListener('click', () => {
const parent = item.parentElement;
parent.classList.toggle('active');
});
});

/* =========================
FORM SUBMIT LOADING
========================= */
const form = document.querySelector('.contact-box form');

if(form){
form.addEventListener('submit', function(){
const btn = this.querySelector('button');
if(btn){
btn.innerHTML = "Sending...";
btn.disabled = true;
}
});
}

});