<style>
    .button-btn {
        background-color: #2A3F54;
        border: none;
        color: white;
        padding: 4px 6px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 12px;
        cursor: pointer;
        border-radius: 25px;
    }

    .button-btn:hover {
        background-color: #009688;
    }

    .button-btn-btn {
        background-color: #009688;
        border: none;
        color: white;
        padding: 4px 20px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 12px;
        cursor: pointer;
        border-radius: 25px;
    }

    .button-btn-btn:hover {
        background-color: #2A3F54;
    }
</style>

<?php
if (!function_exists('render_breadcrumb')) {
    function render_breadcrumb()
    {
        // Breadcrumb disabled across all pages
        return;
    }
}
?>