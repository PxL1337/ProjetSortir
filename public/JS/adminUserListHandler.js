document.addEventListener('DOMContentLoaded', function () {
    const dashboardParentContentSelector = document.getElementById('dashboard-contents-parent');
    const allDashboardContentSections = dashboardParentContentSelector.querySelectorAll('.section');


    handleFilteringAndSortingBehaviours();
    handleDashboardNavElements();

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
                method: 'POST',
                body: formData
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

        function handleDashboardCategoryButtonsClick() {
            dashboardNav_liElements.forEach((li, index) => {
                li.addEventListener("click", () => {
                    console.log(li.textContent);
                    unselectAllDashboardButtons();
                    selectClickedDashboardButton(li, 'dbc-selected');
                    changeDashboardContentTitle(li.textContent);
                    console.log(index);
                    console.log(allDashboardContentSections[index].textContent);

                    let selectedSection = allDashboardContentSections[index];
                    displayDashboardContent(
                        index,
                        'content-displayed');
                });
            });
        }

        function selectClickedDashboardButton(li, className) {
            addOrSetClassName(li, className);
        }

        function unselectAllDashboardButtons() {
            dashboardNav_liElements.forEach(li => {
                addOrSetClassName(li, 'dcb-not-selected');
            })
        }

        function changeDashboardContentTitle(newTitle) {
            dashboardContentTitle.innerHTML = "";
            dashboardContentTitle.innerHTML = newTitle;
        }

        handleDashboardCategoryButtonsClick();
    }

    function displayDashboardContent(indexToShow, displayedClassName) {
        hideDashboardContents();
        allDashboardContentSections.forEach((section, index) =>
        {
           if (index === indexToShow) {
               section.style.display = 'inline';
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

        element.className = className;
        console.log("Setting class name");
    }
});


