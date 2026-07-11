<!DOCTYPE html>
<html lang="<?= Lang::getLocale() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Web</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; min-height: 100vh; display: flex; flex-direction: column; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; flex: 1; }
        .navbar { 
            background: #333; 
            color: white; 
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a { color: white; text-decoration: none; margin-right: 20px; }
        .navbar .right { 
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .lang-switcher {
            position: relative;
            display: inline-block;
        }
        .lang-switcher select {
            background: #555;
            color: white;
            border: 1px solid #777;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 14px;
        }
        .lang-switcher select:hover {
            background: #666;
        }
        .card { background: white; border-radius: 5px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer; text-decoration: none; }
        .btn:hover { background: #0056b3; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 3px; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        footer { 
            text-align: center; 
            padding: 20px; 
            margin-top: 40px; 
            border-top: 1px solid #ddd; 
            color: #666; 
            font-size: 0.9em;
            background: #f9f9f9;
        }
        footer a { color: #007bff; text-decoration: none; }
        footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <?php if (Auth::check()): ?>
    <div class="navbar">
        <div class="left">
            <a href="/dashboard"><?= Lang::t('nav.dashboard') ?></a>
            <a href="/reports"><?= Lang::t('nav.reports') ?></a>
            <a href="/processing"><?= Lang::t('nav.processing') ?></a>
            <a href="/computers"><?= Lang::t('nav.computers') ?></a>
            <a href="/transfers"><?= Lang::t('nav.history') ?></a>
            <a href="/users"><?= Lang::t('nav.users') ?></a>
        </div>
        <div class="right">
            <form method="POST" action="/lang" style="display: inline;">
                <div class="lang-switcher">
                    <select name="locale" onchange="this.form.submit()">
                        <option value="en" <?= Lang::is('en') ? 'selected' : '' ?>>🇬🇧 EN</option>
                        <option value="ru" <?= Lang::is('ru') ? 'selected' : '' ?>>🇷🇺 RU</option>
                    </select>
                </div>
            </form>

            <?php $currentUser = Auth::user(); ?>
            <span>
                <?= Lang::t('nav.logged_in_as') ?>: 
                <strong><?= htmlspecialchars($currentUser['name'] ?? '') ?></strong>
                (<?= htmlspecialchars($currentUser['role'] ?? '') ?>)
            </span>
            <a href="/logout"><?= Lang::t('nav.logout') ?></a>
        </div>
    </div>
    <?php endif; ?>

    <div class="container">
        <?= $content ?>
    </div>
    
    <footer>
        <p>
            <?= Lang::t('footer.developed_by') ?> 
            <a href="https://github.com/vpupkin123" target="_blank">vpupkin123</a>
            | <?= Lang::t('footer.source_code') ?> 
            <a href="https://github.com/vpupkin123/inventory-web" target="_blank">GitHub</a>
        </p>
    </footer>
</body>
</html>