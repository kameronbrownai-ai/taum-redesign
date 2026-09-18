(function () {
 var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
 var reveals = document.querySelectorAll('.reveal');
 if (reduced || !('IntersectionObserver' in window)) {
  reveals.forEach(function (el) { el.classList.add('in'); });
 } else {
  var io = new IntersectionObserver(function (entries) {
   entries.forEach(function (e) {
    if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
   });
  }, { threshold: 0.15 });
  reveals.forEach(function (el) { io.observe(el); });
 }

 var nums = document.querySelectorAll('.num[data-count]');
 if (!reduced && 'IntersectionObserver' in window) {
  var seen = new IntersectionObserver(function (entries) {
   entries.forEach(function (e) {
    if (!e.isIntersecting) return;
    seen.unobserve(e.target);
    var el = e.target;
    var target = parseInt(el.getAttribute('data-count'), 10);
    var suffix = el.getAttribute('data-suffix') || '';
    var start = null;
    function tick(ts) {
     if (!start) start = ts;
     var p = Math.min((ts - start) / 1400, 1);
     var eased = 1 - Math.pow(1 - p, 3);
     el.textContent = Math.round(target * eased).toLocaleString() + suffix;
     if (p < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
   });
  }, { threshold: 0.6 });
  nums.forEach(function (el) { seen.observe(el); });
 }
})();
