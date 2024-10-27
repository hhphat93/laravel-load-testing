<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <script defer>
        $(document).ready(function() {
            // Thực hiện yêu cầu AJAX
            $.ajax({
                url: 'http://localhost:9001/ajax/test-content-type', // URL của API
                type: 'GET', // Phương thức GET
                // dataType : 'text',
                dataType: 'json',
                // dataType : 'html',
                success: function(data) {
                    console.log(data);
                },
                error: function(xhr, status, error) {
                    // Xử lý lỗi
                    console.error('Error: ' + error);
                }
            });
        });
    </script>
</head>

<body class="antialiased">
    Test accept, content-type ajax
</body>

</html>
