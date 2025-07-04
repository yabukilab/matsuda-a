<?php
$guide_image = "introduction_thread.png";
require_once 'includes/config.php';
require_once 'includes/functions.php';

// ログイン確認
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: home.php");
    exit;
}

$thread_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT t.*, u.name AS author_name, u.is_admin 
                      FROM threads t
                      JOIN users u ON t.created_by = u.student_id
                      WHERE t.thread_id = ?");
$stmt->execute([$thread_id]);
$thread = $stmt->fetch();

if (!$thread) {
    header("Location: home.php");
    exit;
}

$stmt = $pdo->prepare("SELECT c.*, u.name AS author_name, u.is_admin 
                      FROM comments c
                      JOIN users u ON c.student_id = u.student_id
                      WHERE c.thread_id = ?
                      ORDER BY c.created_at ASC");
$stmt->execute([$thread_id]);
$comments = $stmt->fetchAll();

$last_comment_id = 0;
foreach ($comments as &$comment) {
    // ファイル情報取得
    $stmt = $pdo->prepare("SELECT * FROM uploaded_files WHERE comment_id = ?");
    $stmt->execute([$comment['comment_id']]);
    $comment['files'] = $stmt->fetchAll();

    // 最新コメントIDを保持
    if ($comment['comment_id'] > $last_comment_id) {
        $last_comment_id = $comment['comment_id'];
    }
}
unset($comment);
?>

<?php include 'includes/header.php'; ?>
<div class="container">
    <header>
        <h1><?= h($thread['title']) ?></h1>

        <!-- ヘッダー部分に追加 -->
        <div class="help-btn-container">
            <a href="introduction/<?= $guide_image ?>" target="_blank" class="btn help-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                    <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z" />
                </svg>
                使い方ガイド
            </a>
        </div>

        <div class="user-info">
            ユーザー名: <?php echo htmlspecialchars($_SESSION['name']); ?>
            <?php if ($_SESSION['student_id'] === '9877389'): ?>
                <span class="admin-badge">管理者</span>
            <?php endif; ?>
            <a href="home.php" class="btn">ホームに戻る</a>
            <a href="logout.php" class="logout-btn">ログアウト</a>
        </div>
    </header>

    <div class="thread-meta">
        <span>カテゴリ: <?= h($thread['category']) ?></span>
        <span>作成者: <?= h($thread['author_name']) ?></span>
        <span>作成日時: <?= h($thread['created_at']) ?></span>
    </div>

    <div class="comment-section">
        <h2>コメント</h2>

        <div class="comment-list" id="comment-list">
            <?php if (empty($comments)): ?>
                <p id="no-comments-message">コメントがありません</p>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment-item" id="comment-<?= $comment['comment_id'] ?>">
                        <div class="comment-header">
                            <span class="comment-author">
                                <?= h($comment['author_name']) ?>
                                <?php if ($comment['is_admin']): ?>
                                    <span class="admin-badge">管理者</span>
                                <?php endif; ?>
                            </span>
                            <span class="comment-date"><?= h($comment['created_at']) ?></span>

                            <?php if ($_SESSION['student_id'] === $comment['student_id'] || $_SESSION['student_id'] === '9877389'): ?>
                                <form method="post" action="delete.php" class="inline-form">
                                    <input type="hidden" name="comment_id" value="<?= $comment['comment_id'] ?>">
                                    <button type="submit" class="delete-btn" onclick="return confirm('本当に削除しますか？')">削除</button>
                                </form>
                            <?php endif; ?>
                        </div>

                        <div class="comment-content"><?= nl2br(htmlspecialchars_decode(trim(h($comment['content'])))) ?></div>

                        <?php if (!empty($comment['files'])): ?>
                            <div class="comment-files">
                                <strong>添付ファイル:</strong>
                                <?php foreach ($comment['files'] as $file): ?>
                                    <?php
                                    $ext = strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION));
                                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    $isPDF = ($ext === 'pdf'); // PDF判定を追加
                                    ?>
                                    <div class="file-item">
                                        <?php if ($isImage): ?>
                                            <div>
                                                <a href="download.php?file_id=<?= $file['file_id'] ?>&preview=1" target="_blank">
                                                    <!-- base64データを直接表示 -->
                                                    <img src="data:<?= $file['file_type'] ?>;base64,<?= $file['file_data'] ?>"
                                                        class="preview-image"
                                                        alt="画像プレビュー"
                                                        style="max-width: 400px; max-height: 300px; display: block;">
                                                </a>
                                            </div>
                                            <div>
                                                <a href="download.php?file_id=<?= $file['file_id'] ?>" download="<?= h($file['file_name']) ?>">
                                                    <?= h($file['file_name']) ?> (ダウンロード)
                                                </a>
                                            </div>
                                        <?php elseif ($isPDF): ?>
                                            <!-- PDFプレビュー用のリンクを追加 -->
                                            <div>
                                                <a href="preview_pdf.php?file_id=<?= $file['file_id'] ?>" target="_blank">
                                                    PDFをプレビュー
                                                </a>
                                            </div>
                                            <div>
                                                <a href="download.php?file_id=<?= $file['file_id'] ?>" download="<?= h($file['file_name']) ?>">
                                                    <?= h($file['file_name']) ?> (ダウンロード)
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <a href="download.php?file_id=<?= $file['file_id'] ?>" download="<?= h($file['file_name']) ?>">
                                                <?= h($file['file_name']) ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="comment-form">
            <h3>コメントを投稿</h3>
            <form method="post" action="post_comment.php">
                <input type="hidden" name="thread_id" value="<?= h($thread_id) ?>">

                <div class="form-group">
                    <textarea name="content" rows="5" required placeholder="コメントを入力"></textarea>
                </div>

                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button type="submit" class="btn">投稿</button>
                    <a href="upload_file.php?thread_id=<?= h($thread_id) ?>" class="btn">
                        ファイルアップロード
                    </a>
                    <a href="table_editor.php?thread_id=<?= h($thread_id) ?>" class="btn">
                        表を作成する
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- スクロールボタン -->
<div class="scroll-to-bottom" onclick="scrollToCommentForm()">↓</div>

