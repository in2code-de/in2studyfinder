// Storage Component for MistralChatbot
export class Storage {
    savePosition(right, bottom) {
        // Save the position to localStorage
        localStorage.setItem('chatbot-position', JSON.stringify({
            right: right,
            bottom: bottom
        }));
    }

    loadPosition() {
        // Try to load the position from localStorage
        const positionStr = localStorage.getItem('chatbot-position');
        if (positionStr) {
            try {
                return JSON.parse(positionStr);
            } catch (e) {
                console.error('Error loading chatbot position:', e);
                return null;
            }
        }
        return null;
    }

    saveMinimizedState(isMinimized) {
        // Save the minimized state to localStorage
        localStorage.setItem('chatbot-minimized', isMinimized);
    }

    loadMinimizedState() {
        // Load minimized state
        return localStorage.getItem('chatbot-minimized') === 'true';
    }
}
