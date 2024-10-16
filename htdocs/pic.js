const zoom = document.querySelectorAll(".box-image");
const zoomback = document.getElementById("zoomback");
const zooming = document.getElementById("zooming");

// 各画像にクリックイベントを追加
zoom.forEach(function(value) {
    value.addEventListener("click", kakudai);
});

function kakudai(e) {
    //拡大領域を表示
    zoomback.style.display = "flex";
    //押された画像のリンクを渡す
    zooming.setAttribute("src", e.target.getAttribute("src"));
}

// 拡大画像表示領域をクリックしたときに閉じる
zoomback.addEventListener("click", modosu);

// onerrorのエラー処理
function handleImageError(img) {
    img.onerror = null; // onerror ハンドラーを解除して無限ループを防ぐ
    img.src = './items/placeholder.png'; // デフォルトの画像に切り替え
}

function modosu() {
    zoomback.style.display = "none";
}