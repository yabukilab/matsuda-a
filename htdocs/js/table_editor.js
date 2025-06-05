$(document).ready(function() {
    // 行と列の数を更新
    function updateTable() {
        const rows = parseInt($('#rows').val()) || 1;
        const cols = parseInt($('#cols').val()) || 1;
        
        // ヘッダー行
        let html = '<thead><tr>';
        for (let i = 0; i < cols; i++) {
            html += `<th><input type="text" name="header[]" placeholder="ヘッダー ${i+1}"></th>`;
        }
        html += '</tr></thead><tbody>';
        
        // データ行
        for (let i = 0; i < rows; i++) {
            html += '<tr>';
            for (let j = 0; j < cols; j++) {
                html += `<td><input type="text" name="row_${i}[]" placeholder="データ ${i+1}-${j+1}"></td>`;
            }
            html += '</tr>';
        }
        html += '</tbody>';
        
        $('#table-preview').html(html);
    }
    
    // 初期テーブル作成
    updateTable();
    
    // 行数・列数変更時のイベント
    $('#rows, #cols').on('change', updateTable);
    
    // フォーム送信
    $('#table-form').on('submit', function(e) {
        e.preventDefault();
        
        const threadId = $('#thread_id').val();
        const tableTitle = $('#table-title').val();
        const tableComment = $('#table-comment').val();
        
        // テーブルデータを収集
        const tableData = {
            rows: parseInt($('#rows').val()),
            cols: parseInt($('#cols').val()),
            title: tableTitle,
            comment: tableComment,
            header: [],
            cells: []
        };
        
        // ヘッダーを収集
        $('input[name^="header"]').each(function() {
            tableData.header.push($(this).val());
        });
        
        // セルデータを収集
        for (let i = 0; i < tableData.rows; i++) {
            tableData.cells[i] = [];
            $(`input[name^="row_${i}"]`).each(function() {
                tableData.cells[i].push($(this).val());
            });
        }
        
        // サーバーにデータを送信
        $.ajax({
            url: 'insert_table.php',
            type: 'POST',
            data: {
                thread_id: threadId,
                table_data: JSON.stringify(tableData)
            },
            success: function(response) {
                window.location.href = `thread.php?id=${threadId}`;
            },
            error: function(xhr, status, error) {
                alert('エラーが発生しました: ' + error);
            }
        });
    });
});