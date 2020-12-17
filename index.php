<?php
// var_dump($_POST);
// exit();
define('FILENAME', './data.csv');
date_default_timezone_set('Asia/Tokyo');

// 変数の初期化
$data = null;
$now_date = null;
$file = null;
$split_data = null;
$message = array();
$message_array = array();
$success_message = null;
$error_message = array();
$clean = array();


if (!empty($_POST['btn_submit'])) {

  if (empty($_POST['name'])) {
    $error_message[] = '表示名が入力されていません';//error処理
  } else {
    $clean['name'] = htmlspecialchars($_POST['name'], ENT_QUOTES); //サニタイズ処理
    $clean['name'] = preg_replace('/\\r\\n|\\n|\\r/', '', $clean['name']);
  }

  if (empty($_POST['message'])) {
    $error_message[] = '本文が入力されていません'; //error処理
  } else {
    $clean['message'] = htmlspecialchars($_POST['message'], ENT_QUOTES); //サニタイズ処理
    $clean['message'] = preg_replace('/\\r\\n|\\n|\\r/', '',$clean['message']);
  }

  if (empty($error_message)) {
    
    if ($file = fopen(FILENAME, "a")) {  // ファイルを開く 引数はa

      $now_date = date("Y-m-d H:i:s");  //日付の取得

      $data = "'" . $clean['name'] . "','" . $clean['message'] . "','" . $now_date . "'\n"; // スペース区切りで最後に改行

      // flock($file, LOCK_EX); // ファイルをロック

      fwrite($file, $data); // データに書き込み,

      // flock($file, LOCK_UN); // ロック解除

      fclose($file); // ファイルを閉じる

      $success_message = 'メッセージを書き込みました。';
    }
    

    // $mysqli = new mysqli('localhost', 'root', '', 'board'); // データベースに接続
    // if ($mysqli->connect_errno) {
    //   $error_message[] = '書き込みに失敗しました。 エラー番号 ' . $mysqli->connect_errno . ' : ' . $mysqli->connect_error;
    // } else {

    //   $mysqli->set_charset('utf8'); //文字コード取得

    //   $now_date = date("Y-m-d H:i:s"); //日時取得

    //   //insert sql
    //   $sql = "INSERT INTO message (name, message, post_date) VALUES ( '$clean[name]', '$clean[message]', '$now_date')";
    
    //   $res = $mysqli->query($sql); //データを登録

    //   if($res) {
    //     $success_message = 'メッセージを書き込みました。';
    //   } else {
    //     $error_message[] = '書き込みに失敗しました。';
    //   }

    //   $mysqli->close(); //dbをclose
    // }
  }
}
// flock($file, LOCK_EX); // ファイルをロック

if ($file = fopen(FILENAME, "r")) {
  while ($data = fgets($file)) {

    $split_data = preg_split('/\'/', $data);

    $message = array(
      'name' => $split_data[1],
      'message' => $split_data[3],
      'post_date' => $split_data[5]
    );
    array_unshift($message_array, $message);
  }
  // flock($file, LOCK_UN); // ロック解除
  fclose($file); // ファイルを閉じる
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>DEV7ちゃんねる</title>
</head>

<body>

  <h1>DEV7ちゃんねる</h1>
  <?php if (!empty($success_message)): ?>
    <p class="success_message"><?php echo $success_message; ?></p>
  <?php endif; ?>
  <?php if (!empty($error_message)): ?>
    <ul class="error_message">
      <?php foreach ($error_message as $value): ?>
        <li>・<?php echo $value; ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
  <form method="post">
    <div>
      <label for="name">表示名</label>
      <input id="name" type="text" name="name" value="">
    </div>
    <div>
      <label for="message">本文</label>
      <textarea id="message" name="message"></textarea>
    </div>
    <div>
      <input type="submit" name="btn_submit" value="投稿">
    </div>

    <section>
      <?php if (!empty($message_array)) { ?>
        <?php foreach ($message_array as $value) { ?>
          <article>
            <div class="info">
              <h2><?php echo $value['name']; ?></h2>
              <time><?php echo date('Y年m月d日 H:i', strtotime($value['post_date'])); ?></time>
            </div>
            <p><?php echo $value['message']; ?></p>
          </article>
        <?php } ?>
      <?php } ?>
    </section>
  </form>




</body>

</html>