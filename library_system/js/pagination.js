let currentPage = 1;
let entriesPerPage = 10;
let totalPages = 1;
let currentData = [];

function initializePagination(data) {
    currentData = data;
    totalPages = Math.ceil(data.length / entriesPerPage);
    updatePageSelect();
    updateEntriesInfo();
    displayCurrentPage();
}

function updatePageSelect() {
    const pageSelect = document.getElementById('pageSelect');
    pageSelect.innerHTML = '';
    for (let i = 1; i <= totalPages; i++) {
        const option = document.createElement('option');
        option.value = i;
        option.textContent = `Page ${i}`;
        option.selected = i === currentPage;
        pageSelect.appendChild(option);
    }
}

function updateEntriesInfo() {
    const startEntry = (currentPage - 1) * entriesPerPage + 1;
    const endEntry = Math.min(currentPage * entriesPerPage, currentData.length);
    const totalEntries = currentData.length;

    document.getElementById('startEntry').textContent = startEntry;
    document.getElementById('endEntry').textContent = endEntry;
    document.getElementById('totalEntries').textContent = totalEntries;

    // Update pagination buttons state
    document.getElementById('previousPage').classList.toggle('disabled', currentPage === 1);
    document.getElementById('nextPage').classList.toggle('disabled', currentPage === totalPages);
}

function changePage(action) {
    if (action === 'prev' && currentPage > 1) {
        currentPage--;
    } else if (action === 'next' && currentPage < totalPages) {
        currentPage++;
    } else if (typeof action === 'string' && !isNaN(action)) {
        currentPage = parseInt(action);
    }
    
    displayCurrentPage();
    updateEntriesInfo();
    document.getElementById('pageSelect').value = currentPage;
}

function changeEntriesPerPage() {
    entriesPerPage = parseInt(document.getElementById('entriesPerPage').value);
    currentPage = 1;
    totalPages = Math.ceil(currentData.length / entriesPerPage);
    updatePageSelect();
    updateEntriesInfo();
    displayCurrentPage();
}

function displayCurrentPage() {
    const start = (currentPage - 1) * entriesPerPage;
    const end = start + entriesPerPage;
    const pageData = currentData.slice(start, end);
    
    // This function should be implemented in your existing code to display the data
    updateTableWithData(pageData);
}

function exportToExcel() {
    // Create a worksheet
    const ws = XLSX.utils.json_to_sheet(currentData);
    
    // Create a workbook
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Library Logs");
    
    // Generate Excel file
    const fileName = `library_logs_${new Date().toISOString().split('T')[0]}.xlsx`;
    XLSX.writeFile(wb, fileName);
} 