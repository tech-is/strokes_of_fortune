<?php
    $nums = 0;
    file_exists($fp = dirname(__FILE__)."/functions.php")?require_once($fp):exit('必要なファイルが存在しません');

    if(!empty($_POST['name'])) {
        $pdo = dbconnect();
        $str = preg_replace("/( |　)/", "", trim($_POST['name'])); 
        $names = mb_strlen($str) > 1? mb_str_split($str): $str;
        if(is_array($names)) {
            foreach($names as $name) {
                $num = get_strokes($name, $pdo);
                $nums = $nums + $num;
            }
        } else {
            $nums = get_strokes($names, $pdo);
        }
        $result = $nums? fortuner($nums): null;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="my-4">
            <h2>名前判断プログラム</h2>
        </div>
        <form method="POST">
            <div class="form-group">
                <label for="name">名前</label>
                <input type="text" class="form-control" name="name" placeholder="名前を漢字で入力してください">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        <div class="my-4">
            <?php if(!empty($result)): ?>
                <p>名前: <?= htmlspecialchars($_POST['name']) ?></p>
                <p>総画数 <?= htmlspecialchars($nums) ?></p>
                <p>あなたは... <span style="font-size: 12rem">「<?= $result ?>」</span>です</p>
            <?php elseif(isset($result)): ?>
                <p>名前: <?= htmlspecialchars(@$_POST['name']) ?></p>
                <p>総画数 <?= htmlspecialchars(@$nums) ?></p>
                <p>判定できませんでした...</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>