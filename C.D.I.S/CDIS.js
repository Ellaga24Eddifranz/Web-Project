// CDIS.script
document.addEventListener('DOMContentLoaded', function() {
  const buttons = document.querySelectorAll('.page-btn');
  const pages = {
    1: document.getElementById('page1'),
    2: document.getElementById('page2'),
    3: document.getElementById('page3'),
    4: document.getElementById('page4')
  };

  buttons.forEach(button => {
    button.addEventListener('click', function() {
      buttons.forEach(btn => btn.classList.remove('active'));
      this.classList.add('active');

      Object.values(pages).forEach(page => page.classList.remove('active-page'));

      const pageNum = parseInt(this.dataset.page);
      if (pages[pageNum]) {
        pages[pageNum].classList.add('active-page');
      }
    });
  });
});