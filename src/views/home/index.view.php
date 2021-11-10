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

    <form action="" method="post">
        <input type="hidden" name="_method" value="put">
        <input type="hidden" name="valor" value="2222"></input>
        <button type="submit" name="Postar"></button>        
    </form>

</body>
</html>

<script>
    objDELETE_Request = new DELETE_Request();
</script>

