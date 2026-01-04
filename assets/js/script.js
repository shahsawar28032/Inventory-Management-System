// Inventory Management System JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Confirm before delete
    var deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });

    // Form validation
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Real-time search filtering
    var searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            var filter = this.value.toLowerCase();
            var rows = document.querySelectorAll('table tbody tr');
            
            rows.forEach(function(row) {
                var text = row.textContent.toLowerCase();
                row.style.display = text.indexOf(filter) > -1 ? '' : 'none';
            });
        });
    }

    // Stock level indicators
    var stockCells = document.querySelectorAll('.stock-level');
    stockCells.forEach(function(cell) {
        var stock = parseInt(cell.textContent);
        var minStock = parseInt(cell.dataset.minStock) || 5;
        
        if (stock === 0) {
            cell.classList.add('text-danger', 'fw-bold');
        } else if (stock <= minStock) {
            cell.classList.add('text-warning', 'fw-bold');
        }
    });

    // Price formatting
    var priceInputs = document.querySelectorAll('input[type="number"].price-input');
    priceInputs.forEach(function(input) {
        input.addEventListener('blur', function() {
            var value = parseFloat(this.value);
            if (!isNaN(value)) {
                this.value = value.toFixed(2);
            }
        });
    });

    // Auto-generate SKU
    var productNameInput = document.getElementById('name');
    var skuInput = document.getElementById('sku');
    if (productNameInput && skuInput && !skuInput.value) {
        productNameInput.addEventListener('blur', function() {
            if (!skuInput.value) {
                var name = this.value.trim();
                if (name) {
                    var sku = name.substring(0, 3).toUpperCase() + 
                             Math.floor(1000 + Math.random() * 9000);
                    skuInput.value = sku;
                }
            }
        });
    }

    // Quantity validation
    var quantityInputs = document.querySelectorAll('input[type="number"].quantity-input');
    quantityInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            if (this.value < 0) {
                this.value = 0;
            }
        });
    });

    // Print functionality
    var printButtons = document.querySelectorAll('.btn-print');
    printButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            window.print();
        });
    });

    // Dashboard charts (if implemented)
    initializeCharts();
});

function initializeCharts() {
    // Simple chart implementation using vanilla JS
    // This is a placeholder for actual chart implementation
    const chartContainers = document.querySelectorAll('.chart-container');
    
    chartContainers.forEach(container => {
        const canvas = container.querySelector('canvas');
        if (canvas) {
            drawSimpleChart(canvas);
        }
    });
}

function drawSimpleChart(canvas) {
    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;
    
    // Simple bar chart drawing
    ctx.fillStyle = '#3498db';
    ctx.fillRect(50, height - 100, 60, 100);
    ctx.fillRect(130, height - 150, 60, 150);
    ctx.fillRect(210, height - 80, 60, 80);
    ctx.fillRect(290, height - 120, 60, 120);
}

// Export data functionality
function exportTableToCSV(tableId, filename) {
    var table = document.getElementById(tableId);
    var rows = table.querySelectorAll('tr');
    var csv = [];
    
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll('td, th');
        
        for (var j = 0; j < cols.length; j++) {
            row.push('"' + cols[j].innerText.replace(/"/g, '""') + '"');
        }
        
        csv.push(row.join(','));
    }
    
    // Download CSV file
    var csvFile = new Blob([csv.join('\n')], {type: 'text/csv'});
    var downloadLink = document.createElement('a');
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}