<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="UTF-8">
		<title>コメント削除</title>
	</head>
	<body>
		<?php

			require_once '_database_conf.php';
			require_once '_h.php';

			$id=$_GET['id'];

			try
			{
				$db = new PDO($dsn, $dbUser, $dbPass);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				$sql='DELETE FROM comments WHERE id = :id';
				$stmt=$db->prepare($sql);
				$stmt->bindValue(':id', $id, PDO::PARAM_INT);
				$stmt->execute();

				$db=null;

				print '削除しました。<br />';

			}
			catch(Exception$e)
			{
				echo 'エラーが発生しました。内容: ' . h($e->getMessage());
	 			exit();
			}

		?>
		<a href="index.php">戻る</a>
	</body>
</html>