<script>
    // ポーリング用の変数
    let lastCommentId = <?= $last_comment_id ?>;
    const threadId = <?= $thread_id ?>;
    let isPollingActive = true;
    const pollingInterval = 5000; // 5秒ごとにチェック

    // 設定値
    const FOCUS_OUT_DELAY = 10000; // フォーカスが外れてから10秒後に再開
    const INPUT_IDLE_TIMEOUT = 30000; // 30秒間入力がない場合に再開

    // タイマー変数
    let focusOutTimer = null;
    let inputIdleTimer = null;
    let lastInputTime = 0;

    // テキストエリア要素を取得
    const commentTextarea = document.querySelector('textarea[name="content"]');

    // 新しいコメントをチェックする関数
    function checkForNewComments() {
        // スクロール位置を保存
        const scrollTopBefore = $(window).scrollTop();
        const scrollHeightBefore = $(document).height();

        // テキストエリアにフォーカスがある場合はポーリングをスキップ
        if (commentTextarea && document.activeElement === commentTextarea) {
            // 入力アイドル状態チェック
            const now = Date.now();
            if (now - lastInputTime > INPUT_IDLE_TIMEOUT) {
                restartPolling();
            } else {
                setTimeout(checkForNewComments, pollingInterval);
            }
            return;
        }

        if (!isPollingActive) return;

        $.ajax({
            url: 'get_new_comments.php',
            type: 'GET',
            data: {
                thread_id: threadId,
                last_comment_id: lastCommentId
            },
            dataType: 'json',
            success: function(newComments) {
                if (newComments.length > 0) {
                    // 「コメントがありません」メッセージを削除
                    $('#no-comments-message').remove();

                    // 追加前のスクロール位置が最下部付近か判定
                    const wasNearBottom = isNearBottom();

                    // 新しいコメントを追加
                    newComments.forEach(comment => {
                        const commentElement = createCommentElement(comment);
                        $('#comment-list').append(commentElement);
                        highlightNewComment(comment.comment_id);

                        // 最新コメントIDを更新
                        if (comment.comment_id > lastCommentId) {
                            lastCommentId = comment.comment_id;
                        }
                    });

                    // 新しいコメントがあることを通知
                    showNewCommentsNotification(newComments.length);

                    // 追加後のドキュメント高さ
                    const scrollHeightAfter = $(document).height();

                    // スクロール位置を調整
                    if (wasNearBottom) {
                        // 元々最下部付近にいた場合は新しい最下部にスクロール
                        $('html, body').scrollTop(scrollHeightAfter);
                    } else {
                        // それ以外は元のスクロール位置を維持（高さの変化分を加算）
                        const scrollTopAfter = scrollTopBefore + (scrollHeightAfter - scrollHeightBefore);
                        $('html, body').scrollTop(scrollTopAfter);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('コメント取得エラー:', error);
            },
            complete: function() {
                // 次のポーリングをスケジュール
                setTimeout(checkForNewComments, pollingInterval);
            }
        });
    }

    // 最下部付近にいるか判定
    function isNearBottom(threshold = 100) {
        const scrollTop = $(window).scrollTop();
        const windowHeight = $(window).height();
        const documentHeight = $(document).height();

        return (scrollTop + windowHeight) >= (documentHeight - threshold);
    }

    // ポーリングを再開する関数
    function restartPolling() {
        // タイマーをクリア
        clearTimeout(focusOutTimer);
        clearTimeout(inputIdleTimer);

        // ポーリング状態をアクティブに
        isPollingActive = true;

        // 即座にチェック
        checkForNewComments();
    }

    // ポーリングを一時停止する関数
    function pausePolling() {
        isPollingActive = false;
        clearTimeout(focusOutTimer);
        clearTimeout(inputIdleTimer);
    }

    // コメント要素を作成する関数
    function createCommentElement(comment) {
        let filesHtml = '';

        if (comment.files && comment.files.length > 0) {
            filesHtml += '<div class="comment-files"><strong>添付ファイル:</strong>';

            comment.files.forEach(file => {
                const ext = file.file_name.split('.').pop().toLowerCase();
                const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);
                const isPDF = (ext === 'pdf'); // PDF判定を追加

                if (isImage) {
                    filesHtml += `
                        <div class="file-item">
                            <div>
                                <a href="download.php?file_id=${file.file_id}&preview=1" target="_blank">
                                    <img src="data:${file.file_type};base64,${file.file_data}"
                                         class="preview-image"
                                         alt="画像プレビュー"
                                         style="max-width: 400px; max-height: 300px; display: block;">
                                </a>
                            </div>
                            <div>
                                <a href="download.php?file_id=${file.file_id}" download="${escapeHtml(file.file_name)}">
                                    ${escapeHtml(file.file_name)} (ダウンロード)
                                </a>
                            </div>
                        </div>`;
                } else if (isPDF) {
                    // PDFプレビュー用リンクを追加
                    filesHtml += `
                        <div class="file-item">
                            <div>
                                <a href="preview_pdf.php?file_id=${file.file_id}" target="_blank">
                                    PDFをプレビュー
                                </a>
                            </div>
                            <div>
                                <a href="download.php?file_id=${file.file_id}" download="${escapeHtml(file.file_name)}">
                                    ${escapeHtml(file.file_name)} (ダウンロード)
                                </a>
                            </div>
                        </div>`;
                } else {
                    filesHtml += `
                        <div class="file-item">
                            <a href="download.php?file_id=${file.file_id}" download="${escapeHtml(file.file_name)}">
                                ${escapeHtml(file.file_name)}
                            </a>
                        </div>`;
                }
            });

            filesHtml += '</div>';
        }

        // 削除ボタン（管理者と投稿者のみ）
        let deleteButton = '';
        if ('<?= $_SESSION['student_id'] ?>' === comment.student_id || '<?= $_SESSION['student_id'] ?>' === '9877389') {
            deleteButton = `
                <form method="post" action="delete.php" class="inline-form">
                    <input type="hidden" name="comment_id" value="${comment.comment_id}">
                    <button type="submit" class="delete-btn" onclick="return confirm('本当に削除しますか？')">削除</button>
                </form>`;
        }

        return `
        <div class="comment-item" id="comment-${comment.comment_id}">
            <div class="comment-header">
                <span class="comment-author">
                    ${escapeHtml(comment.author_name)}
                    ${comment.is_admin ? '<span class="admin-badge">管理者</span>' : ''}
                </span>
                <span class="comment-date">${escapeHtml(comment.created_at)}</span>
                ${deleteButton}
            </div>
            <div class="comment-content">${nl2br(escapeHtml(comment.content))}</div>
            ${filesHtml}
        </div>`;
    }

    // 新しいコメントをハイライト
    function highlightNewComment(commentId) {
        $(`#comment-${commentId}`)
            .css('background-color', '#ffffcc')
            .delay(3000)
            .animate({
                backgroundColor: 'transparent'
            }, 1000);
    }

    // 新しいコメント通知
    function showNewCommentsNotification(count) {
        const notification = $(`<div class="new-comment-notification">新しいコメントが${count}件あります</div>`);
        $('body').append(notification);

        notification.fadeIn().delay(2000).fadeOut(function() {
            $(this).remove();
        });
    }

    // HTMLエスケープ関数
    function escapeHtml(text) {
        return text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // 改行を<br>に変換
    function nl2br(text) {
        return text.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br>$2');
    }

    // ページ読み込み時のスクロール制御
    function scrollToCommentForm() {
        const commentForm = document.querySelector('.comment-form');
        commentForm.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    // ページ読み込み時の処理
    $(document).ready(function() {
        // リファラをチェックしてhome.phpからの遷移か判定
        if (document.referrer.includes('home.php')) {
            window.scrollTo(0, 0);
        } else {
            const commentForm = document.querySelector('.comment-form');
            if (commentForm) {
                commentForm.scrollIntoView({
                    behavior: 'auto',
                    block: 'start'
                });
            }
        }

        // テキストエリアのイベントリスナー設定
        if (commentTextarea) {
            // フォーカス取得時
            commentTextarea.addEventListener('focus', function() {
                pausePolling();
                lastInputTime = Date.now();

                // アイドル状態チェック用タイマー開始
                inputIdleTimer = setTimeout(restartPolling, INPUT_IDLE_TIMEOUT);
            });

            // フォーカス喪失時
            commentTextarea.addEventListener('blur', function() {
                // 10秒後に再開
                focusOutTimer = setTimeout(restartPolling, FOCUS_OUT_DELAY);
            });

            // 入力イベント監視
            commentTextarea.addEventListener('input', function() {
                lastInputTime = Date.now();

                // アイドルタイマーをリセット
                clearTimeout(inputIdleTimer);
                inputIdleTimer = setTimeout(restartPolling, INPUT_IDLE_TIMEOUT);
            });
        }

        // ポーリング開始
        setTimeout(checkForNewComments, pollingInterval);

        // ページ離脱時にポーリング停止
        $(window).on('beforeunload', function() {
            isPollingActive = false;
        });
    });
</script>

</body>

</html>