<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Mortgage Loan Calculator') - {{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --primary: #0f766e; --primary-dark: #0d5d56; --primary-light: #14b8a6;
            --secondary: #1e293b; --success: #10b981; --danger: #ef4444;
            --bg-dark: #0f172a; --bg-card: #1e293b; --bg-input: #334155;
            --text-primary: #f8fafc; --text-secondary: #94a3b8; --text-muted: #64748b;
            --border: #334155;
        }
        body { font-family: 'Instrument Sans', system-ui, sans-serif; background: var(--bg-dark); color: var(--text-primary); line-height: 1.6; min-height: 100vh; }
        .container { max-width: 1400px; margin: 0 auto; padding: 2rem; }
        .header { text-align: center; margin-bottom: 3rem; padding: 2rem 0; }
        .header h1 { font-size: 2.5rem; font-weight: 700; background: linear-gradient(135deg, var(--primary-light), #f59e0b); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 0.5rem; }
        .header p { color: var(--text-secondary); font-size: 1.125rem; }
        .card { background: var(--bg-card); border-radius: 1rem; padding: 2rem; margin-bottom: 2rem; border: 1px solid var(--border); }
        .card-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border); }
        .card-header .icon { width: 48px; height: 48px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .card-header h2 { font-size: 1.5rem; font-weight: 600; }
        .card-header p { color: var(--text-secondary); font-size: 0.875rem; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; }
        .form-group { display: flex; flex-direction: column; gap: 0.5rem; }
        .form-group label { font-weight: 500; color: var(--text-primary); font-size: 0.875rem; }
        .form-group input { background: var(--bg-input); border: 2px solid var(--border); border-radius: 0.5rem; padding: 0.875rem 1rem; font-size: 1rem; color: var(--text-primary); transition: all 0.2s; }
        .form-group input:focus { outline: none; border-color: var(--primary-light); box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.2); }
        .form-group .hint { font-size: 0.75rem; color: var(--text-muted); }
        .form-group.error input { border-color: var(--danger); }
        .form-group .error-message { color: var(--danger); font-size: 0.75rem; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 1rem 2rem; font-size: 1rem; font-weight: 600; border-radius: 0.5rem; border: none; cursor: pointer; transition: all 0.2s; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(15, 118, 110, 0.3); }
        .btn-secondary { background: var(--bg-input); color: var(--text-primary); }
        .btn-danger { background: var(--danger); color: white; }
        .btn-sm { padding: 0.5rem 1rem; font-size: 0.875rem; }
        .alert { padding: 1rem 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; }
        .alert-success { background: rgba(16, 185, 129, 0.15); border: 1px solid var(--success); color: var(--success); }
        .alert-error { background: rgba(239, 68, 68, 0.15); border: 1px solid var(--danger); color: var(--danger); }
        .table-container { overflow-x: auto; border-radius: 0.75rem; border: 1px solid var(--border); }
        table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        th, td { padding: 1rem; text-align: right; border-bottom: 1px solid var(--border); }
        th:first-child, td:first-child { text-align: left; }
        th { background: var(--bg-input); font-weight: 600; color: var(--text-secondary); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; }
        tr:hover td { background: rgba(51, 65, 85, 0.5); }
        tr:last-child td { border-bottom: none; }
        .money { font-family: 'Courier New', monospace; font-weight: 500; }
        .money.positive { color: var(--success); }
        .money.negative { color: var(--danger); }
        .tabs { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem; }
        .tab { padding: 0.75rem 1.5rem; background: transparent; border: none; color: var(--text-secondary); font-weight: 500; cursor: pointer; border-radius: 0.5rem; transition: all 0.2s; }
        .tab:hover { background: var(--bg-input); color: var(--text-primary); }
        .tab.active { background: var(--primary); color: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .loan-header { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); border-radius: 1rem; padding: 2rem; margin-bottom: 2rem; color: white; }
        .loan-header h2 { font-size: 1.25rem; margin-bottom: 1rem; opacity: 0.9; }
        .loan-header-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1.5rem; }
        .loan-header-item .label { font-size: 0.75rem; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.05em; }
        .loan-header-item .value { font-size: 1.5rem; font-weight: 700; }
        .loan-item { display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: var(--bg-input); border-radius: 0.5rem; margin-bottom: 0.5rem; }
        .loan-item:hover { background: var(--border); }
        .loan-item-info { display: flex; gap: 2rem; }
        .loan-item-info span { color: var(--text-secondary); font-size: 0.875rem; }
        .loan-item-info strong { color: var(--text-primary); }
        @media (max-width: 768px) { .container { padding: 1rem; } .header h1 { font-size: 1.75rem; } .form-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>🏠 Mortgage Loan Calculator</h1>
            <p>Calculate your mortgage payments and view detailed amortization schedules</p>
        </header>

        @if(session('success'))
            <div class="alert alert-success"><span>✓</span> {{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-error"><span>✕</span> {{ session('error') }}</div>
        @endif

        @yield('content')
    </div>

    @yield('scripts')
</body>
</html>

