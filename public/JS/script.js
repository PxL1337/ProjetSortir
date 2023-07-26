/*window.addEventListener("load", () => {
    const loader = document.querySelector(".loader");

    loader.classList.add("slide-up-animation");

    loader.addEventListener("transitionend", () =>{
        document.body.removeChild(loader);
    })
})*/
const preloader = document.querySelector("[data-preload]");
window.addEventListener("load", function(){
    preloader.classList.add("loaded");
    document.body.classList.add("loaded");
});