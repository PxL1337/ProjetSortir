document.addEventListener('DOMContentLoaded', function() {
    const filterButton = document.querySelector('#filter-button');
    const sortButton = document.querySelector('#sort-button');
    const filterForm = document.querySelector('#filter-form');
    const userTableContainer = document.querySelector('.table-list-container');
    const loadingElement = document.querySelector('#loading');

    const filterFields = document.querySelectorAll('.filter-field');
    const sortFields = document.querySelectorAll('.sort-field');

    filterButton.addEventListener('click', function(event) {
        event.stopPropagation();

        if (filterForm.classList.contains('visible')) {
            filterForm.classList.remove('visible');
        } else {
            filterForm.classList.add('visible');
            sortFields.forEach(field => field.classList.add('d-none'));
            filterFields.forEach(field => field.classList.remove('d-none'));
        }
    });

    sortButton.addEventListener('click', function(event) {
        event.stopPropagation();

        if (filterForm.classList.contains('visible')) {
            filterForm.classList.remove('visible');
        } else {
            filterForm.classList.add('visible');
            filterFields.forEach(field => field.classList.add('d-none'));
            sortFields.forEach(field => field.classList.remove('d-none'));
        }
    });

    document.addEventListener('click', function() {
        if (filterForm.classList.contains('visible')) {
            filterForm.classList.remove('visible');
        }
    });

    filterForm.addEventListener('click', function(event) {
        event.stopPropagation();
    });

    filterForm.querySelectorAll('select').forEach(function(select) {
        select.addEventListener('change', function() {
            filterForm.submit();
        });
    });

    filterForm.addEventListener('submit', function(event) {
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
});
