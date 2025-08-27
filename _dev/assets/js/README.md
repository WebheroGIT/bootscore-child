# JavaScript Modules Structure

Questa directory contiene la struttura modulare del JavaScript per il tema Bootscore Child.

## Struttura delle Directory

```
js/
├── main.js                 # File principale che importa tutti i moduli
├── plugins/                # Moduli per funzionalità specifiche
│   └── platform-interceptor.js  # Intercetta i link ai corsi esterni
└── utility/                # Moduli di utilità
    ├── search.js           # Gestione della ricerca
    └── theme-settings.js   # Gestione delle impostazioni del tema
```

## Moduli Disponibili

### 1. Search Module (`utility/search.js`)
- Gestisce il toggle della barra di ricerca
- Esporta la classe `SearchToggle`

### 2. Platform Interceptor (`plugins/platform-interceptor.js`)
- Intercetta i click sui link che contengono `/externalplatformprogram/`
- Mostra un popup con i dettagli del corso
- Esporta la classe `PlatformInterceptor`

### 3. Theme Settings (`utility/theme-settings.js`)
- Gestisce le impostazioni del tema (dark mode, font size, etc.)
- Salva le preferenze nel localStorage
- Esporta la classe `ThemeSettings`

## Come Aggiungere Nuovi Moduli

1. Crea un nuovo file nella directory appropriata (`plugins/` o `utility/`)
2. Esporta le funzioni/classi necessarie usando ES6 modules:
   ```javascript
   export class MyModule {
       // implementazione
   }
   ```
3. Importa il modulo in `main.js`:
   ```javascript
   import { MyModule } from './path/to/module.js';
   ```
4. Inizializza il modulo nel DOMContentLoaded event

## Build Process

I moduli vengono compilati usando Webpack:
- `npm run build` - Compila i file per la produzione
- `npm run dev` - Compila i file per lo sviluppo
- `npm run watch` - Compila automaticamente quando i file cambiano

## File Compilati

I file compilati vengono generati nella directory `build/js/` con hash per il cache busting.

## Note per lo Sviluppo

- Usa sempre ES6 modules (import/export)
- Mantieni i moduli piccoli e focalizzati su una singola responsabilità
- Documenta le funzioni pubbliche
- Testa sempre il build dopo aver aggiunto nuovi moduli