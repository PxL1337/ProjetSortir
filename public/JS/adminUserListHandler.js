const dashboardParentContentSelector = document.getElementById('dashboard-contents-parent');
const allDashboardContentSections = dashboardParentContentSelector.querySelectorAll('.section');
const LOCAL_STORAGE_DASHBOARD_INDEX = 'DASHBOARD_SELECTED_CONTENT_INDEX';
let selectedContentIndex = 0;

document.addEventListener('DOMContentLoaded', function () {
    handleFilteringAndSortingBehaviours();
    handleDashboardNavElements();
});

function handleFilteringAndSortingBehaviours() {
    const filterButton = document.querySelector('#filter-button');
    const sortButton = document.querySelector('#sort-button');
    const filterForm = document.querySelector('#filter-form');
    const userTableContainer = document.querySelector('.table-list-container');
    const loadingElement = document.querySelector('#loading');

    const filterFields = document.querySelectorAll('.filter-field');
    const sortFields = document.querySelectorAll('.sort-field');

    filterButton.addEventListener('click', function (event) {
        event.stopPropagation();

        if (filterForm.classList.contains('visible')) {
            filterForm.classList.remove('visible');
        } else {
            filterForm.classList.add('visible');
            sortFields.forEach(field => field.classList.add('d-none'));
            filterFields.forEach(field => field.classList.remove('d-none'));
        }
    });

    sortButton.addEventListener('click', function (event) {
        event.stopPropagation();

        if (filterForm.classList.contains('visible')) {
            filterForm.classList.remove('visible');
        } else {
            filterForm.classList.add('visible');
            filterFields.forEach(field => field.classList.add('d-none'));
            sortFields.forEach(field => field.classList.remove('d-none'));
        }
    });

    document.addEventListener('click', function () {
        if (filterForm.classList.contains('visible')) {
            filterForm.classList.remove('visible');
        }
    });

    filterForm.addEventListener('click', function (event) {
        event.stopPropagation();
    });

    filterForm.querySelectorAll('select').forEach(function (select) {
        select.addEventListener('change', function () {
            filterForm.submit();
        });
    });

    filterForm.addEventListener('submit', function (event) {
        event.preventDefault();

        loadingElement.classList.remove('d-none');

        const formData = new FormData(filterForm);

        fetch(filterForm.action, {
            method: 'POST', body: formData
        })
            .then(response => new Promise(resolve => setTimeout(() => resolve(response.text()), 2000))) // Ajoute un délai de 2 secondes
            .then(data => {
                userTableContainer.innerHTML = data;
                loadingElement.classList.add('d-none');
            });
    });
}

function handleDashboardNavElements() {
    const dashboardNav_ulElement = document.getElementById('dashboard-buttons');
    const dashboardNav_liElements = dashboardNav_ulElement.querySelectorAll('li');
    const dashboardContentTitle = document.querySelector('.content-title');

    selectContentCategory();
    handleDashboardCategoryButtonsClick();
    console.log("TA MERE LA DINDE");

    function selectContentCategory() {
        let index = localStorage.getItem(LOCAL_STORAGE_DASHBOARD_INDEX) ? localStorage.getItem(LOCAL_STORAGE_DASHBOARD_INDEX) : 0;
        console.log(index);
        console.log(dashboardNav_liElements[index].innerHTML);
        console.log(dashboardNav_liElements[index].querySelector('button').id);
        const navButtonSelector = dashboardNav_liElements[index].querySelector('button');

        selectCategoryButton();

        function selectCategoryButton() {
            const element = document.querySelector('.dbc-selected');
            const styles = window.getComputedStyle(element);

            navButtonSelector.style.color = styles.color;
            navButtonSelector.style.fontWeight = 'bolder';
        }
    }

    function handleDashboardCategoryButtonsClick() {
        dashboardNav_liElements.forEach((li, index) => {
            li.addEventListener("click", () => {
                console.log(li.textContent);
                deselectAllDashboardButtons();
                selectClickedDashboardButton(index, 'dbc-selected');
                changeDashboardContentTitle(li.textContent);
                console.log(index);
                selectedContentIndex = index;
                console.log(selectedContentIndex);
                localStorage.setItem(LOCAL_STORAGE_DASHBOARD_INDEX, selectedContentIndex);

                let selectedSection = allDashboardContentSections[index];
                displayDashboardContent(index, 'content-displayed');
            });
        });
    }

    function selectClickedDashboardButton(indexToShow, className) {
        console.log(indexToShow + "in selectClickedDashboardButton");
        dashboardNav_liElements.forEach((li, index) => {
            if (index === indexToShow) {
                addOrSetClassName(li, className);
            } else {
                console.log(li + " not found in selectClickedDashboardButton");
            }
        });
    }

    function deselectAllDashboardButtons() {
        dashboardNav_liElements.forEach(li => {
            addOrSetClassName(li, 'dcb-not-selected');
        })

        dashboardNav_liElements.forEach( li =>
        {
            li.querySelector('button').style.color = null;
            li.querySelector('button').style.fontWeight = null;
        })
    }

    function changeDashboardContentTitle(newTitle) {
        dashboardContentTitle.innerHTML = "";
        dashboardContentTitle.innerHTML = newTitle;
    }
}

function displayDashboardContent(indexToShow, displayedClassName) {
    console.log(indexToShow + "in displayDashboardContent");

    hideDashboardContents();
    allDashboardContentSections.forEach((section, index) => {
        if (index === indexToShow) {
            section.style.display = 'inline';
        } else {
            console.log(section + " not found in displayDashboardContent");
        }
    });
}

function hideDashboardContents() {
    allDashboardContentSections.forEach(section => {
        section.style.display = 'none';
    });
}

function addOrSetClassName(element, className) {
    if (element.classList.length === 0) {
        element.classList.add(className);
        console.log("Adding class name");
        return;
    }

    element.classList.replace(element.className, className);
    console.log("Setting class name");
}


