function performSearch() {
    const password = document.getElementById('db_password').value;
    if (!password) {
        alert('Please enter the database password.');
        return;
    }

    // Collect all search criteria
    const searchCriteria = {};
    const searchForm = document.getElementById('search-form');
    const inputs = searchForm.querySelectorAll('input[type="text"]');
    inputs.forEach(input => {
        if (input.value.trim() !== '') {
            const field = input.id.replace('search-', '');
            searchCriteria[field] = input.value.trim();
        }
    });

    // Convert search criteria to URL parameters
    const params = new URLSearchParams(searchCriteria);
    params.append('action', 'search');
    params.append('db_password', password);

    // Perform the search
    fetch(`process.php?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            const resultsDiv = document.getElementById('search-results');
            resultsDiv.innerHTML = '<h3>Search Results</h3>';
            if (data.length === 0) {
                resultsDiv.innerHTML += '<p>No results found.</p>';
            } else {
                data.forEach(record => {
                    resultsDiv.innerHTML += `<p>${record.name} - ${record.email}</p>`;
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while searching.');
        });
}

function saveChanges() {
    const form = document.getElementById('edit-form');
    const formData = new FormData(form);
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });
    data['action'] = 'save';

    if (!data.db_password) {
        alert('Please enter the database password.');
        return;
    }

    fetch('process.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Changes saved successfully!');
            } else {
                alert('Failed to save changes.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving changes.');
        });
}