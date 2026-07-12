<div class="card">
    <h2><?= Lang::t('report.batch_upload') ?></h2>
    <p style="color: #666; margin-top: 10px;"><?= Lang::t('report.batch_description') ?></p>
    <br>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="/reports/upload" enctype="multipart/form-data" id="batch_upload_form">
        <div style="display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap;">
            <label class="btn" style="background: #007bff; cursor: pointer; display: inline-block;">
                <?= Lang::t('report.select_files') ?>
                <input type="file" name="json_files[]" accept=".json" multiple style="display: none;" id="file_input">
            </label>
            
            <label class="btn" style="background: #28a745; cursor: pointer; display: inline-block;">
                <?= Lang::t('report.select_folder') ?>
                <input type="file" name="json_folder[]" accept=".json" webkitdirectory directory multiple style="display: none;" id="folder_input">
            </label>
        </div>

        <div id="file_list_container" style="display: none;">
            <h3><?= Lang::t('report.selected_files') ?>: <span id="file_count">0</span></h3>
            <br>
            <div id="file_list" style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; border-radius: 5px; padding: 10px; background: #f9f9f9;"></div>
            <br>
            <button type="submit" class="btn" id="submit_btn"><?= Lang::t('report.upload_all') ?></button>
            <a href="/reports" class="btn" style="background: #6c757d;"><?= Lang::t('common.cancel') ?></a>
        </div>
    </form>
</div>

<script>
let selectedFiles = [];

function formatBytes(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function renderFileList() {
    const container = document.getElementById('file_list');
    const countEl = document.getElementById('file_count');
    const listContainer = document.getElementById('file_list_container');
    
    if (selectedFiles.length === 0) {
        listContainer.style.display = 'none';
        return;
    }
    
    listContainer.style.display = 'block';
    countEl.textContent = selectedFiles.length;
    
    container.innerHTML = '';
    selectedFiles.forEach((file, index) => {
        const row = document.createElement('div');
        row.style.cssText = 'display: flex; justify-content: space-between; align-items: center; padding: 8px; border-bottom: 1px solid #eee; background: white; border-radius: 3px; margin-bottom: 5px;';
        
        row.innerHTML = `
            <div style="flex: 1; overflow: hidden;">
                <strong style="color: #333;">${file.name}</strong>
                <br><small style="color: #666;">${formatBytes(file.size)}</small>
            </div>
            <button type="button" onclick="removeFile(${index})" style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; margin-left: 10px;">
                ${'<?= Lang::t('report.remove') ?>'}
            </button>
        `;
        container.appendChild(row);
    });
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    renderFileList();
}

function handleFiles(files) {
    for (let file of files) {
        // Only add JSON files
        if (file.name.toLowerCase().endsWith('.json')) {
            // Avoid duplicates in the list
            const exists = selectedFiles.some(f => f.name === file.name && f.size === file.size);
            if (!exists) {
                selectedFiles.push(file);
            }
        }
    }
    renderFileList();
}

document.getElementById('file_input').addEventListener('change', function(e) {
    handleFiles(e.target.files);
    this.value = ''; // Reset to allow selecting same files again
});

document.getElementById('folder_input').addEventListener('change', function(e) {
    handleFiles(e.target.files);
    this.value = '';
});

// Intercept form submission to add files to FormData
document.getElementById('batch_upload_form').addEventListener('submit', function(e) {
    if (selectedFiles.length === 0) {
        e.preventDefault();
        alert('<?= Lang::t('report.no_files_selected') ?>');
        return false;
    }
    
    // Show uploading state
    const btn = document.getElementById('submit_btn');
    btn.disabled = true;
    btn.textContent = '<?= Lang::t('report.uploading') ?>';
    
    // Create FormData and add all files
    const formData = new FormData(this);
    formData.delete('json_files[]');
    formData.delete('json_folder[]');
    
    selectedFiles.forEach(file => {
        formData.append('json_files[]', file);
    });
    
    // Submit via fetch
    e.preventDefault();
    fetch('/reports/upload', {
        method: 'POST',
        body: formData
    }).then(response => {
        if (response.redirected) {
            window.location.href = response.url;
        } else {
            window.location.href = '/reports';
        }
    }).catch(err => {
        console.error(err);
        alert('Upload failed');
        btn.disabled = false;
        btn.textContent = '<?= Lang::t('report.upload_all') ?>';
    });
});
</script>