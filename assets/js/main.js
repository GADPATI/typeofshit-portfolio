document.addEventListener('DOMContentLoaded', () => {
  const revealEls = document.querySelectorAll('.reveal');
  revealEls.forEach((el, index) => {
    el.style.animationDelay = (index * 0.05) + 's';
  });

  const visualCards = document.querySelectorAll('.visual-card');
  const onScroll = () => {
    const scrollY = window.scrollY;
    visualCards.forEach((card, index) => {
      const offset = (scrollY * (0.05 + index * 0.02));
      card.style.transform = `translateY(${offset * -1}px)`;
    });
  };

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
});
