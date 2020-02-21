<?php

    /**
     * テキストデータベースをmysqlにコンバートする
     *
     * @return void
     */
    function convertdb()
    {
        $filepath = dirname(__FILE__).'/ucs-strokes.txt';
        $f = file_exists($filepath)? fopen($filepath, 'r'): exit('対応するファイルが存在しません');
        $pdo = dbconnect();
        while ($line = fgets($f)) {
            if (!empty($line)) {
                $array = explode("\t", trim($line));
                if (!empty($array[2])) {
                    $array[2] = ($offset = mb_strpos($array[2], ','))? mb_substr($array[2], 0, $offset):  $array[2];
                } else {
                    $array[2] = 0;
                }
                insert($pdo, $array);
            }
        }
    }

    /**
     * DBと接続する
     * 接続出来ない場合は強制終了。
     *
     * @return obj|void
     */
    function dbconnect()
    {
        try {
            return new PDO(
                'mysql:dbname=techis;host=localhost;charset=utf8mb4',
                'root',
                '',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            header('Content-Type: text/plain; charset=UTF-8', true, 500);
            exit($e->getMessage());
        }
    }

    /**
     * データベースに登録する
     *
     * @param obj $pdo
     * @param array $array
     * @return bool
     */
    function insert($pdo, array $array)
    {
        $stmt = $pdo->prepare("INSERT INTO cjk_table (unicode, number_of_stroke) VALUES (:unicode, :number_of_stroke)");
        $stmt->bindValue(':unicode', $array[0], PDO::PARAM_STR);
        $stmt->bindValue(':number_of_stroke', (int)$array[2], PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * データベースから漢字の画数を取得
     *
     * @param str $name
     * @param obj $pdo
     * @return int|void
     */
    function get_strokes($name, $pdo)
    {
        $name = sprintf("U+%04X", hexdec(bin2hex(mb_convert_encoding($name, 'UCS-4', 'UTF-8'))));
        $stmt = $pdo->prepare('SELECT number_of_stroke FROM cjk_table WHERE unicode = :unicode');
        $stmt->bindValue(':unicode', $name, PDO::PARAM_STR);
        $stmt->execute();
        $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fetch['number_of_stroke']? intval($fetch['number_of_stroke']): null;
    }

    /**
     * マルチバイト文字を1文字ずつ配列に格納
     *
     * @param str $subject
     * @param integer $length
     * @return array
     */
    function mb_str_split($subject, $length = 1)
    {
        // 正規表現でマッチするものを取得
        preg_match_all("/.{1,$length}/u", $subject, $match, PREG_OFFSET_CAPTURE, 0);
        $temp = array();
        foreach ($match[0] as $value) {
            // マッチした正規表現を返り値用配列に挿入
            $temp[] = $value[0];
        }
        return $temp;
    }

    /**
     * 配列のインデックスに漢字の画数を代入して占いの結果を出力
     *
     * @param int $index
     * @return str|bool
     */
    function fortuner(int $index)
    {
        $kanji_array = [2 => '凶', 3 => '吉', 4 => '凶', 5 => '吉', 6 => '吉', 7 => '半吉', 8 => '吉', 9 => '凶', 10 => '凶', 11 => '大吉', 12 => '凶', 13 => '吉', 14 => '凶', 15 => '吉', 16 => '大吉', 17 => '半吉', 18 => '吉', 19 => '凶', 20 => '凶', 21 => '大吉', 22 => '凶', 23 => '大吉', 24 => '吉', 25 => '吉', 26 => '凶', 27 => '半吉', 28 => '凶', 29 => '吉', 30 => '半吉', 31 => '大吉', 32 => '大吉', 33 => '吉', 34 => '半吉', 35 => '半吉', 36 => '半吉', 37 => '吉', 38 => '半吉', 39 => '吉', 40 => '半吉', 41 => '大吉', 42 => '半吉', 43 => '半吉', 44 => '吉', 45 => '吉', 46 => '凶', 47 => '吉', 48 => '吉', 49 => '半吉', 50 => '凶', 53 => '半吉', 54 => '凶', 55 => '凶', 56 => '凶', 57 => '半吉', 58 => '半吉', 59 => '凶', 60 => '凶', 61 => '半吉', 63 => '半吉'];
        return array_key_exists($index, $kanji_array)? $kanji_array[$index]: false;
    }
