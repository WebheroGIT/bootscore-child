<?php

// Assicurati che il file non sia accessibile direttamente
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Adds a floating chat button with contact information panel.
 *
 * This function creates a fixed button on the right side of the screen that,
 * when clicked, opens a panel with WhatsApp and phone contact information.
 */
function add_floating_chat_button() {
    ?>
    <style>
        /* NAMESPACE: .custom-chat-widget */
        .custom-chat-widget {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
        }

        .custom-chat-widget .chat-toggle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #25d366;
            color: #fff;
            font-size: 22px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
            cursor: pointer;
            transition: background-color 0.3s;
            animation: custom-pulse 2s infinite;
        }

        .custom-chat-widget .chat-toggle:hover {
            background-color: #128c7e;
        }

        @keyframes custom-pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .custom-chat-widget .chat-label {
            font-size: 11px;
            color: #fff;
            margin-top: 4px;
        }

        .custom-chat-widget .chat-panel {
            position: fixed;
            bottom: 90px;
            right: 20px;
            background: #fff;
            width: 260px;
            border-radius: 10px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.2);
            padding: 16px;
            display: none;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
        }

        .custom-chat-widget .chat-panel.show {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        .custom-chat-widget .chat-panel h3 {
            font-size: 18px;
            margin-bottom: 12px;
            color: #333;
        }

        .custom-chat-widget .contact-item {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .custom-chat-widget .contact-item i {
            margin-right: 8px;
            color: #25d366;
        }

        .custom-chat-widget .contact-item a {
            color: #0078d7;
            text-decoration: none;
        }

        .custom-chat-widget .contact-item a:hover {
            text-decoration: underline;
        }
    </style>

    <div class="custom-chat-widget">
        <div class="chat-toggle" id="chatToggle">
            <i class="fa-brands fa-whatsapp" id="chatIcon"></i>
            <span class="chat-label" id="chatLabel">Contattaci</span>
        </div>

        <div class="chat-panel" id="chatPanel">
            <h3>Contattaci</h3>
            <div class="contact-item">
                <a href="https://wa.me/numero_1"><i class="fa-brands fa-whatsapp"></i><strong>WhatsApp Latina</strong></a>
            </div>
            <div class="contact-item">
                <a href="https://wa.me/numero_2"><i class="fab fa-whatsapp-square"></i><strong>WhatsApp Milano</strong></a>
            </div>
            <div class="contact-item">
                <a href="tel:numero_3"><i class="fa fa-phone"></i><strong>Chiama Latina</strong></a>
            </div>
            <div class="contact-item">
                <a href="tel:numero_4"><i class="fa fa-phone"></i><strong>Chiama Milano</strong></a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chatToggle = document.getElementById('chatToggle');
            const chatPanel = document.getElementById('chatPanel');
            const chatIcon = document.getElementById('chatIcon');
            const chatLabel = document.getElementById('chatLabel');

            const icons = [
                { icon: 'fa-brands fa-whatsapp', text: 'Contattaci' },
                { icon: 'fa-phone', text: 'Chiama' },
                { icon: 'fa-envelope', text: 'Email' }
            ];

            let iconIndex = 0;
            let panelVisible = false;

            setInterval(() => {
                iconIndex = (iconIndex + 1) % icons.length;
                chatIcon.className = 'fa ' + icons[iconIndex].icon;
                chatLabel.textContent = icons[iconIndex].text;
            }, 3000);

            chatToggle.addEventListener('click', function (e) {
                e.stopPropagation();
                panelVisible = !panelVisible;
                chatPanel.classList.toggle('show', panelVisible);
            });

            document.addEventListener('click', function (e) {
                if (!chatToggle.contains(e.target) && !chatPanel.contains(e.target)) {
                    chatPanel.classList.remove('show');
                    panelVisible = false;
                }
            });
        });
    </script>
    <?php
}
add_action('wp_footer', 'add_floating_chat_button');