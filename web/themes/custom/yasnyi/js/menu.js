(function (Drupal, once) {
  Drupal.behaviors.yasnyiMenu = {
    attach(context) {
      once('yasnyi-menu', '.menu-toggle', context).forEach((button) => {
        const menu = document.getElementById('site-menu');
        button.addEventListener('click', () => {
          const open = menu.classList.toggle('is-open');
          button.setAttribute('aria-expanded', String(open));
        });
      });
    },
  };
})(Drupal, once);
