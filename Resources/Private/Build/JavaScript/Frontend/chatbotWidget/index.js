// Main entry point for MistralChatbot
import { UI } from './ui.js';
import { Messaging } from './messaging.js';
import { Position } from './position.js';
import { Storage } from './storage.js';

class MistralChatbot {
    constructor(chatbotWidget, draggable) {
        this.chatbotWidget = chatbotWidget;
        this.draggable = draggable;
    }

    init() {
        // Initialize components
        this.storage = new Storage();
        this.ui = new UI(this.chatbotWidget);
        this.messaging = new Messaging(this.chatbotWidget, this.ui);

        // Setup components
        this.ui.setupResizeHandler();

        if (this.draggable) {
            this.position = new Position(this.chatbotWidget, this.storage);
            this.position.setupDraggable();
            this.position.loadPosition();
        }

        // Load minimized state
        const isMinimized = this.storage.loadMinimizedState();
        if (isMinimized) {
            this.chatbotWidget.classList.add('minimized');
            this.chatbotWidget.querySelector('[data-chatbot-toggle]').textContent = '+';
        }

        // Bind events
        this.bindEvents();
    }

    bindEvents() {
        const that = this;
        const sendBtn = this.chatbotWidget.querySelector('[data-chatbot-send]');
        const input = this.chatbotWidget.querySelector('[data-chatbot-input]');
        const toggle = this.chatbotWidget.querySelector('[data-chatbot-toggle]');
        const defaultPromptsContainer = this.chatbotWidget.querySelector('[data-defaultprompts]');

        // Initialize default prompts
        this.initDefaultPrompts(defaultPromptsContainer, input);

        sendBtn.addEventListener('click', () => {
            const message = input.value.trim();
            this.messaging.sendMessage(message);
        });

        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                const message = input.value.trim();
                this.messaging.sendMessage(message);
            }
        });

        toggle.addEventListener('click', () => {
            const isMinimized = this.ui.toggleChat();
            this.storage.saveMinimizedState(isMinimized);
        });
    }

    initDefaultPrompts(container, input) {
        if (!container) return;

        const that = this;
        const promptsText = container.getAttribute('data-prompts');

        if (!promptsText) return;

        // Clear existing content
        container.innerHTML = '';

        // Split by newlines and create prompt elements
        const prompts = promptsText.split('\n');

        prompts.forEach(prompt => {
            const trimmedPrompt = prompt.trim();
            if (trimmedPrompt) {
                const promptElement = document.createElement('div');
                promptElement.className = 'defaultprompt';
                promptElement.setAttribute('data-defaultprompt', '');
                promptElement.textContent = trimmedPrompt;

                // Add click event listener
                promptElement.addEventListener('click', (e) => {
                    that.ui.hideDefaultPrompt();
                    input.value = e.target.textContent.trim();
                    that.ui.adjustTextareaHeight(input);
                });

                container.appendChild(promptElement);
            }
        });
    }
}

// Initialize chatbot when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new MistralChatbot();
});

// Export the class for potential reuse
export default MistralChatbot;
