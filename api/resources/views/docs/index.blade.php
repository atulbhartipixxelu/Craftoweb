<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CraftoWeb API Documentation</title>
    <link rel="stylesheet" href="{{ asset('css/docs.css') }}">
</head>
<body class="docs-body">
    <div class="docs-wrap docs-wrap--wide">
        <div class="docs-card">
            <div class="docs-top">
                <div>
                    <h1>CraftoWeb API</h1>
                    <p>Logged in as {{ $user->name }} ({{ $user->email }})</p>
                </div>
                <form method="POST" action="{{ route('docs.logout') }}">
                    @csrf
                    <button type="submit" class="docs-btn docs-btn--ghost">Logout</button>
                </form>
            </div>

            <div class="docs-section">
                <h2>Base URL</h2>
                <pre><code>{{ $apiBaseUrl }}</code></pre>

                <h2>1. Login (get token)</h2>
                <pre><code>POST {{ $apiBaseUrl }}/login
Content-Type: application/json

{
  "email": "your@email.com",
  "password": "your-password"
}</code></pre>
                <p>Response mein <code>data.token</code> use karo.</p>

                <h2>2. Header (har request)</h2>
                <pre><code>Authorization: Bearer YOUR_TOKEN
Accept: application/json
Content-Type: application/json</code></pre>

                <h2>3. Projects — list</h2>
                <pre><code>GET {{ $apiBaseUrl }}/projects</code></pre>

                <h2>4. Project — single</h2>
                <pre><code>GET {{ $apiBaseUrl }}/projects/{id}</code></pre>

                <h2>5. Project — add</h2>
                <pre><code>POST {{ $apiBaseUrl }}/projects

{
  "name": "Project name",
  "client": "Client name",
  "technology": "React",
  "startDate": "2026-06-01",
  "status": "active",
  "priority": "medium",
  "progress": 0,
  "value": "₹50000"
}</code></pre>
            </div>
        </div>
    </div>
</body>
</html>
