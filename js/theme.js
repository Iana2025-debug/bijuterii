// Theme toggle shared script
(function(){
  const KEY = 'elograTheme';
  function apply(mode){
    if(mode === 'dark') document.body.classList.add('dark'); else document.body.classList.remove('dark');
    // update all toggle buttons
    document.querySelectorAll('#themeToggle, .theme-toggle').forEach(btn=>{ btn.textContent = mode === 'dark' ? '☀️' : '🌙'; });
  }
  // read saved or prefered
  const saved = localStorage.getItem(KEY) || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  apply(saved);
  document.addEventListener('click', (e)=>{
    const btn = e.target.closest && e.target.closest('#themeToggle, .theme-toggle');
    if(!btn) return;
    const now = document.body.classList.contains('dark') ? 'light' : 'dark';
    localStorage.setItem(KEY, now);
    apply(now);
  });
})();
