<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
</head>
<body>

<h1>Регистрация</h1>

<form method="POST" action="{{ route('register.store') }}">
    @csrf

    <div>
        <label>
            Имя:
            <input type="text" name="name" required>
        </label>
    </div>

    <div>
        <label>
            Email:
            <input type="email" name="email" required>
        </label>
    </div>

    <div>
        <label>
            Пароль:
            <input type="password" name="password" required>
        </label>
    </div>

    <div>
        <label>
            Повтор пароля:
            <input type="password" name="password_confirmation" required>
        </label>
    </div>

    <button type="submit">Зарегистрироваться</button>
</form>

</body>
</html>
