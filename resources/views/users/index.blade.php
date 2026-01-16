<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Users</title>
</head>
<body>

<h1>Registered users</h1>

@if(empty($users))
    <p>No users yet.</p>
@else
    <ul>
        @foreach($users as $user)
            <li style="margin-bottom: 10px;">
                <strong>{{ $user->nickname }}</strong><br>
                <img
                    src="{{ Storage::url($user->avatar) }}"
                    alt="{{ $user->nickname }}"
                    width="100"
                >
            </li>
        @endforeach
    </ul>
@endif

</body>
</html>
