// JavaScript for drag and drop functionality
let dropArea = document.getElementById('drop-area');
let photoInput = document.getElementById('photo');
let previewContainer = document.getElementById('preview-container');
let previewImage = document.getElementById('preview');
let removeButton = document.getElementById('remove-photo');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    dropArea.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, unhighlight, false);
});

function highlight(e) {
    dropArea.classList.add('highlight');
}

function unhighlight(e) {
    dropArea.classList.remove('highlight');
}

dropArea.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
    let dt = e.dataTransfer;
    let files = dt.files;

    let fileInput = document.getElementById('photo');
    fileInput.files = files;
    handleFiles(files);
}

photoInput.addEventListener('change', function() {
    handleFiles(this.files);
});

function handleFiles(files) {
    let file = files[0];
    if (!file.type.startsWith('image/')) { return; }

    let reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onloadend = function() {
        previewImage.src = reader.result;
        previewContainer.style.display = 'block';
    };
}

removeButton.addEventListener('click', function() {
    photoInput.value = '';
    previewImage.src = '';
    previewContainer.style.display = 'none';
});

function confirmDelete() {
    return confirm("Are you sure you want to delete this item?");
}


document.getElementById('searchInput').addEventListener('input', filterTable);
document.getElementById('categoryFilter').addEventListener('change', filterTable);

function filterTable() {
    console.log('Filter function called');
    var searchInput = document.getElementById('searchInput').value.toLowerCase();
    var categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();
    console.log('Search Input:', searchInput);
    console.log('Category Filter:', categoryFilter);

    var rows = document.querySelectorAll('#itemList tr');
    console.log('Total Rows:', rows.length);

    rows.forEach(function(row) {
        var category = row.cells[0].innerText.toLowerCase();
        var nameBrand = row.cells[1].innerText.toLowerCase();
        console.log('Row Category:', category, 'Row Name/Brand:', nameBrand);

        var matchesCategory = categoryFilter === 'all' || category === categoryFilter;
        var matchesSearch = nameBrand.includes(searchInput);
        console.log('Matches Category:', matchesCategory, 'Matches Search:', matchesSearch);

        if (matchesCategory && matchesSearch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

document.getElementById('searchInput').addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
        filterTable();
    }
});
