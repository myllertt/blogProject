<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Inicial</title>
</head>
<body>
    
    <h1>Bém-vindo(a) à página de blogs</h1>

    <?php foreach($testeArgumentoView AS $ob): ?>

        <h2><?php echo $ob ?></h2>

    <?php endforeach; ?>

</body>
</html>

<script>
    objDELETE_Request = new DELETE_Request();
</script>