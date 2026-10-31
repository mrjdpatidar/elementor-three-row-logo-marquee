(function () {
  'use strict';
  const controllers = new WeakMap();
  function init(widget) {
    if (controllers.has(widget)) return;
    const rows = [...widget.querySelectorAll('.trlm__row')];
    if (!rows.length) return;
    const configs = [];
    let observer;
    let raf = 0;
    let phaseStart = performance.now();
    let pausedAt = null;
    let hovered = false;
    const mode = widget.classList.contains('trlm--slide') ? 'slide' : 'marquee';
    // Faster timing for existing Elementor widgets without re-saving their settings.
    const interval = Math.max(.5, (parseFloat(widget.dataset.syncInterval) || 2.5) * .72) * 1000;
    // Spend most of each step moving, rather than abruptly stopping halfway.
    const motion = Math.max(.82, Math.min(.95, parseFloat(widget.dataset.motionFraction) || .82));
    const ease = t => t * t * (3 - 2 * t);
    rows.forEach(row => {
      const track = row.querySelector('.trlm__track');
      const first = track && track.querySelector('.trlm__set');
      if (!first || !first.children.length) return;
      // Guard against duplicates saved in older widget data / rendered markup.
      const seen = new Set();
      const originals = [...first.children].filter(node => {
        const img = node.querySelector('img');
        if (!img) return false;
        const key = (img.getAttribute('src') || '').split('?')[0];
        if (seen.has(key)) return false;
        seen.add(key);
        return true;
      }).map(node => node.cloneNode(true));
      if (!originals.length) return;
      configs.push({row,track,first,originals,reverse:row.classList.contains('trlm__row--right'),pitch:1,count:1,distance:1,duration:30000});
    });
    if (!configs.length) return;
    controllers.set(widget, true);
    function rebuild() {
      configs.forEach(c => {
        c.first.replaceChildren(...c.originals.map(node => node.cloneNode(true)));
        // The group must contain a full ordered cycle. Never insert a
        // duplicate of a single logo next to itself when filling space.
        const target = c.row.clientWidth + 200;
        let cycles = 0;
        while (c.first.getBoundingClientRect().width < target && cycles < 40) {
          // Add complete ordered cycles, not isolated clones.
          c.originals.forEach(original => {
            const copy = original.cloneNode(true);
            copy.setAttribute('aria-hidden','true');
            copy.querySelectorAll('a').forEach(a => a.tabIndex = -1);
            c.first.appendChild(copy);
          });
          cycles++;
        }
        [...c.track.children].slice(1).forEach(node => node.remove());
        const duplicate = c.first.cloneNode(true);
        duplicate.setAttribute('aria-hidden','true');
        duplicate.querySelectorAll('a').forEach(a => a.tabIndex = -1);
        c.track.appendChild(duplicate);
        c.count = c.first.children.length;
        c.distance = c.first.getBoundingClientRect().width;
        c.pitch = c.distance / c.count;
        c.duration = Math.max(3000, parseFloat(getComputedStyle(c.row).getPropertyValue('--trlm-duration')) * 1000 || 30000);
        c.track.style.animation = 'none';
      });
    }
    function render(now) {
      const paused = widget.classList.contains('trlm--stopped') || (widget.classList.contains('trlm--pause') && hovered) || matchMedia('(prefers-reduced-motion: reduce)').matches;
      if (paused && pausedAt === null) pausedAt = now;
      if (!paused && pausedAt !== null) {phaseStart += now - pausedAt; pausedAt = null;}
      const elapsed = Math.max(0,(pausedAt === null ? now : pausedAt) - phaseStart);
      configs.forEach(c => {
        let x;
        if (mode === 'slide') {
          const steps = elapsed / interval;
          const index = Math.floor(steps);
          const fractional = steps - index;
          // Move during first 55% of interval, then hold: same phase for every row.
          const progress = ease(Math.min(1, fractional / motion));
          const distance = ((index % c.count) + progress) * c.pitch;
          x = c.reverse ? -c.distance + distance : -distance;
        } else {
          const distance = (elapsed % c.duration) / c.duration * c.distance;
          x = c.reverse ? -c.distance + distance : -distance;
        }
        c.track.style.transform = `translate3d(${x}px,0,0)`;
      });
      raf = requestAnimationFrame(render);
    }
    let resizeFrame = 0;
    function scheduleRebuild() {
      cancelAnimationFrame(resizeFrame);
      resizeFrame = requestAnimationFrame(rebuild);
    }
    widget.addEventListener('mouseenter', () => hovered = true);
    widget.addEventListener('mouseleave', () => hovered = false);
    rebuild();
    if (typeof ResizeObserver !== 'undefined') {
      observer = new ResizeObserver(scheduleRebuild);
      configs.forEach(c => observer.observe(c.row));
    } else window.addEventListener('resize', scheduleRebuild);
    window.addEventListener('load', scheduleRebuild, {once:true});
    raf = requestAnimationFrame(render);
  }
  function scan(root) {
    const el = root && root.querySelectorAll ? root : document;
    if (el.matches && el.matches('.trlm')) init(el);
    el.querySelectorAll('.trlm').forEach(init);
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', () => scan(document));
  else scan(document);
  if (window.elementorFrontend && window.elementorFrontend.hooks) {
    window.elementorFrontend.hooks.addAction('frontend/element_ready/trlm_logo_marquee.default', scope => scan(scope[0] || scope));
  }
})();
