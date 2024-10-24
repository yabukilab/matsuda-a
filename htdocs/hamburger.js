function toggleNav() {
    // 要素の取得と変数への格納
    const body = document.body;
    const hamburger = document.getElementById('hamburger');
    const blackBg = document.getElementById('bg');
  
    hamburger.addEventListener('click', function() {
      body.classList.toggle('nav-open');
    });
    blackBg.addEventListener('click', function() {
      body.classList.remove('nav-open');
    });
  }
  toggleNav();