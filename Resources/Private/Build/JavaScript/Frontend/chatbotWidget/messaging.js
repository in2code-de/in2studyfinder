import { marked } from 'marked';

marked.setOptions({
    sanitize: false, // Disable sanitization to preserve HTML structure
    gfm: true,
    breaks: true,
});

export class Messaging {
    constructor(chatbotWidget, ui) {
        this.chatbotWidget = chatbotWidget;
        this.ui = ui;
    }

    async sendMessage(message) {
        if (!message.trim()) return;

        this.addMessage(message);

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
            // Get topNResults setting if available
            const topNResults = this.chatbotWidget.getAttribute('data-chatbot-top-n') || 3;

            const response = await fetch(
              this.chatbotWidget.getAttribute('data-chatbot-url'),
              {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json',},
                    body: JSON.stringify({
                        message,
                        topNResults: parseInt(topNResults, 10)
                    })
                  }
            );

            const data = await response.json();

            // Remove typing indicator
            this.removeTypingIndicator(typingIndicator);

            if (data.success) {
                // Show message with typing effect
                this.addMessageWithTypingEffect(data.message);
            } else {
                this.addMessageWithTypingEffect('Entschuldigung, es gab einen Fehler.');
            }
        } catch (error) {
            this.removeTypingIndicator(typingIndicator);
            this.addMessageWithTypingEffect('Verbindungsfehler. Bitte versuchen Sie es später.');
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

    addMessage(text) {
        const messages = this.chatbotWidget.querySelector('[data-chatbot-messages]');
        const messageEl = document.createElement('div');
        messageEl.className = `message user`;
        messageEl.textContent = text;
        messages.appendChild(messageEl);
        messages.scrollTop = messages.scrollHeight;
    }

    async addMessageWithTypingEffect(text) {
        const messages = this.chatbotWidget.querySelector('[data-chatbot-messages]');
        const messageEl = document.createElement('div');
        messageEl.className = `message assistant`;
        messageEl.innerHTML = '';
        messages.appendChild(messageEl);

        // Parse the markdown to HTML
        const htmlContent = this.getMarkdownHTML(text);
        const speed = 30;
        let partIndex = 0;
        let charIndex = 0;
        let userHasScrolled = false;

        // Pre-parse the string into an array of text and HTML tags
        const parts = htmlContent.split(/(<[^>]+>)/).filter(Boolean);

        // This variable will hold the HTML that has been "typed" so far
        let progressHTML = '';

        // Track if user has scrolled up during typing
        const scrollHandler = () => {
            const isAtBottom = messages.scrollTop >= messages.scrollHeight - messages.clientHeight - 5;
            if (!isAtBottom) {
                userHasScrolled = true;
            }
        };
        messages.addEventListener('scroll', scrollHandler);

        function typeWriter() {
          messageEl.innerHTML = progressHTML;

          // Only auto-scroll if user hasn't manually scrolled up
          if (!userHasScrolled) {
              messages.scrollTop = messages.scrollHeight;
          }

          if (partIndex < parts.length) {
            const currentPart = parts[partIndex];

            // Check if the current part is an HTML tag
            if (currentPart.startsWith('<') && currentPart.endsWith('>')) {
              // If it's a tag, add it to our progress string instantly
              progressHTML += currentPart;
              partIndex++;
              // Process the next part immediately without a delay
              requestAnimationFrame(typeWriter);
            } else {
              // If it's plain text, type it out character by character
              if (charIndex < currentPart.length) {
                // Add the next character to our progress string
                progressHTML += currentPart.charAt(charIndex);
                charIndex++;
                setTimeout(typeWriter, speed);
              } else {
                // Move to the next part when the current text part is finished
                charIndex = 0;
                partIndex++;
                // Process the next part immediately
                requestAnimationFrame(typeWriter);
              }
            }
          } else {
            // Typing is complete, remove scroll listener
            messages.removeEventListener('scroll', scrollHandler);
          }
        }

        // Start the typing effect
        typeWriter();
    }

    getMarkdownHTML(text) {
      let htmlContent = marked.parse(text);
      if (typeof htmlContent !== 'string') {
        return text;
      }

      const regex = /\n(\s*\n)+(?=\s*([*+-]|\d+\.)\s)/g;
      return htmlContent.replace(regex, '\n');
    }
}
