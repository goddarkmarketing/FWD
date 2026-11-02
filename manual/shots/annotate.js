/**
 * Place red boxes + bubbles from data-ann attributes on real elements.
 * data-ann="1|กดเมนูแคตตาล็อก|right"
 */
(function () {
  function place() {
    document.querySelectorAll('[data-ann]').forEach(function (el) {
      var parts = el.getAttribute('data-ann').split('|');
      var num = parts[0] || '';
      var text = parts[1] || '';
      var side = parts[2] || 'right';
      var rect = el.getBoundingClientRect();
      var root = document.querySelector('.shot-frame');
      if (!root) return;
      var rootRect = root.getBoundingClientRect();
      var top = rect.top - rootRect.top + root.scrollTop;
      var left = rect.left - rootRect.left + root.scrollLeft;

      var box = document.createElement('div');
      box.className = 'ann-box';
      box.style.left = (left - 4) + 'px';
      box.style.top = (top - 4) + 'px';
      box.style.width = (rect.width + 8) + 'px';
      box.style.height = (rect.height + 8) + 'px';
      root.appendChild(box);

      var bubble = document.createElement('div');
      bubble.className = 'ann-bubble ' + side;
      bubble.innerHTML = '<strong>' + num + '</strong> ' + text.replace(/\\n|\n/g, '<br>');

      var bLeft = left;
      var bTop = top;
      if (side === 'right') {
        bLeft = left + rect.width + 16;
        bTop = top + rect.height / 2 - 22;
      } else if (side === 'left') {
        bLeft = left - 230;
        bTop = top + rect.height / 2 - 22;
      } else if (side === 'down') {
        bLeft = left;
        bTop = top - 70;
      } else if (side === 'up') {
        bLeft = left;
        bTop = top + rect.height + 14;
      }
      bubble.style.left = Math.max(8, bLeft) + 'px';
      bubble.style.top = Math.max(8, bTop) + 'px';
      root.appendChild(bubble);
    });
  }

  if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(function () { setTimeout(place, 50); });
  } else {
    window.addEventListener('load', function () { setTimeout(place, 80); });
  }
})();
