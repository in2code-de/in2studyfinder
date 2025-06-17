// Messaging Component for MistralChatbot
export class Messaging {
    constructor(chatbotWidget, ui) {
        this.chatbotWidget = chatbotWidget;
        this.ui = ui;
    }

    async sendMessage(message) {
        if (!message.trim()) return;

        this.addMessage(message, 'user');

        // Reset textarea height after clearing
        const input = this.chatbotWidget.querySelector('[data-chatbot-input]');
        input.value = '';
        input.style.height = 'auto';
        input.style.height = input.scrollHeight + 'px';

        // Show typing indicator
        const typingIndicator = this.showTypingIndicator();

        // hide default prompt
        this.ui.hideDefaultPrompt();

        try {
            const response = await fetch(
              this.chatbotWidget.getAttribute('data-chatbot-url'),
              {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json',},
                    body: JSON.stringify({ message })
                  }
            );

            const data = await response.json();

            // Remove typing indicator
            this.removeTypingIndicator(typingIndicator);

            if (data.success) {
                // Show message with typing effect
                this.addMessageWithTypingEffect(data.message, 'assistant');
            } else {
                this.addMessageWithTypingEffect('Entschuldigung, es gab einen Fehler.', 'assistant');
            }
        } catch (error) {
            this.removeTypingIndicator(typingIndicator);
            this.addMessageWithTypingEffect('Verbindungsfehler. Bitte versuchen Sie es später.', 'assistant');
        }
    }

    showTypingIndicator() {
        const messages = this.chatbotWidget.querySelector('[data-chatbot-messages]');
        const typingEl = document.createElement('div');
        typingEl.className = 'message assistant typing-indicator';
        typingEl.innerHTML = `
            <div class="typing-dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        `;
        messages.appendChild(typingEl);
        messages.scrollTop = messages.scrollHeight;
        return typingEl;
    }

    removeTypingIndicator(typingIndicator) {
        if (typingIndicator && typingIndicator.parentNode) {
            typingIndicator.parentNode.removeChild(typingIndicator);
        }
    }

    addMessage(text, sender) {
        const messages = this.chatbotWidget.querySelector('[data-chatbot-messages]');
        const messageEl = document.createElement('div');
        messageEl.className = `message ${sender}`;
        messageEl.textContent = text;
        messages.appendChild(messageEl);
        messages.scrollTop = messages.scrollHeight;
    }

    async addMessageWithTypingEffect(text, sender) {
        const messages = this.chatbotWidget.querySelector('[data-chatbot-messages]');
        const messageEl = document.createElement('div');
        messageEl.className = `message ${sender}`;
        messages.appendChild(messageEl);

        // Simulate typing effect
        let i = 0;
        const typeSpeed = 30; // Milliseconds per character

        const typeWriter = () => {
            if (i < text.length) {
                let char = text.charAt(i);

                if (char === '\n') {
                    char = '<br>';
                }

                messageEl.innerHTML += char;
                i++;
                messages.scrollTop = messages.scrollHeight;
                setTimeout(typeWriter, typeSpeed);
            }
        };

        typeWriter();
    }
}
