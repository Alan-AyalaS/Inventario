<?php
/**
* @author evilnapsis
**/

define("ROOT", dirname(__FILE__));

$debug= false;
if($debug){
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
}

include "core/autoload.php";

ob_start();
session_start();
Core::$root="";

// si quieres que se muestre las consultas SQL debes descomentar la siguiente linea
// Core::$debug_sql = true;

$lb = new Lb();
$lb->start();

$iniciador = new Core();

// Inicializar variables para evitar warnings
$products = [];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$total_pages = 1;

// Añadir el controlador de eliminación múltiple
if(isset($_GET["view"]) && $_GET["view"]=="deleteproducts"){
    require_once("core/app/controller/DeleteProductsController.php");
    $controller = new DeleteProductsController();
    $controller->index();
    exit;
}

?>
<style>
    .product-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .product-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-bottom: 1px solid #eee;
    }

    .product-info {
        padding: 15px;
    }

    .product-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }

    .product-category {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 5px;
    }

    .product-price {
        font-size: 1.2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 10px;
    }

    .product-stock {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .stock-high {
        background-color: #d4edda;
        color: #155724;
    }

    .stock-medium {
        background-color: #fff3cd;
        color: #856404;
    }

    .stock-low {
        background-color: #f8d7da;
        color: #721c24;
    }

    .product-actions {
        padding: 10px 15px;
        background: #f8f9fa;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-action {
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .btn-edit {
        background-color: #3498db;
        color: white;
        border: none;
    }

    .btn-edit:hover {
        background-color: #2980b9;
    }

    .btn-delete {
        background-color: #e74c3c;
        color: white;
        border: none;
    }

    .btn-delete:hover {
        background-color: #c0392b;
    }

    .add-product-btn {
        background-color: #2ecc71;
        color: white;
        padding: 10px 20px;
        border-radius: 4px;
        border: none;
        font-weight: 600;
        transition: background-color 0.3s ease;
    }

    .add-product-btn:hover {
        background-color: #27ae60;
    }

    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 30px;
    }

    .pagination .page-link {
        padding: 8px 16px;
        margin: 0 4px;
        border: 1px solid #ddd;
        border-radius: 4px;
        color: #3498db;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .pagination .page-link:hover {
        background-color: #f8f9fa;
        border-color: #3498db;
    }

    .pagination .active {
        background-color: #3498db;
        color: white;
        border-color: #3498db;
    }

    .pagination .disabled {
        color: #6c757d;
        pointer-events: none;
    }

    /* Estilos para alertas personalizadas */
    .custom-alert {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        display: flex;
        align-items: center;
        animation: slideIn 0.3s ease-out;
        max-width: 400px;
    }

    .custom-alert.success {
        background-color: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
    }

    .custom-alert.error {
        background-color: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
    }

    .custom-alert .icon {
        margin-right: 15px;
        font-size: 24px;
    }

    .custom-alert .message {
        flex: 1;
    }

    .custom-alert .close {
        margin-left: 15px;
        cursor: pointer;
        font-size: 20px;
        opacity: 0.7;
        transition: opacity 0.3s;
    }

    .custom-alert .close:hover {
        opacity: 1;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    /* Estilos para el modal */
    #confirmationModal {
        display: none;
        position: fixed !important;
        z-index: 99999 !important;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        overflow: auto;
    }

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 50%;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        position: relative;
    }

    .modal-header {
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }

    .modal-footer {
        border-top: 1px solid #eee;
        padding-top: 15px;
        margin-top: 15px;
        text-align: right;
    }
</style>

</body>
</html>