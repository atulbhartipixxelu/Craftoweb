<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Docs Login — CraftoWeb</title>
    <link rel="stylesheet" href="{{ asset('css/docs.css') }}">
</head>
<body class="docs-body">
    <div class="docs-wrap">
        <div class="docs-card">
            <div class="docs-brand">
                <h1>CraftoWeb API</h1>
                <p>Documentation login</p>
            </div>

            @if ($errors->any())
                <div class="docs-errors">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('docs.login') }}">
                @csrf

                <div class="docs-field">
                    <label for="email">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                    >
                </div>

                <div class="docs-field">
                    <label for="password">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                    >
                </div>

                <label class="docs-remember">
                    <input type="checkbox" name="remember" value="1">
                    Remember me
                </label>

                <button type="submit" class="docs-btn">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
