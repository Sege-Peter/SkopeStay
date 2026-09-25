<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'SkopeStay Management'; ?></title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* CSS Variables for Light & Dark Mode */
    :root {
        --color-primary: 37 99 235;                /* Royal Blue */
        --color-primary-container: 219 234 254;    /* Light Blue */
        --color-on-primary: 255 255 255;           /* White */
        --color-on-primary-container: 30 58 138;   /* Deep Blue */
        --color-on-primary-fixed: 30 58 138;
        --color-on-primary-fixed-variant: 37 99 235;

        --color-secondary: 249 115 22;             /* Vibrant Orange */
        --color-secondary-container: 255 237 213;  /* Light Orange */
        --color-on-secondary: 255 255 255;         /* White */
        --color-on-secondary-container: 154 52 18; /* Deep Orange */
        --color-on-secondary-fixed: 154 52 18;
        --color-on-secondary-fixed-variant: 249 115 22;
        --color-secondary-fixed: 255 237 213;
        --color-secondary-fixed-dim: 253 186 116;

        --color-tertiary: 0 0 0;                   /* Pure Black */
        --color-tertiary-container: 243 244 246;   /* Light Gray */
        --color-on-tertiary: 255 255 255;          /* White */
        --color-on-tertiary-container: 0 0 0;      /* Black */
        --color-on-tertiary-fixed: 0 0 0;
        --color-on-tertiary-fixed-variant: 75 85 99;

        --color-background: 249 250 251;           /* Gray 50 */
        --color-on-background: 17 24 39;           /* Gray 900 */

        --color-surface: 255 255 255;              /* Pure White */
        --color-on-surface: 17 24 39;              /* Gray 900 */
        --color-on-surface-variant: 107 114 128;   /* Gray 500 */

        --color-surface-container-lowest: 255 255 255; /* White */
        --color-surface-container-low: 249 250 251;    /* Gray 50 */
        --color-surface-container: 243 244 246;        /* Gray 100 */
        --color-surface-container-high: 229 231 235;   /* Gray 200 */
        --color-surface-container-highest: 209 213 219;/* Gray 300 */

        --color-outline: 156 163 175;              /* Gray 400 */
        --color-outline-variant: 209 213 219;      /* Gray 300 */

        --color-error: 239 68 68;                  /* Red 500 */
        --color-error-container: 254 226 226;      /* Red 100 */
        --color-on-error: 255 255 255;
        --color-on-error-container: 153 27 27;     /* Red 900 */
        --color-success: 34 197 94;                /* Green 500 */
    }

    .dark {
        --color-primary: 59 130 246;               /* Blue 500 */
        --color-primary-container: 30 58 138;      /* Blue 900 */
        --color-on-primary: 255 255 255;
        --color-on-primary-container: 219 234 254; /* Blue 100 */
        --color-on-primary-fixed: 219 234 254;
        --color-on-primary-fixed-variant: 59 130 246;

        --color-secondary: 249 115 22;             /* Orange 500 */
        --color-secondary-container: 124 45 18;    /* Orange 900 */
        --color-on-secondary: 255 255 255;
        --color-on-secondary-container: 255 237 213; /* Orange 100 */
        --color-on-secondary-fixed: 255 237 213;
        --color-on-secondary-fixed-variant: 249 115 22;
        --color-secondary-fixed: 124 45 18;
        --color-secondary-fixed-dim: 194 65 12;

        --color-tertiary: 255 255 255;             /* White */
        --color-tertiary-container: 31 41 55;      /* Gray 800 */
        --color-on-tertiary: 0 0 0;                /* Black */
        --color-on-tertiary-container: 255 255 255;
        --color-on-tertiary-fixed: 255 255 255;
        --color-on-tertiary-fixed-variant: 156 163 175;

        --color-background: 3 7 18;                /* Gray 950 */
        --color-on-background: 249 250 251;        /* Gray 50 */

        --color-surface: 17 24 39;                 /* Gray 900 */
        --color-on-surface: 249 250 251;           /* Gray 50 */
        --color-on-surface-variant: 156 163 175;   /* Gray 400 */

        --color-surface-container-lowest: 3 7 18;  /* Gray 950 */
        --color-surface-container-low: 17 24 39;   /* Gray 900 */
        --color-surface-container: 31 41 55;       /* Gray 800 */
        --color-surface-container-high: 55 65 81;  /* Gray 700 */
        --color-surface-container-highest: 75 85 99; /* Gray 600 */

        --color-outline: 75 85 99;                 /* Gray 600 */
        --color-outline-variant: 55 65 81;         /* Gray 700 */

        --color-error: 248 113 113;                /* Red 400 */
        --color-error-container: 127 29 29;        /* Red 900 */
        --color-on-error: 255 255 255;
        --color-on-error-container: 254 226 226;   /* Red 100 */
        --color-success: 34 197 94;                /* Green 500 */
    }

    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        vertical-align: middle;
    }
    
    body {
        background-color: rgb(var(--color-surface-container-lowest));
        color: rgb(var(--color-on-surface));
        font-family: 'Montserrat', sans-serif;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    
    /* Unified scrollbars */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgb(var(--color-outline-variant)); border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: rgb(var(--color-outline)); }
    
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    
    /* Common UI components like glass cards */
    .glass-card {
        background: rgba(var(--color-surface), 0.85);
        backdrop-filter: blur(16px);
        border: 1px solid rgb(var(--color-outline-variant) / 0.5);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgb(var(--color-outline-variant)); border-radius: 10px; }
