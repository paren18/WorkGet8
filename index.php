<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>GET8</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 16px;
            color: #000;
            background-color: #f8f8f8;
        }

        * {
            box-sizing: border-box;
        }

        .form {
            max-width: 320px;
            padding: 15px;
            margin: 20px auto;
            background-color: #fff;
        }

        .input {
            display: block;
            width: 100%;
            padding: 8px 10px;
            margin-bottom: 10px;

            border: 1px solid #ccc;

            font-family: inherit;
            font-size: 16px;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 8px 10px;

            border: 0;
            background-color: #1cbc11;
            cursor: pointer;

            font-family: inherit;
            font-size: 16px;
            color: #fff;
        }

        .btn:hover {
            background-color: #14a20a;
        }
    </style>
    <script src="https://unpkg.com/imask"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>
<form class="form" action="action.php" method="post" id="registrationForm">
    <h2>Валидация</h2>
    <label for="name"><b>Имя</b></label>
    <input class="input" type="text" name="name" placeholder="Ваше имя">
    <label for="email"><b>Email</b></label>
    <input class="input" type="email" name="email" placeholder="Ваш e-mail">
    <label for="phone"><b>Телефон</b></label>
    <input class="input phone-mask" type="tel" name="phone" placeholder="Ваш телефон">
    <button class="btn" type="button" id="submitBtn">Отправить</button>
    <div id="responseMessage"></div>
</form>
<script>
    let elements = document.getElementsByClassName('phone-mask');
    for (let i = 0; i < elements.length; i++) {
        new IMask(elements[i], {
            mask: '+{7}(000)000-00-00',
        });
    }
</script>

<script>
    $(document).ready(function () {
        function submitForm() {
            $('.input').removeClass('error');
            $('#responseMessage').empty();

            var formData = $('#registrationForm').serialize();

            $.ajax({
                type: 'POST',
                url: 'action.php',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        // Обработка успешного ответа
                        $('#responseMessage').html('<p class="success-message">' + response.message + '</p>');
                    } else {
                        // Обработка ошибок
                        $('#responseMessage').html('<p class="error-message">' + response.message + '</p>');

                        if (response.errors) {
                            $.each(response.errors, function (field, errorMessage) {
                                $('#' + field).addClass('error');
                                $('#responseMessage').append('<p class="error-message">' + errorMessage + '</p>');
                            });
                        }
                    }
                },
                error: function () {
                    $('#responseMessage').html('<p class="error-message">Что-то пошло не так. Пожалуйста, попробуйте позже.</p>');
                }
            });
        }

        // Clear error messages and classes when input values change
        $('.input').on('input', function () {
            $(this).removeClass('error');
            $('#responseMessage').empty();
        });

        $('#submitBtn').click(function () {
            submitForm();
        });
    });
</script>

</body>
</html>