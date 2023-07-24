window.addEventListener("load", () => {
    const loader = document.querySelector(".loader");

    loader.classList.add("slide-up-animation");

    loader.addEventListener("transitionend", () =>{
        document.body.removeChild(loader);
    })
})