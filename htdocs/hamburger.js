function toggleNav() {
    var body = document.body;
    var hamburger = document.getElementById('hamburger');
    var blackBg = document.getElementById('bg');
  
    hamburger.addEventListener('click', function() {
      body.classList.toggle('nav-open');
    });
    blackBg.addEventListener('click', function() {
      body.classList.remove('nav-open');
    });
  }
  toggleNav();