<?php
session_start();
error_reporting(0);

include "timeout.php";
include "../configurasi/koneksi.php";

$db = isset($db) ? $db : null;

if ($_SESSION['login'] == 1) {
    if (!cek_login()) {
        $_SESSION['login'] = 0;
    }
}

if ($_SESSION['login'] == 0) {
    header('location:logout.php');
    exit;
}

if (empty($_SESSION['username']) && empty($_SESSION['passuser']) && $_SESSION['login'] == 0) {
    header('location:index.php');
    exit;
}

$requestedModule = isset($_GET['module']) ? $_GET['module'] : 'home';
$forceMobileView = isset($_GET['force_mobile']) && $_GET['force_mobile'] === '1';
$userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
$isMobileClient = (bool)preg_match('/Android|iPhone|iPad|iPod|IEMobile|Opera Mini|Mobile/i', $userAgent);

if (!$isMobileClient && !$forceMobileView) {
    $desktopModule = 'home';
    if ($requestedModule === 'kasir' || $requestedModule === 'keranjang') {
        $desktopModule = 'trkasir';
    } elseif ($requestedModule === 'barangmobile') {
        $desktopModule = 'barang';
    } elseif ($requestedModule === 'stok') {
        $desktopModule = 'lapstok';
    } elseif ($requestedModule === 'profil') {
        $desktopModule = 'profil';
    }

    header('location:media_admin.php?module=' . urlencode($desktopModule));
    exit;
}

function mobileHasAccess($sessionKey)
{
    return isset($_SESSION[$sessionKey]) && $_SESSION[$sessionKey] === 'Y';
}

function mobileCanAccessModule($targetModule)
{
    if ($targetModule === 'home' || $targetModule === 'profil') {
        return true;
    }

    if ($targetModule === 'barang' || $targetModule === 'barangmobile') {
        return mobileHasAccess('mbarang');
    }

    if ($targetModule === 'kasir') {
        return mobileHasAccess('tpk');
    }

    if ($targetModule === 'keranjang') {
        return mobileHasAccess('tpk');
    }

    if ($targetModule === 'transaksi') {
        return mobileHasAccess('orders') || mobileHasAccess('tbm') || mobileHasAccess('tbmpbf') || mobileHasAccess('byrkredit') || mobileHasAccess('penjualansebelum') || mobileHasAccess('shiftkerja') || mobileHasAccess('cekdarah') || mobileHasAccess('catatan');
    }

    if ($targetModule === 'stok') {
        return mobileHasAccess('mstok') || mobileHasAccess('stok_kritis') || mobileHasAccess('stokopname') || mobileHasAccess('soharian') || mobileHasAccess('kartustok') || mobileHasAccess('jurnalkas');
    }

    return false;
}

$allowedModules = array('home', 'barangmobile', 'kasir', 'keranjang', 'transaksi', 'stok', 'profil');
$defaultModule = mobileCanAccessModule('kasir') ? 'kasir' : 'home';
$module = isset($_GET['module']) ? $_GET['module'] : $defaultModule;
if (!in_array($module, $allowedModules, true) || !mobileCanAccessModule($module)) {
    $module = $defaultModule;
}

$namaAplikasi = 'SMART INVENTORY';
if ($db instanceof PDO) {
    try {
        $stmtHeader = $db->query("SELECT * FROM setheader LIMIT 1");
        $headerRow = $stmtHeader->fetch(PDO::FETCH_ASSOC);
        if (!empty($headerRow['satu'])) {
            $namaAplikasi = $headerRow['satu'];
        }
    } catch (Exception $e) {
        // Keep fallback app name.
    }
}

function isMobileMenuActive($targetModule, $currentModule)
{
    return $targetModule === $currentModule ? 'active' : '';
}

$bottomMenus = array(
    array('module' => 'home', 'label' => 'Home', 'icon' => 'home-outline', 'enabled' => mobileCanAccessModule('home')),
    array('module' => 'kasir', 'label' => 'Kasir', 'icon' => 'cart-outline', 'enabled' => mobileCanAccessModule('kasir')),
    array('module' => 'keranjang', 'label' => 'Keranjang', 'icon' => 'basket-outline', 'enabled' => mobileCanAccessModule('keranjang')),
    array('module' => 'transaksi', 'label' => 'Transaksi', 'icon' => 'repeat-outline', 'enabled' => mobileCanAccessModule('transaksi')),
    array('module' => 'stok', 'label' => 'Stok', 'icon' => 'cube-outline', 'enabled' => mobileCanAccessModule('stok')),
    array('module' => 'profil', 'label' => 'Profil', 'icon' => 'person-outline', 'enabled' => mobileCanAccessModule('profil')),
);

