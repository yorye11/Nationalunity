 
 //------------------MODO OSCURO------------------------------------------------------------//
 const themeToggle = document.getElementById('theme-toggle');
        const body = document.body;
        const icon = themeToggle.querySelector('i');

        // Función para aplicar el tema
        function applyTheme(theme) {
            if (theme === 'dark') {
                body.classList.add('dark-mode');
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                body.classList.remove('dark-mode');
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        }

        // Cargar el tema guardado o el preferido por el sistema
        let currentTheme = localStorage.getItem('theme');
        if (!currentTheme) {
            // Si no hay tema guardado, usar la preferencia del sistema
            currentTheme = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        applyTheme(currentTheme);

        themeToggle.addEventListener('click', () => {
            let newTheme = body.classList.contains('dark-mode') ? 'light' : 'dark';
            applyTheme(newTheme);
            localStorage.setItem('theme', newTheme);
        });

        // Escuchar cambios en la preferencia del sistema
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
            const newColorScheme = event.matches ? "dark" : "light";
            applyTheme(newColorScheme);
            localStorage.setItem('theme', newColorScheme);
        });
  //------------------MODO OSCURO--------------------------------------------------------------------------//