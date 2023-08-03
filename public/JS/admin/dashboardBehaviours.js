const dashboardParentContentSelector = document.getElementById('dashboard-contents-parent');
const allDashboardContentSections = dashboardParentContentSelector.querySelectorAll('.content-section');
const LOCAL_STORAGE_DASHBOARD_INDEX = 'DASHBOARD_SELECTED_CONTENT_INDEX';
const dashboardContentTitle = document.querySelector('.content-title');

initBeforeDocumentLoaded();

document.addEventListener('DOMContentLoaded', function () {
    handleDashboardNavElements();
});

function handleDashboardNavElements() {
    const dashboardNav_ulElement = document.getElementById('dashboard-buttons');
    const dashboardNav_liElements = dashboardNav_ulElement.querySelectorAll('li');

    initDashboard();
    handleDashboardCategoryButtonsClick();

    function initDashboard() {
        let index = getDashboardIndexFromLocalStorage();

        selectContentCategoryOnInit();
        allDashboardContentSections[index].style.display = 'inline';

        function selectContentCategoryOnInit() {
            let index = getDashboardIndexFromLocalStorage();
            const navButtonSelector = dashboardNav_liElements[index].querySelector('button');

            highlightCategoryButton();

            function highlightCategoryButton() {
                const element = document.querySelector('.dbc-selected');
                const styles = window.getComputedStyle(element);

                navButtonSelector.style.color = styles.color;
                navButtonSelector.style.fontWeight = 'bolder';
            }
        }
    }

    function handleDashboardCategoryButtonsClick() {
        dashboardNav_liElements.forEach((li, index) => {
            li.addEventListener("click", () => {

                deselectAllDashboardButtons();
                selectDashboardButton(index, 'dbc-selected');

                changeDashboardContentTitle(li.textContent);

                setDashboardIndexInLocalStorage(index);

                hideAllDashboardContents();
                displayDashboardContent(index);
            });
        });

        function selectDashboardButton(indexToSelect, selectedClassName) {
            dashboardNav_liElements.forEach((li, index) => {
                if (index === indexToSelect) {
                    addOrSetClassNameToElement(li, selectedClassName);
                }
                // else {
                //     console.log(li + " not found in selectDashboardButton");
                // }
            });
        }

        function deselectAllDashboardButtons() {
            dashboardNav_liElements.forEach(li => {
                addOrSetClassNameToElement(li, 'dcb-not-selected');

                li.querySelector('button').style.color = null;
                li.querySelector('button').style.fontWeight = null;
            })
        }
    }

    function displayElementById(id) {
        let element = document.getElementById(id);

        if (element.style.display === 'inline') {
            return;
        }
        element.style.display = 'inline';
    }

    function hideElementById(id) {
        let element = document.getElementById(id);

        if (element.style.display === 'none') {
            return;
        }

        element.style.display = 'none';
    }
}

function initBeforeDocumentLoaded() {
    let title = allDashboardContentSections[getDashboardIndexFromLocalStorage()].id
        .replace("-section", "");
    changeDashboardContentTitle(title.toUpperCase());

    hideAllDashboardContents();
}

function displayDashboardContent(indexToDisplay) {
    allDashboardContentSections.forEach((section, index) => {
        if (index !== indexToDisplay) {
            return;
        }
        section.style.display = 'inline';
    });
}

function hideAllDashboardContents() {
    allDashboardContentSections.forEach(section => {
        section.style.display = 'none';
    });
}

function addOrSetClassNameToElement(element, className) {
    if (element.classList.length === 0) {
        element.classList.add(className);
        // console.log("Adding class name");
        return;
    }

    element.classList.replace(element.className, className);
    // console.log("Setting class name");
}

function changeDashboardContentTitle(newTitle) {
    dashboardContentTitle.innerHTML = "";
    dashboardContentTitle.innerHTML = newTitle;
}

function setDashboardIndexInLocalStorage(index) {
    localStorage.setItem(LOCAL_STORAGE_DASHBOARD_INDEX, index);
}

function getDashboardIndexFromLocalStorage() {
    return localStorage.getItem(LOCAL_STORAGE_DASHBOARD_INDEX) ? localStorage.getItem(LOCAL_STORAGE_DASHBOARD_INDEX) : 0;
}