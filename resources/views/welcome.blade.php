<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <style>
            *, ::after, ::before {
                box-sizing: border-box;
                border-width: 0;
                border-style: solid;
            }
            html {
                line-height: 1.5;
                font-family: Figtree, ui-sans-serif, system-ui, sans-serif;
            }
            body {
                margin: 0;
                line-height: inherit;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
            }
            .container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 2rem;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }
            .card {
                background: rgba(255, 255, 255, 0.95);
                border-radius: 1.5rem;
                padding: 3rem;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                text-align: center;
                max-width: 600px;
                width: 100%;
            }
            .logo {
                width: 120px;
                height: 120px;
                margin: 0 auto 2rem;
            }
            .logo svg {
                width: 100%;
                height: 100%;
            }
            h1 {
                font-size: 2.5rem;
                font-weight: 600;
                color: #1a1a2e;
                margin: 0 0 1rem;
            }
            .version {
                display: inline-block;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 0.25rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.875rem;
                font-weight: 500;
                margin-bottom: 1.5rem;
            }
            p {
                color: #4a5568;
                font-size: 1.125rem;
                margin: 0 0 2rem;
                line-height: 1.75;
            }
            .links {
                display: flex;
                gap: 1rem;
                justify-content: center;
                flex-wrap: wrap;
            }
            .links a {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.75rem 1.5rem;
                background: #f7f7f7;
                color: #374151;
                text-decoration: none;
                border-radius: 0.75rem;
                font-weight: 500;
                transition: all 0.2s;
            }
            .links a:hover {
                background: #667eea;
                color: white;
                transform: translateY(-2px);
            }
            .services {
                margin-top: 2rem;
                padding-top: 2rem;
                border-top: 1px solid #e5e7eb;
            }
            .services h2 {
                font-size: 1.25rem;
                color: #374151;
                margin: 0 0 1rem;
            }
            .service-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 1rem;
            }
            .service-item {
                background: linear-gradient(135deg, #f6f8fb 0%, #e9ecf2 100%);
                padding: 1rem;
                border-radius: 0.75rem;
                text-align: center;
            }
            .service-item .icon {
                font-size: 2rem;
                margin-bottom: 0.5rem;
            }
            .service-item .name {
                font-weight: 600;
                color: #1a1a2e;
            }
            .service-item .port {
                font-size: 0.875rem;
                color: #6b7280;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="card">
                <div class="logo">
                    <svg viewBox="0 0 62 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M61.8548 14.6253C61.8778 14.7102 61.8895 14.7978 61.8895 14.8858V28.5615C61.8895 28.737 61.8327 28.9085 61.7259 29.0545C61.6## 29.2005 61.5765 29.3151 61.4604 29.3857L50.0801 35.9906V48.7917C50.0801 49.0735 49.9293 49.3334 49.6899 49.4763L25.6091 62.8512C25.5539 62.8817 25.4935 62.9043 25.4307 62.9193C25.4135 62.9237 25.3957 62.9265 25.378 62.9306C25.2719 62.9518 25.1627 62.9518 25.0566 62.9306C25.0371 62.9266 25.0177 62.9232 24.9986 62.9182C24.9371 62.9032 24.8778 62.8794 24.824 62.8471L0.760344 49.4763C0.641756 49.4077 0.543906 49.3089 0.477561 49.191C0.411217 49.0731 0.378802 48.9402 0.383343 48.8058V14.8858C0.383343 14.7974 0.395049 14.7094 0.417997 14.6253L0.430689 14.5733C0.449262 14.5071 0.475775 14.4432 0.509615 14.3829L0.522307 14.3576C0.564525 14.2868 0.617536 14.2223 0.679655 14.1664L0.696655 14.1505C0.760344 14.0945 0.832733 14.0477 0.911161 14.0108L13.4607 7.27992L25.4307 0.697699C25.5572 0.627636 25.6987 0.59082 25.8428 0.59082C25.9868 0.59082 26.1284 0.627636 26.2549 0.697699L50.3204 14.0108C50.3992 14.0477 50.4716 14.0945 50.5353 14.1505L50.5523 14.1664C50.6144 14.2223 50.6674 14.2868 50.7096 14.3576L50.7223 14.3829C50.7562 14.4432 50.7827 14.5071 50.8012 14.5733L50.814 14.6253C50.8369 14.7102 50.8486 14.7978 50.8486 14.8858V31.4615L61.0148 25.3378V14.8858C61.0148 14.7978 61.0266 14.7102 61.0496 14.6253L61.0623 14.5733C61.0808 14.5071 61.1074 14.4432 61.1412 14.3829L61.1539 14.3576C61.1961 14.2868 61.2492 14.2223 61.3113 14.1664L61.3283 14.1505C61.392 14.0945 61.4643 14.0477 61.5428 14.0108L61.5548 14.0048C61.6464 13.9553 61.7463 13.9209 61.8495 13.9027C61.9145 13.8911 61.9802 13.8855 62.0461 13.8858C62.1902 13.8858 62.3318 13.9227 62.4583 13.9927L62.4803 14.0048C62.559 14.0424 62.6313 14.0899 62.695 14.1464L62.712 14.1623C62.7741 14.2182 62.8271 14.2827 62.8693 14.3535L62.882 14.3788C62.9158 14.4391 62.9424 14.503 62.9609 14.5692L62.9737 14.6212V14.6253H61.8548ZM48.8155 35.9906L37.4352 42.5955V55.3965L48.8155 48.7917V35.9906ZM25.8428 58.1666L48.2845 45.6085V32.8057L37.4352 39.4105V52.2117L25.8428 58.1666ZM2.91842 17.0713V48.0526L25.3601 60.6107V29.6294L2.91842 17.0713ZM38.9592 29.5682L50.3396 23.2372V12.0697L38.9592 18.4007V29.5682ZM26.5794 3.95265L15.1991 10.2836L26.5794 16.6146L37.9598 10.2836L26.5794 3.95265ZM14.4625 11.6908V22.8584L25.8428 29.1894V18.0219L14.4625 11.6908ZM39.6958 31.9754L51.0762 25.3706L62.4565 31.7017L51.0762 38.0326L39.6958 31.9754ZM63.193 34.5168L51.8127 40.8479V53.6489L63.193 47.044V34.5168ZM50.3396 54.3962V41.5951L38.9592 48.2L38.9592 61.001L50.3396 54.3962Z" fill="url(#paint0_linear)"/>
                        <defs>
                            <linearGradient id="paint0_linear" x1="0.383343" y1="31.7481" x2="62.9737" y2="31.7481" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#667eea"/>
                                <stop offset="1" stop-color="#764ba2"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <h1>Welcome to Laravel</h1>
                <span class="version">v{{ Illuminate\Foundation\Application::VERSION }} (PHP {{ PHP_VERSION }})</span>
                <p>Your Laravel application is up and running with Docker Sail!</p>
                
                <div class="links">
                    <a href="https://laravel.com/docs" target="_blank">
                        📚 Documentation
                    </a>
                    <a href="https://laracasts.com" target="_blank">
                        🎬 Laracasts
                    </a>
                    <a href="https://laravel-news.com" target="_blank">
                        📰 Laravel News
                    </a>
                </div>

                <div class="services">
                    <h2>🐳 Docker Services</h2>
                    <div class="service-grid">
                        <div class="service-item">
                            <div class="icon">🚀</div>
                            <div class="name">Laravel</div>
                            <div class="port">Port 80</div>
                        </div>
                        <div class="service-item">
                            <div class="icon">🗄️</div>
                            <div class="name">MySQL 8</div>
                            <div class="port">Port 3306</div>
                        </div>
                        <div class="service-item">
                            <div class="icon">🔧</div>
                            <div class="name">phpMyAdmin</div>
                            <div class="port">Port 8080</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

