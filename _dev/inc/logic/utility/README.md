# Video Modal Utility

Utility leggera per creare modali video responsive e accessibili nel tema BootScore Child.

## Utilizzo Base

Per attivare la modale video, aggiungi semplicemente la classe `modal-video` ai tuoi link video:

```html
<a href="https://video.unimarconi.it/dsms/istitutional/416/l7_ma1_rinaldi_l00.mp4" class="modal-video">Video lezione introduttiva 1</a>

<a class="btn btn-primary modal-video" href="https://video.unimarconi.it/dsms/istitutional/425/l9_mr_iazeolla_l00.mp4">Guarda il video</a>
```

## Caratteristiche

- ✅ **Leggera**: CSS e JS inline ottimizzati
- ✅ **Performance**: Caricamento condizionale degli asset
- ✅ **Responsive**: Si adatta a tutti i dispositivi
- ✅ **Accessibile**: Supporto completo per screen reader
- ✅ **Keyboard friendly**: Chiusura con ESC
- ✅ **Touch friendly**: Chiusura toccando fuori dalla modale

## Attivazione Automatica

La modale si attiva automaticamente quando:
1. Il contenuto della pagina contiene la classe `modal-video`
2. È presente lo shortcode `[modal-video]`

## Attivazione Manuale

Per pagine con contenuto dinamico, puoi forzare l'attivazione:

### In PHP:
```php
// Nel template o nelle funzioni
wh_enqueue_video_modal();
```

### Con Shortcode:
```
[modal-video]
```

### In JavaScript:
```javascript
// Reinizializza dopo contenuto dinamico
if (window.WHVideoModal) {
    WHVideoModal.init();
}
```

## Struttura File

```
_dev/inc/logic/utility/
├── video-modal.php     # Classe PHP principale
└── README.md          # Questa documentazione

_dev/assets/js/utility/
└── video-modal.js     # JavaScript separato (opzionale)
```

## Personalizzazione CSS

Per personalizzare l'aspetto della modale, sovrascrivi queste classi:

```css
.wh-video-modal {
    /* Overlay della modale */
}

.wh-video-modal-content {
    /* Contenitore del video */
}

.wh-video-modal-close {
    /* Pulsante di chiusura */
}

.wh-video-wrapper {
    /* Wrapper responsive del video */
}
```

## API JavaScript

```javascript
// Apri modale programmaticamente
WHVideoModal.open('url-del-video.mp4');

// Chiudi modale
WHVideoModal.close();

// Reinizializza (per contenuto dinamico)
WHVideoModal.init();
```

## Formati Video Supportati

- MP4 (raccomandato)
- WebM
- OGV

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+
- iOS Safari 12+
- Android Chrome 60+

## Performance

- **CSS**: ~400 bytes (minificato)
- **JS**: ~1.2KB (minificato)
- **Caricamento**: Solo quando necessario
- **Impatto**: Minimo sul Core Web Vitals

## Troubleshooting

### La modale non si apre
1. Verifica che il link abbia la classe `modal-video`
2. Controlla che l'URL del video sia valido
3. Assicurati che gli asset siano caricati

### Video non si riproduce
1. Verifica il formato del video
2. Controlla i CORS headers del server video
3. Testa l'URL direttamente nel browser

### Problemi di performance
1. La modale si carica solo quando necessario
2. Usa il formato MP4 per migliore compatibilità
3. Considera il preload="none" per video pesanti