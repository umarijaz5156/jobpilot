
<!-- resources/views/preloader.blade.php -->
<div class="preloader">
    <div class="loader"></div>
</div>
<style>
    :root {
        --main-color: {{ $setting->main_color }} !important;
    }
</style>
<style>
    /* public/css/preloader.css */
    .preloader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        transition: all 0.5s ease-in-out;
    }

    .loader {
        border: 4px solid #f3f3f3;
        border-top: 4px solid var(--main-color);;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

</style>
