<?php
/** @var string $page_title */
/** @var string $content */
/** @var string|null $active_file */
if (!isset($active_file)) {
    $active_file = basename($_SERVER['PHP_SELF']);
}
require_once __DIR__ . '/_icons.php';
$menu = require __DIR__ . '/_menu.php';
$flash = admin_flash_get();
$siteName = cms_get('site', 'site_name', 'FWD AGENT');
$userEmail = $_SESSION['admin_user'] ?? '';
$userInitial = $userEmail !== '' ? strtoupper(mb_substr($userEmail, 0, 1, 'UTF-8')) : '?';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= admin_h($page_title) ?> — CMS</title>
    <link rel="stylesheet" href="<?= admin_h('../assets/css/admin.css') ?>?v=3">
</head>
<body class="admin-body" data-admin-csrf="<?= admin_h(admin_csrf_token()) ?>" data-admin-upload-url="<?= admin_h(admin_url('media-upload.php')) ?>">
    <div class="admin-sidebar-overlay" id="sidebar-overlay" aria-hidden="true"></div>
    <div class="admin-shell">
        <aside class="admin-sidebar" id="admin-sidebar">
            <div class="admin-sidebar__brand">
                <a href="<?= admin_h(admin_url('index.php')) ?>">
                    <span class="admin-sidebar__logo">FWD</span>
                    <span>
                        <span class="admin-sidebar__title">Content Studio</span>
                        <span class="admin-sidebar__subtitle"><?= admin_h($siteName) ?></span>
                    </span>
                </a>
            </div>
            <nav class="admin-nav">
                <?php foreach ($menu as $group): ?>
                <div class="admin-nav__section">
                    <p class="admin-nav__label"><?= admin_h($group['section']) ?></p>
                    <ul>
                        <?php foreach ($group['items'] as $item):
                            $href = admin_url($item['file']);
                            $icon = $item['icon'] ?? 'page';
                            $isActive = ($active_file === $item['file'])
                                || (str_contains($item['file'], 'page-edit.php') && ($active_file === 'page-edit.php') && str_contains($_SERVER['REQUEST_URI'] ?? '', $item['file']));
                        ?>
                        <li>
                            <a href="<?= admin_h($href) ?>" class="admin-nav__link<?= $isActive ? ' is-active' : '' ?>">
                                <span class="admin-nav__icon"><?= admin_icon($icon) ?></span>
                                <span><?= admin_h($item['label']) ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
            </nav>
            <div class="admin-sidebar__footer">
                <a href="<?= admin_h('../index.php') ?>" target="_blank" rel="noopener">
                    <span class="admin-nav__icon"><?= admin_icon('external') ?></span>
                    ดูเว็บไซต์
                </a>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <button type="button" class="admin-topbar__toggle" id="sidebar-toggle" aria-label="เปิดเมนู">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="admin-topbar__title-wrap">
                    <p class="admin-topbar__eyebrow">FWD CMS</p>
                    <h1 class="admin-topbar__title"><?= admin_h($page_title) ?></h1>
                </div>
                <div class="admin-topbar__actions">
                    <div class="admin-user-chip" title="<?= admin_h($userEmail) ?>">
                        <span class="admin-user-chip__avatar"><?= admin_h($userInitial) ?></span>
                        <span class="admin-user-chip__email"><?= admin_h($userEmail) ?></span>
                    </div>
                </div>
            </header>

            <?php if ($flash): ?>
            <div class="admin-alert-wrap">
                <div class="admin-alert admin-alert--<?= admin_h($flash['type']) ?>" id="admin-flash" role="alert">
                    <?= admin_h($flash['message']) ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="admin-content">
                <?= $content ?>
            </div>
        </div>
    </div>
    <script src="<?= admin_h('../assets/js/admin.js') ?>?v=5"></script>
</body>
</html>
