<div class="card" style="max-width: 600px;">
    <h2><?= $title ?></h2>
    <br>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= $user ? '/users/update' : '/users/store' ?>">
        <?php if ($user): ?>
            <input type="hidden" name="id" value="<?= $user['id'] ?>">
        <?php endif; ?>

        <div class="form-group">
            <label><?= Lang::t('users.last_name') ?>:</label>
            <input type="text" name="last_name" id="last_name" value="<?= htmlspecialchars($_SESSION['form_data']['last_name'] ?? $user['last_name'] ?? '') ?>" required <?= !$user ? 'oninput="generateLogin()"' : '' ?>>
        </div>

        <div class="form-group">
            <label><?= Lang::t('users.first_name') ?>:</label>
            <input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars($_SESSION['form_data']['first_name'] ?? $user['first_name'] ?? '') ?>" required <?= !$user ? 'oninput="generateLogin()"' : '' ?>>
        </div>

        <div class="form-group">
            <label><?= Lang::t('users.middle_name') ?>:</label>
            <input type="text" name="middle_name" id="middle_name" value="<?= htmlspecialchars($_SESSION['form_data']['middle_name'] ?? $user['middle_name'] ?? '') ?>" <?= !$user ? 'oninput="generateLogin()"' : '' ?>>
        </div>

        <div class="form-group">
            <label><?= Lang::t('users.login') ?>:</label>
            <input type="text" name="login" id="login" value="<?= htmlspecialchars($_SESSION['form_data']['login'] ?? $user['login'] ?? '') ?>" <?= $user ? 'disabled style="background: #eee;"' : '' ?> oninput="checkLoginManual()">
            <span id="login_status" style="margin-left: 10px; font-size: 0.9em;"></span>
            <?php if (!$user): ?>
                <small style="color: #666;"><?= Lang::t('users.login_generated') ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label><?= Lang::t('users.password') ?>:</label>
            <input type="password" name="password" <?= !$user ? 'required' : '' ?>>
            <?php if ($user): ?>
                <small style="color: #666;"><?= Lang::t('users.password_hint') ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label><?= Lang::t('users.role') ?>:</label>
            <select name="role" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                <option value="admin" <?= ($_SESSION['form_data']['role'] ?? $user['role'] ?? '') === 'admin' ? 'selected' : '' ?>><?= Lang::t('users.role_admin') ?></option>
                <option value="editor" <?= ($_SESSION['form_data']['role'] ?? $user['role'] ?? '') === 'editor' ? 'selected' : '' ?>><?= Lang::t('users.role_editor') ?></option>
                <option value="viewer" <?= ($_SESSION['form_data']['role'] ?? $user['role'] ?? '') === 'viewer' ? 'selected' : '' ?>><?= Lang::t('users.role_viewer') ?></option>
                <option value="none" <?= ($_SESSION['form_data']['role'] ?? $user['role'] ?? '') === 'none' ? 'selected' : '' ?>><?= Lang::t('users.role_none') ?></option>
            </select>
        </div>

        <button type="submit" class="btn"><?= Lang::t('users.save') ?></button>
        <a href="/users" class="btn" style="background: #6c757d;"><?= Lang::t('common.cancel') ?></a>
    </form>
</div>

<?php if (!$user): ?>
<script>
let checkLoginTimeout;
let lastCheckedLogin = '';

function transliterate(text) {
    const map = {
        'А': 'A', 'Б': 'B', 'В': 'V', 'Г': 'G', 'Д': 'D',
        'Е': 'E', 'Ё': 'E', 'Ж': 'Zh', 'З': 'Z', 'И': 'I',
        'Й': 'I', 'К': 'K', 'Л': 'L', 'М': 'M', 'Н': 'N',
        'О': 'O', 'П': 'P', 'Р': 'R', 'С': 'S', 'Т': 'T',
        'У': 'U', 'Ф': 'F', 'Х': 'Kh', 'Ц': 'Ts', 'Ч': 'Ch',
        'Ш': 'Sh', 'Щ': 'Shch', 'Ъ': 'Ie', 'Ы': 'Y', 'Ь': '',
        'Э': 'E', 'Ю': 'Iu', 'Я': 'Ia',
        'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd',
        'е': 'e', 'ё': 'e', 'ж': 'zh', 'з': 'z', 'и': 'i',
        'й': 'i', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n',
        'о': 'o', 'п': 'p', 'р': 'r', 'с': 's', 'т': 't',
        'у': 'u', 'ф': 'f', 'х': 'kh', 'ц': 'ts', 'ч': 'ch',
        'ш': 'sh', 'щ': 'shch', 'ъ': 'ie', 'ы': 'y', 'ь': '',
        'э': 'e', 'ю': 'iu', 'я': 'ia'
    };
    
    let result = '';
    for (let i = 0; i < text.length; i++) {
        const char = text[i];
        result += map[char] !== undefined ? map[char] : char;
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

async function generateLogin() {
    const lastName = document.getElementById('last_name').value.trim();
    const firstName = document.getElementById('first_name').value.trim();
    const middleName = document.getElementById('middle_name').value.trim();
    
    if (!lastName || !firstName) {
        return;
    }
    
    const lastNameTranslit = transliterate(lastName).toLowerCase();
    const firstNameTranslit = transliterate(firstName).toLowerCase();
    const middleNameTranslit = transliterate(middleName).toLowerCase();
    
    // Try different variations until we find an available one
    const variations = [
        lastNameTranslit + '.' + firstNameTranslit.charAt(0) + middleNameTranslit.charAt(0),
        lastNameTranslit + '.' + firstNameTranslit.charAt(0) + firstNameTranslit.charAt(1) + middleNameTranslit.charAt(0),
        lastNameTranslit + '.' + firstNameTranslit.charAt(0) + middleNameTranslit.charAt(0) + middleNameTranslit.charAt(1),
        lastNameTranslit + '.' + firstNameTranslit.charAt(0) + firstNameTranslit.charAt(1) + firstNameTranslit.charAt(2),
        lastNameTranslit + '.' + firstNameTranslit.charAt(0) + firstNameTranslit.charAt(1) + middleNameTranslit.charAt(0) + middleNameTranslit.charAt(1),
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
        document.getElementById('login').value = finalLogin;
        updateLoginStatus(true, 'Login is available', 'green');
    }
}

async function checkLoginManual() {
    const login = document.getElementById('login').value.trim();
    if (!login) {
        return;
    }
    
    clearTimeout(checkLoginTimeout);
    checkLoginTimeout = setTimeout(async () => {
        const available = await checkLoginAvailability(login);
        updateLoginStatus(available, available ? 'Login is available' : 'Login already exists', available ? 'green' : 'red');
    }, 500);
}

function updateLoginStatus(available, message, color) {
    const statusEl = document.getElementById('login_status');
    statusEl.textContent = message;
    statusEl.style.color = color;
}

// Generate login on page load if fields are filled
window.onload = function() {
    if (document.getElementById('last_name').value && document.getElementById('first_name').value) {
        generateLogin();
    }
};
</script>
<?php endif; ?>