</style>

<script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          "colors": {
            "primary": "rgb(var(--color-primary) / <alpha-value>)",
            "primary-container": "rgb(var(--color-primary-container) / <alpha-value>)",
            "on-primary": "rgb(var(--color-on-primary) / <alpha-value>)",
            "on-primary-container": "rgb(var(--color-on-primary-container) / <alpha-value>)",
            "on-primary-fixed": "rgb(var(--color-on-primary-fixed) / <alpha-value>)",
            "on-primary-fixed-variant": "rgb(var(--color-on-primary-fixed-variant) / <alpha-value>)",
            
            "secondary": "rgb(var(--color-secondary) / <alpha-value>)",
            "secondary-container": "rgb(var(--color-secondary-container) / <alpha-value>)",
            "on-secondary": "rgb(var(--color-on-secondary) / <alpha-value>)",
            "on-secondary-container": "rgb(var(--color-on-secondary-container) / <alpha-value>)",
            "on-secondary-fixed": "rgb(var(--color-on-secondary-fixed) / <alpha-value>)",
            "on-secondary-fixed-variant": "rgb(var(--color-on-secondary-fixed-variant) / <alpha-value>)",
            "secondary-fixed": "rgb(var(--color-secondary-fixed) / <alpha-value>)",
            "secondary-fixed-dim": "rgb(var(--color-secondary-fixed-dim) / <alpha-value>)",
            
            "tertiary": "rgb(var(--color-tertiary) / <alpha-value>)",
            "tertiary-container": "rgb(var(--color-tertiary-container) / <alpha-value>)",
            "on-tertiary": "rgb(var(--color-on-tertiary) / <alpha-value>)",
            "on-tertiary-container": "rgb(var(--color-on-tertiary-container) / <alpha-value>)",
            "on-tertiary-fixed": "rgb(var(--color-on-tertiary-fixed) / <alpha-value>)",
            "on-tertiary-fixed-variant": "rgb(var(--color-on-tertiary-fixed-variant) / <alpha-value>)",
            
            "background": "rgb(var(--color-background) / <alpha-value>)",
            "on-background": "rgb(var(--color-on-background) / <alpha-value>)",
            
            "surface": "rgb(var(--color-surface) / <alpha-value>)",
            "on-surface": "rgb(var(--color-on-surface) / <alpha-value>)",
            "on-surface-variant": "rgb(var(--color-on-surface-variant) / <alpha-value>)",
            
            "surface-container-lowest": "rgb(var(--color-surface-container-lowest) / <alpha-value>)",
            "surface-container-low": "rgb(var(--color-surface-container-low) / <alpha-value>)",
            "surface-container": "rgb(var(--color-surface-container) / <alpha-value>)",
            "surface-container-high": "rgb(var(--color-surface-container-high) / <alpha-value>)",
            "surface-container-highest": "rgb(var(--color-surface-container-highest) / <alpha-value>)",
            
            "outline": "rgb(var(--color-outline) / <alpha-value>)",
            "outline-variant": "rgb(var(--color-outline-variant) / <alpha-value>)",
            
            "error": "rgb(var(--color-error) / <alpha-value>)",
            "error-container": "rgb(var(--color-error-container) / <alpha-value>)",
            "on-error": "rgb(var(--color-on-error) / <alpha-value>)",
            "on-error-container": "rgb(var(--color-on-error-container) / <alpha-value>)",
            "success": "rgb(var(--color-success) / <alpha-value>)"
          },
          "fontFamily": {
            "title-lg": ["Montserrat", "sans-serif"],
            "body-md": ["Montserrat", "sans-serif"],
            "body-lg": ["Montserrat", "sans-serif"],
            "caption": ["Montserrat", "sans-serif"],
            "display-lg": ["Montserrat", "sans-serif"],
            "headline-lg": ["Montserrat", "sans-serif"],
            "headline-md": ["Montserrat", "sans-serif"],
            "headline-lg-mobile": ["Montserrat", "sans-serif"],
            "label-md": ["Montserrat", "sans-serif"]
          }
        }
      }
    }
</script>
<script>
    // Prevent Flash of Unstyled Content for Dark Mode
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark')
    } else {
        document.documentElement.classList.remove('dark')
    }
</script>
</head>
