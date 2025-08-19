STRUTTURA DEL TEMA CHILD BOOTSCORE
=================================

La directory _dev contiene due sottocartelle principali che organizzano il codice di sviluppo:

1. DIRECTORY ASSETS
------------------
Contiene tutte le risorse statiche del tema:
- /media: Immagini, SVG e altri file multimediali
- /css: File di stile
- /js: Script JavaScript

2. DIRECTORY INC
---------------
Contiene la logica PHP del tema, organizzata come segue:

2.1 File Principale: logic.php
    - Funge da punto di ingresso principale
    - Gestisce l'inclusione di altri file di logica
    - Previene l'accesso diretto al file

2.2 Sottocartella woocommerce
    Contiene file specifici per la personalizzazione di WooCommerce:

    a) logic-single-prod.php
       - Gestisce la logica per le pagine dei prodotti singoli
       - Include funzionalità per:
         * Generazione JSON per varianti di pelle e colore
         * Visualizzazione delle dimensioni dei prodotti
         * Gestione degli attributi personalizzati

    b) Altri file previsti (non ancora implementati):
       - logic-categories.php
       - logic-checkout.php

STRUTTURA GENERALE
-----------------
_dev/
├── assets/
│   ├── media/
│   ├── css/
│   └── js/
└── inc/
    ├── logic.php
    └── logic/
        └── woocommerce/
            ├── logic-single-prod.php
            ├── logic-categories.php (commentato)
            └── logic-checkout.php (commentato)

VANTAGGI DELLA STRUTTURA
-----------------------
1. Separazione chiara tra risorse statiche e logica
2. Organizzazione modulare del codice
3. Facile manutenzione e aggiornamento
4. Possibilità di espandere le funzionalità in modo organizzato

Note: Questa struttura è stata progettata per ottimizzare la manutenibilità e la scalabilità del tema child, mantenendo una chiara separazione delle responsabilità tra i diversi componenti.