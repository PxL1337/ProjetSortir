/*window.addEventListener("load", () => {
    const loader = document.querySelector(".loader");

    loader.classList.add("slide-up-animation");

    loader.addEventListener("transitionend", () =>{
        document.body.removeChild(loader);
    })
})*/
(function() {
    const preloader = document.querySelector("[data-preload]");
    window.addEventListener("load", function () {
        preloader.classList.add("loaded");
        document.body.classList.add("loaded");
    });
    /* NAV - START */
    let current = 0;
    for (let i = 0; i < document.links.length; i++) {
        if (document.links[i].href === document.URL) {
            current = i;
        }
    }
    document.links[current].className = 'current';
    /* NAV - END */
})();