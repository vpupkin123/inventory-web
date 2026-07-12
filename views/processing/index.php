<div class="card">
    <h2><?= Lang::t('processing.title') ?></h2>
    <br>

    <?php if (isset($_SESSION['processing_error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['processing_error']) ?></div>
        <?php unset($_SESSION['processing_error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['processing_success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['processing_success']) ?></div>
        <?php unset($_SESSION['processing_success']); ?>
    <?php endif; ?>

    <?php if (empty($computers)): ?>
        <p><?= Lang::t('processing.empty_queue') ?></p>
    <?php else: ?>
        <form method="POST" action="/processing/process" id="processing_form">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f4f4f4;">
                        <th style="padding: 10px; border: 1px solid #ddd; width: 30px; text-align: center;">
                            <input type="checkbox" id="select_all">
                        </th>
                        <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('processing.computer_name') ?></th>
                        <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('processing.hardware') ?></th>
                        <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('processing.reported_by') ?></th>
                        <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('processing.comment') ?></th>
                        <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('processing.create_user') ?></th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($computers as $pc): ?>
                    <tr id="row_<?= $pc['id'] ?>">
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                            <input type="checkbox" name="computer_ids[]" value="<?= $pc['id'] ?>" class="row_checkbox">
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <strong><?= htmlspecialchars($pc['computer_name']) ?></strong><br>
                            <small style="color: #666;"><?= htmlspecialchars($pc['ip_address']) ?></small>
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd; font-size: 0.9em;">
                            <strong>CPU:</strong> <?= htmlspecialchars($pc['cpu_name']) ?><br>
                            <strong>RAM:</strong> <?= htmlspecialchars($pc['ram_total_gb']) ?> GB<br>
                            <strong>MB:</strong> <?= htmlspecialchars($pc['motherboard']) ?>
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <?= htmlspecialchars($pc['reported_by']) ?>
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <input type="text" name="comments[<?= $pc['id'] ?>]" value="<?= htmlspecialchars($pc['comment']) ?>" style="width: 100%; padding: 5px;">
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                            <input type="checkbox" name="create_user[<?= $pc['id'] ?>]" value="1" class="create_user_checkbox" data-id="<?= $pc['id'] ?>">
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                            <button type="button" class="btn settings_btn" data-id="<?= $pc['id'] ?>" style="display: none; padding: 5px 10px; font-size: 0.9em; background: #ffc107; color: #000;">
                                <?= Lang::t('processing.settings') ?>
                            </button>
                            <span class="configured_status" data-id="<?= $pc['id'] ?>" style="display: none; color: green; font-weight: bold;">✓</span>
                        </td>
                    </tr>
                    
                    <!-- Hidden inputs for user configuration -->
                    <input type="hidden" name="user_config[<?= $pc['id'] ?>][last_name]" id="cfg_last_<?= $pc['id'] ?>" value="">
                    <input type="hidden" name="user_config[<?= $pc['id'] ?>][first_name]" id="cfg_first_<?= $pc['id'] ?>" value="">
                    <input type="hidden" name="user_config[<?= $pc['id'] ?>][middle_name]" id="cfg_middle_<?= $pc['id'] ?>" value="">
                    <input type="hidden" name="user_config[<?= $pc['id'] ?>][login]" id="cfg_login_<?= $pc['id'] ?>" value="">
                    <input type="hidden" name="user_config[<?= $pc['id'] ?>][is_configured]" id="cfg_status_<?= $pc['id'] ?>" value="0">
                    
                    <?php endforeach; ?>
                </tbody>
            </table>
            <br>
            <button type="submit" class="btn"><?= Lang::t('processing.process_selected') ?></button>
        </form>
    <?php endif; ?>
</div>

<!-- Modal for User Configuration -->
<div id="userModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="background: white; margin: 10% auto; padding: 20px; width: 400px; border-radius: 5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h3 id="modal_title"><?= Lang::t('processing.configure_user') ?></h3>
        <br>
        <div class="form-group">
            <label><?= Lang::t('users.last_name') ?>:</label>
            <input type="text" id="modal_last_name" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
        </div>
        <div class="form-group">
            <label><?= Lang::t('users.first_name') ?>:</label>
            <input type="text" id="modal_first_name" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
        </div>
        <div class="form-group">
            <label><?= Lang::t('users.middle_name') ?>:</label>
            <input type="text" id="modal_middle_name" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
        </div>
        <div class="form-group">
            <label><?= Lang::t('users.login') ?>:</label>
            <input type="text" id="modal_login" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
            <span id="modal_login_status" style="margin-left: 10px; font-size: 0.9em;"></span>
        </div>
        <br>
        <!-- КНОПКИ -->
        <button type="button" id="modal_save" class="btn"><?= Lang::t('processing.save') ?></button>
        <button type="button" id="modal_cancel" class="btn" style="background: #6c757d;"><?= Lang::t('processing.cancel') ?></button>
    </div>
</div>

<script>
let currentConfigId = null;
let checkLoginTimeout;
let lastCheckedLogin = '';

// Transliteration map
const transMap = {
    'А': 'A', 'Б': 'B', 'В': 'V', 'Г': 'G', 'Д': 'D', 'Е': 'E', 'Ё': 'E', 'Ж': 'Zh', 'З': 'Z', 'И': 'I',
    'Й': 'I', 'К': 'K', 'Л': 'L', 'М': 'M', 'Н': 'N', 'О': 'O', 'П': 'P', 'Р': 'R', 'С': 'S', 'Т': 'T',
    'У': 'U', 'Ф': 'F', 'Х': 'Kh', 'Ц': 'Ts', 'Ч': 'Ch', 'Ш': 'Sh', 'Щ': 'Shch', 'Ъ': 'Ie', 'Ы': 'Y', 'Ь': '',
    'Э': 'E', 'Ю': 'Iu', 'Я': 'Ia',
    'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'e', 'ж': 'zh', 'з': 'z', 'и': 'i',
    'й': 'i', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o', 'п': 'p', 'р': 'r', 'с': 's', 'т': 't',
    'у': 'u', 'ф': 'f', 'х': 'kh', 'ц': 'ts', 'ч': 'ch', 'ш': 'sh', 'щ': 'shch', 'ъ': 'ie', 'ы': 'y', 'ь': '',
    'э': 'e', 'ю': 'iu', 'я': 'ia'
};

function transliterate(text) {
    let result = '';
    for (let i = 0; i < text.length; i++) {
        result += transMap[text[i]] !== undefined ? transMap[text[i]] : text[i];
    }
    return result;
}

async function checkLoginAvailability(login) {
    if (!login || login === lastCheckedLogin) {
        return false;
    }
    
    try {
        const response = await fetch(`/api/check-login?login=${encodeURIComponent(login)}`);
        const data = await response.json();
        lastCheckedLogin = login;
        return data.available;
    } catch (error) {
        console.error('Error checking login:', error);
        return false;
    }
}

async function generateLoginFromModal() {
    const ln = document.getElementById('modal_last_name').value.trim();
    const fn = document.getElementById('modal_first_name').value.trim();
    const mn = document.getElementById('modal_middle_name').value.trim();
    
    if (!ln || !fn) {
        document.getElementById('modal_login_status').textContent = '';
        return;
    }
    
    const lnTranslit = transliterate(ln).toLowerCase();
    const fnTranslit = transliterate(fn).toLowerCase();
    const mnTranslit = transliterate(mn).toLowerCase();
    
    // Try different variations until we find an available one
    const variations = [
        lnTranslit + '.' + fnTranslit.charAt(0) + mnTranslit.charAt(0),
        lnTranslit + '.' + fnTranslit.charAt(0) + fnTranslit.charAt(1) + mnTranslit.charAt(0),
        lnTranslit + '.' + fnTranslit.charAt(0) + mnTranslit.charAt(0) + mnTranslit.charAt(1),
        lnTranslit + '.' + fnTranslit.charAt(0) + fnTranslit.charAt(1) + fnTranslit.charAt(2),
    ];
    
    let finalLogin = '';
    for (const variation of variations) {
        const cleanLogin = variation.replace(/[^a-z0-9.]/g, '');
        if (await checkLoginAvailability(cleanLogin)) {
            finalLogin = cleanLogin;
            break;
        }
    }
    
    // If all variations taken, add number
    if (!finalLogin) {
        const baseLogin = variations[0].replace(/[^a-z0-9.]/g, '');
        let counter = 2;
        while (counter < 100) {
            const numberedLogin = baseLogin + counter;
            if (await checkLoginAvailability(numberedLogin)) {
                finalLogin = numberedLogin;
                break;
            }
            counter++;
        }
    }
    
    if (finalLogin) {
        document.getElementById('modal_login').value = finalLogin;
        updateModalLoginStatus(true, 'Login is available', 'green');
    } else {
        updateModalLoginStatus(false, 'Could not generate unique login', 'red');
    }
}

function updateModalLoginStatus(available, message, color) {
    const statusEl = document.getElementById('modal_login_status');
    if (statusEl) {
        statusEl.textContent = message;
        statusEl.style.color = color;
    }
}

async function checkManualLogin() {
    const login = document.getElementById('modal_login').value.trim();
    if (!login) {
        updateModalLoginStatus(false, '', '');
        return;
    }
    
    clearTimeout(checkLoginTimeout);
    checkLoginTimeout = setTimeout(async () => {
        const available = await checkLoginAvailability(login);
        updateModalLoginStatus(available, available ? 'Login is available' : 'Login already exists', available ? 'green' : 'red');
    }, 500);
}

// Show/Hide Settings button based on checkbox
document.querySelectorAll('.create_user_checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        const id = this.dataset.id;
        const btn = document.querySelector(`.settings_btn[data-id="${id}"]`);
        if (this.checked) {
            btn.style.display = 'inline-block';
        } else {
            btn.style.display = 'none';
            document.getElementById(`cfg_status_${id}`).value = '0';
            document.querySelector(`.configured_status[data-id="${id}"]`).style.display = 'none';
            btn.style.background = '#ffc107';
        }
    });
});

// Open Modal
document.querySelectorAll('.settings_btn').forEach(btn => {
    btn.addEventListener('click', function() {
        currentConfigId = this.dataset.id;
        const row = document.getElementById(`row_${currentConfigId}`);
        const reportedBy = row.cells[3].innerText.trim();
        
        const parts = reportedBy.split(/\s+/);
        document.getElementById('modal_last_name').value = document.getElementById(`cfg_last_${currentConfigId}`).value || parts[0] || '';
        document.getElementById('modal_first_name').value = document.getElementById(`cfg_first_${currentConfigId}`).value || parts[1] || '';
        document.getElementById('modal_middle_name').value = document.getElementById(`cfg_middle_${currentConfigId}`).value || parts[2] || '';
        document.getElementById('modal_login').value = document.getElementById(`cfg_login_${currentConfigId}`).value || '';
        
        document.getElementById('userModal').style.display = 'block';
        
        // Auto-generate login on modal open
        generateLoginFromModal();
    });
});

// Close Modal
document.getElementById('modal_cancel').addEventListener('click', () => {
    document.getElementById('userModal').style.display = 'none';
});

// Save Modal
document.getElementById('modal_save').addEventListener('click', () => {
    if (!currentConfigId) return;
    
    const ln = document.getElementById('modal_last_name').value.trim();
    const fn = document.getElementById('modal_first_name').value.trim();
    const login = document.getElementById('modal_login').value.trim();
    
    if (!ln || !fn || !login) {
        alert('<?= Lang::t('change_password.error_empty') ?>');
        return;
    }
    
    document.getElementById(`cfg_last_${currentConfigId}`).value = ln;
    document.getElementById(`cfg_first_${currentConfigId}`).value = fn;
    document.getElementById(`cfg_middle_${currentConfigId}`).value = document.getElementById('modal_middle_name').value.trim();
    document.getElementById(`cfg_login_${currentConfigId}`).value = login;
    document.getElementById(`cfg_status_${currentConfigId}`).value = '1';
    
    const btn = document.querySelector(`.settings_btn[data-id="${currentConfigId}"]`);
    btn.style.background = '#28a745';
    btn.style.color = '#fff';
    document.querySelector(`.configured_status[data-id="${currentConfigId}"]`).style.display = 'inline';
    
    document.getElementById('userModal').style.display = 'none';
});

// Form Validation
document.getElementById('processing_form').addEventListener('submit', function(e) {
    const checkboxes = document.querySelectorAll('.create_user_checkbox:checked');
    for (let cb of checkboxes) {
        const id = cb.dataset.id;
        const isConfigured = document.getElementById(`cfg_status_${id}`).value;
        if (isConfigured !== '1') {
            e.preventDefault();
            alert('<?= Lang::t('processing.user_not_configured') ?>');
            return false;
        }
    }
});

// Select All logic
document.getElementById('select_all').addEventListener('change', function() {
    document.querySelectorAll('.row_checkbox').forEach(cb => cb.checked = this.checked);
});

// Add input listeners for auto-generation
document.getElementById('modal_last_name').addEventListener('input', generateLoginFromModal);
document.getElementById('modal_first_name').addEventListener('input', generateLoginFromModal);
document.getElementById('modal_middle_name').addEventListener('input', generateLoginFromModal);
document.getElementById('modal_login').addEventListener('input', checkManualLogin);
</script>