// UI Component for MistralChatbot
export class UI {
    constructor(chatbotWidget) {
        this.chatbotWidget = chatbotWidget;
        this.setupAutoResize();
    }

    hideDefaultPrompt() {
        const defaultPrompts = this.chatbotWidget.querySelector('[data-defaultprompts]');
        if (defaultPrompts) {
            defaultPrompts.classList.add('u-hide');
        }
    }

    setupAutoResize() {
        const input = this.chatbotWidget.querySelector('[data-chatbot-input]');
        if (!input) return;

        // Initial resize
        this.adjustTextareaHeight(input);

        // Resize on input
        input.addEventListener('input', () => {
            this.adjustTextareaHeight(input);
        });

        // Resize on focus
        input.addEventListener('focus', () => {
            this.adjustTextareaHeight(input);
        });
    }

    adjustTextareaHeight(textarea) {
        // Reset height to auto to get the correct scrollHeight
        textarea.style.height = 'auto';

        // Set the height to match content (scrollHeight)
        textarea.style.height = textarea.scrollHeight + 'px';
    }

    toggleChat() {
        const toggle = this.chatbotWidget.querySelector('[data-chatbot-toggle]');
        const body = this.chatbotWidget.querySelector('[data-chatbotwidget-body]')

        if (body.classList.contains('u-hide')) {
            body.classList.remove('u-hide');
            toggle.textContent = '−';
            return false;
        }

        body.classList.add('u-hide');
        toggle.textContent = '+';
        return true;
    }

    setupResizeHandler() {
        // Add resize event listener to handle window size changes
        window.addEventListener('resize', () => {
            // Check if we're on a mobile device after resize
            const isMobile = window.innerWidth <= 480;

            if (isMobile) {
                // Reset position for mobile devices to ensure visibility
                this.chatbotWidget.style.right = '20px';
                this.chatbotWidget.style.bottom = '20px';
                this.chatbotWidget.style.transform = 'none';
            }
        });
    }
}
