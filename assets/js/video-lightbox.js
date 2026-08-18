document.addEventListener("DOMContentLoaded", () => {

    console.log("Video Lightbox Ready");

    const modal = document.getElementById("isago-video-lightbox");
    const player = document.getElementById("isago-video-player");
    const close = document.querySelector(".isago-video-close");

    console.log(modal);
    console.log(player);
    console.log(close);

    if (!modal || !player || !close) {
        console.error("Lightbox elements missing.");
        return;
    }

    document.querySelectorAll(".homepage-video-play").forEach(button => {

        button.addEventListener("click", function (e) {

            e.preventDefault();
            e.stopPropagation();

            console.log("Play clicked");

            const url = this.dataset.video;

            console.log(url);

            if (!url) {
                console.error("No video URL.");
                return;
            }

            const match = url.match(
                /(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([^?&]+)/i
            );

            if (!match) {
                console.error("Invalid YouTube URL");
                return;
            }

            const id = match[1];

            player.innerHTML =
                `<iframe
                    src="https://www.youtube.com/embed/${id}?autoplay=1"
                    allowfullscreen
                    allow="autoplay"
                    frameborder="0">
                </iframe>`;

            modal.classList.add("active");

document.body.classList.add("isago-video-open");

        });

    });

  function closeModal(){

modal.classList.remove("active");

document.body.classList.remove("isago-video-open");

player.innerHTML="";

}

    close.addEventListener("click", closeModal);

    modal.querySelector(".isago-video-backdrop")
         .addEventListener("click", closeModal);

});