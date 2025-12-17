// Dark Mode Toggle Logic
(function() {
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    if (!darkModeToggle) return;

    const body = document.body;
    const label = document.querySelector('label[for="dark-mode-toggle"]');
    const icon = label ? label.querySelector('i') : null;
    const currentTheme = localStorage.getItem('theme');

    // Aplica el tema guardado al cargar la página
    if (currentTheme === 'dark') {
        body.classList.add('dark-mode');
        darkModeToggle.checked = true;
        if (icon) icon.classList.replace('fa-moon', 'fa-sun');
    } else {
        body.classList.remove('dark-mode');
        darkModeToggle.checked = false;
        if (icon) icon.classList.replace('fa-sun', 'fa-moon');
    }

    // Listener para el botón
    darkModeToggle.addEventListener('change', function() {
        body.classList.toggle('dark-mode');
        
        let theme = this.checked ? 'dark' : 'light';
        if (icon) icon.classList.toggle('fa-moon', theme === 'light');
        if (icon) icon.classList.toggle('fa-sun', theme === 'dark');
        localStorage.setItem('theme', theme);
    });
})();