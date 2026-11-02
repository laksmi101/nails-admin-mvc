<?php
session_start();
require_once __DIR__ . '/controllers/AdminControllers.php';

$controller = new AdminController();
$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'login':
        $controller->login();
        break;
    case 'logout':
        $controller->logout();
        break;
    case 'user-login': // Route login untuk Pelanggan (User)
        $controller->userLogin();
        break;
    case 'user-logout': // Route logout untuk Pelanggan (User)
        $controller->userLogout();
        break;
    case 'create':
        $controller->create();
        break;
    case 'edit':
        $controller->edit();
        break;
    case 'delete':
        $controller->delete();
        break;
    case 'manage':
        $controller->manage();
        break;
    case 'order':
        $controller->order();
        break;
    case 'order-create':
        $controller->createOrder();
        break;
    case 'payment':
        $controller->payment();
        break;
    case 'check-status':
        $controller->checkStatus();
        break;
    case 'owner-edit':
        $controller->editOwnerProfile();
        break;
    case 'owner-save':
        $controller->saveOwnerProfile();
        break;
    case 'content-edit':
        $controller->editContent();
        break;
    case 'content-save':
        $controller->saveContent();
        break;
    case 'content-delete':
        $controller->deleteContent();
        break;
    case 'update-status': // Tambahan route untuk ACC Pembayaran Admin
        $controller->updateStatus();
        break;
    // --- ROUTE BARU UNTUK KELOLA NAIL ARTIST ---
    case 'artist-edit':
        $controller->editArtist();
        break;
    case 'artist-save':
        $controller->saveArtist();
        break;
    case 'artist-delete':
        $controller->deleteArtist();
        break;
    // -------------------------------------------
    default:
        $controller->index();
        break;
}
?>