$sidebarMenus = array(
    array('module' => 'home', 'label' => 'Dashboard Mobile', 'icon' => 'home-outline', 'color' => 'bg-primary', 'enabled' => mobileCanAccessModule('home')),
    array('module' => 'barangmobile', 'label' => 'Item Obat', 'icon' => 'bandage-outline', 'color' => 'bg-success', 'enabled' => mobileCanAccessModule('barangmobile')),
    array('module' => 'kasir', 'label' => 'Kasir Mobile', 'icon' => 'cart-outline', 'color' => 'bg-success', 'enabled' => mobileCanAccessModule('kasir')),
    array('module' => 'keranjang', 'label' => 'Keranjang Aktif', 'icon' => 'basket-outline', 'color' => 'bg-info', 'enabled' => mobileCanAccessModule('keranjang')),
    array('module' => 'transaksi', 'label' => 'Transaksi', 'icon' => 'repeat-outline', 'color' => 'bg-secondary', 'enabled' => mobileCanAccessModule('transaksi')),
    array('module' => 'stok', 'label' => 'Stok', 'icon' => 'cube-outline', 'color' => 'bg-warning', 'enabled' => mobileCanAccessModule('stok')),
    array('module' => 'profil', 'label' => 'Profil', 'icon' => 'person-outline', 'color' => 'bg-primary', 'enabled' => mobileCanAccessModule('profil')),
);
?>
<!doctype html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#0A4ABF">
    <title><?php echo htmlspecialchars($namaAplikasi); ?> - Mobile</title>

    <link rel="stylesheet" href="assets/mobilekit/css/style.css">
    <link rel="stylesheet" href="mobile/mobile.css">
</head>

<body>

<div id="loader">
    <div class="spinner-border text-primary" role="status"></div>
</div>

<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="#" class="headerButton" data-bs-toggle="offcanvas" data-bs-target="#sidebarPanel">
            <ion-icon name="menu-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle"><?php echo htmlspecialchars($namaAplikasi); ?></div>
    <div class="right">
        <a href="media_admin.php?module=home" class="headerButton" title="Desktop">
            <ion-icon name="desktop-outline"></ion-icon>
        </a>
    </div>
</div>

<div id="appCapsule" class="mobile-app-capsule">
    <?php include "mobile/content_mobile.php"; ?>
</div>

<div class="appBottomMenu mobile-bottom-menu">
    <?php foreach ($bottomMenus as $menu) { ?>
        <?php if (!$menu['enabled']) { continue; } ?>
        <a href="media_mobile.php?module=<?php echo urlencode($menu['module']); ?>" class="item <?php echo isMobileMenuActive($menu['module'], $module); ?>">
            <div class="col">
                <ion-icon name="<?php echo htmlspecialchars($menu['icon']); ?>"></ion-icon>
                <strong><?php echo htmlspecialchars($menu['label']); ?></strong>
            </div>
        </a>
    <?php } ?>
</div>

<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarPanel">
    <div class="offcanvas-body">
        <div class="profileBox">
            <div class="image-wrapper">
                <img src="images/mysifalogo.png" alt="Logo" class="imaged rounded">
            </div>
            <div class="in">
                <strong><?php echo htmlspecialchars($_SESSION['namalengkap']); ?></strong>
                <div class="text-muted">
                    <ion-icon name="person-circle-outline"></ion-icon>
                    <?php echo htmlspecialchars($_SESSION['level']); ?>
                </div>
            </div>
            <a href="#" class="close-sidebar-button" data-bs-dismiss="offcanvas">
                <ion-icon name="close"></ion-icon>
            </a>
        </div>

        <ul class="listview flush transparent no-line image-listview mt-2">
            <?php foreach ($sidebarMenus as $menu) { ?>
                <?php if (!$menu['enabled']) { continue; } ?>
                <li>
                    <a href="media_mobile.php?module=<?php echo urlencode($menu['module']); ?>" class="item">
                        <div class="icon-box <?php echo htmlspecialchars($menu['color']); ?>">
                            <ion-icon name="<?php echo htmlspecialchars($menu['icon']); ?>"></ion-icon>
                        </div>
                        <div class="in"><?php echo htmlspecialchars($menu['label']); ?></div>
                    </a>
                </li>
            <?php } ?>
            <li>
                <a href="media_admin.php?module=home" class="item">
                    <div class="icon-box bg-secondary">
                        <ion-icon name="desktop-outline"></ion-icon>
                    </div>
                    <div class="in">Buka Versi Desktop</div>
                </a>
            </li>
            <li>
                <a href="logout.php" class="item">
                    <div class="icon-box bg-danger">
                        <ion-icon name="log-out-outline"></ion-icon>
                    </div>
                    <div class="in">Logout</div>
                </a>
            </li>
        </ul>
    </div>
</div>

<script src="assets/mobilekit/js/lib/bootstrap.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="assets/mobilekit/js/base.js"></script>

</body>
</html>
