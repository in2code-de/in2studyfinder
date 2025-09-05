import MistralChatbot from './chatbotWidget/index.js';

const chatbotWidgetElement = document.querySelector('[data-chatbot-container-widget]');
if (chatbotWidgetElement) {
    const chatbot = new MistralChatbot(chatbotWidgetElement, true);
    chatbot.init();
}

const chatbotElement = document.querySelector('[data-chatbot-container]');
if (chatbotElement) {
    const chatbot = new MistralChatbot(chatbotElement, false);
    chatbot.init();
}
